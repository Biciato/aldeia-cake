<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;

/**
 * ConsultarRPS command.
 */
class ConsultarRpsCommand extends Command
{

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
      {
        $lotesRPSTable = TableRegistry::get('LotesRPS');
        $notasFiscaisTable = TableRegistry::get('NotasFiscais');
        $RPSTable = TableRegistry::get('RPS');
        $lotes = $lotesRPSTable->find('all', 
          [
            'conditions' =>
              [
                'LotesRPS.enviado' => true,
                'LotesRPS.respondido' => false
              ]
          ])->contain(['Unidades'])->toArray();
        if(count($lotes) > 0)
          {
            $io->info(count($lotes) . ' lotes encontrados para consulta');
            foreach($lotes as $lote)
              {
                $io->out('Consultando lote ' . $lote->numero_sequencial . ' - ' . $lote->Unidade->nome);
                if(empty($lote->conteudo_xml_consulta))
                  {
                    $io->out('Gerando XML de consulta para o lote ' . $lote->numero_sequencial  . ' - ' . $lote->Unidade->nome);
                    $conteudo = $this->conteudoXML(base64_decode($lote->resposta_envio));
                    $xml_consulta = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                          <soap:Body>
                            <ConsultarLoteRpsRequest xmlns="http://notacarioca.rio.gov.br/">
                                  <inputXML>' . 
                                    str_replace(['<', '>'], ['&lt;', '&gt;'],
                                      '
                                      <ConsultarLoteRpsEnvio xmlns="http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd">
                                        <Prestador>
                                            <Cnpj>' . preg_replace('/\D/', '', $lote->Unidade->cnpj) . '</Cnpj>
                                            <InscricaoMunicipal>' . $lote->Unidade->im . '</InscricaoMunicipal>
                                        </Prestador>
                                        <Protocolo>' . $conteudo['Protocolo'] . '</Protocolo>
                                    </ConsultarLoteRpsEnvio>
                                      '
                                ) . '
                                  </inputXML>
                            </ConsultarLoteRpsRequest>
                          </soap:Body>
                    </soap:Envelope>';
                    $lote->conteudo_xml_consulta = base64_encode($xml_consulta);
                    $lotesRPSTable->save($lote);
                  }
                else
                  {
                    $io->out('Lote  ' . $lote->numero_sequencial  . ' - ' . $lote->Unidade->nome . ' já possui XML de consulta');
                    $xml_consulta = base64_decode($lote->conteudo_xml_consulta);
                  }
                $resultado = $this->enviarXML($xml_consulta, 'http://notacarioca.rio.gov.br/ConsultarLoteRps', preg_replace('/\D/', '', $lote->Unidade->cnpj));
                $_nfs       = $this->conteudoXML($resultado);
                if(isset($_nfs['ListaNfse'])&&(isset($_nfs['ListaNfse']['CompNfse'])))
                  {
                    $notas = $_nfs['ListaNfse']['CompNfse'];
                    $io->success(count($notas) . ' notas retornadas!'); 
                    foreach($notas as $_nota)
                      {
                        $nota = $_nota['Nfse']['InfNfse'];
                        $numero_nota = $nota['Numero'];
                        $existente = $notasFiscaisTable->find('all', 
                          [
                            'conditions' => 
                              [
                                'numero_sequencial' => $numero_nota,
                                'unidade' => $lote->Unidade->id
                              ]
                          ])->first();
                        if(!is_null($existente))
                          {
                            $io->warning('Nota ' . $existente->numero_sequencial . ' - ' . $lote->Unidade->nome . ' - RPS ' . $existente->numero_rps . ' já cadastrada no banco');
                          }
                        else
                          {
                            $RPS = $RPSTable->find('all', 
                              [
                                'conditions' =>
                                  [
                                    'numero_sequencial' => $nota['IdentificacaoRps']['Numero'],
                                    'lote_id' => $lote->id
                                  ]
                              ])->first();
                            $nova_nota = $notasFiscaisTable->newEntity(
                              [
                                'numero_sequencial' => $numero_nota,
                                'numero_rps' => $RPS->numero_sequencial,
                                'rps_id' => $RPS->id,
                                'lote_rps_id' => $lote->id,
                                'unidade' => $lote->Unidade->id,
                                'aluno' => $RPS->aluno,
                                'dados_da_nota' => json_encode($nota)
                              ]);
                            if($notasFiscaisTable->save($nova_nota))
                              {
                                $io->success('Nota fiscal ' . $nova_nota->numero_sequencial . ' - ' . $lote->Unidade->id . ' - RPS ' . $nova_nota->numero_rps  . ' inserida com sucesso!');
                              }
                              else
                              {
                                $io->error('Erro ao salvar a nota ' . $numero_nota . ' - ' . $lote->Unidade->id . ' - RPS ' . $RPS->numero_sequencial  . ' inserida com sucesso!');
                              }
                          }
                      }
                  }
                else
                  {
                    $io->warning('Não foram retornadas notas fiscais para o lote ' . $lote->numero_sequencial  . ' - ' . $lote->Unidade->nome);
                  }
                $RPS_lote = $RPSTable->find('all',
                  [
                    'conditions' =>
                      [
                        'lote_id' => $lote->id
                      ]
                  ])->count();
                $notas_lote = $notasFiscaisTable->find('all',
                  [
                    'conditions' =>
                      [
                        'lote_rps_id' => $lote->id
                      ]
                  ])->count();
                if($RPS_lote == $notas_lote)
                  {
                    $lote->respondido = true;
                    $lote->resposta_consulta = base64_encode($resultado);
                    $lotesRPSTable->save($lote);
                    $io->success('Todas as notas para o lote ' . $lote->numero_sequencial . ' - ' . $lote->Unidade->nome . ' foram geradas!');
                  }
                else
                  {
                    $io->warning('Ainda existem . ' . ($RPS_lote - $notas_lote) . ' notas fiscais para serem geradas para o lote ' . $lote->numero_sequencial . ' - ' . $lote->Unidade->nome);
                  }
              }
          }
        else
          {
            $io->error('Não foram encontrados lotes para consultar');
          }
      }
    private function conteudoXML($string)
      {
        $start = '<outputXML>';
        $end   = '</outputXML>';
        $string = " ".$string;
        $ini = strpos($string,$start);
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        $conteudo = substr($string,$ini,$len);
        $obj = simplexml_load_string(str_replace(['&lt;', '&gt;'], ['<', '>'], $conteudo));
        $array = json_decode(json_encode($obj), true);
        return $array;
      }
    
    private function enviarXML($xml, $action, $cnpj)
      {
        $url = "https://homologacao.notacarioca.rio.gov.br/WSNacional/nfse.asmx";
        //$url = "https://notacarioca.rio.gov.br/WSNacional/nfse.asmx";
        $certificado = CONFIG ."certs/file" . $cnpj . ".withkey.pem";
        $senha = CONFIG ."certs/file" . $cnpj . ".key";
        $headers = 
        [
            "Content-type: text/xml; charset=\"utf-8\"",
            "Content-Length: " . strlen($xml),
            "SOAPAction: \"" . $action . "\""
        ];
    
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSLCERT, $certificado);
        curl_setopt($ch, CURLOPT_SSLKEY, $senha);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        $retorno = curl_exec($ch);
        
        curl_close($ch);
    
        return $retorno;
      }
}
