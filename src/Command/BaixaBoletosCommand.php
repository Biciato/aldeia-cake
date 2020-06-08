<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

class BaixaBoletosCommand extends Command
  {
  	private function formatarData($data, $padrao_br = false, $formatada = false)
  	  {
  	  	if(!$formatada)
  	  	  {
	  	    $data_pieces = 
	  	      [
	  	        'Y' => substr($data, 4, 4),
	  	        'm' => substr($data, 2, 2),
	  	        'd' => substr($data, 0, 2),
	  	      ];
  	  	  }
  	  	else
  	  	  {
  	  	  	$data_pieces = explode('-', $data);
			$data_pieces = 
			  [
			  	'Y' => $data_pieces[0],
			  	'm' => $data_pieces[1],
			  	'd' => $data_pieces[2]
			  ];  	  	  	
  	  	  }
  	    if(checkdate($data_pieces['m'], $data_pieces['d'], $data_pieces['Y']))
  	      {
  	        return (!$padrao_br) ? implode('-', $data_pieces) : implode('/', array_reverse($data_pieces));
  	      } 
  	    return false;
  	  }  
  	private function formatarGrana($grana)
  	  {
  	  	return number_format(((int)$grana/100), 2, ',', '.');
  	  }
    public function execute(Arguments $args, ConsoleIo $io)
      {
        $arquivos_retorno_path = 
              (object)[
                'novos' => WWW_ROOT . 'arquivos_retorno' . DS . 'novos',
                'lidos' => WWW_ROOT . 'arquivos_retorno' . DS . 'lidos'
              ];
        $unidadesTable = TableRegistry::get('Unidades');
        $registrosPagamentoTable = TableRegistry::get('RegistrosPagamento');
        $boletosTable = TableRegistry::get('Boletos');
        $file_suffix = 'MOV';
        $other_suffixes = 
          [
            'REL',
            'CON',
            'FRA'
          ];
        $io->out('Abrindo pasta de arquivos de remessa...');
        $arquivos = scandir($arquivos_retorno_path->novos);
        $io->out((count($arquivos) - 2) . " arquivo(s) encontrado(s)");
        foreach($arquivos as $arquivo)
          {
            if(in_array($arquivo, ['.', '..']))
              {
                continue;
              }
            $io->out('Processando arquivo <info>' . $arquivo . '</info>');
            $exp = explode("_", $arquivo);
            if(strtoupper($exp[(count($exp) - 1)]) == $file_suffix . ".TXT")
              {
                $conteudo     = file_get_contents($arquivos_retorno_path->novos . DS . $arquivo);
                $linhas       = explode("\n", $conteudo);
                $unidade      = null;
                foreach($linhas as $numero_linha => $linha)
                  {
                    if($numero_linha == 0)
                      {
                        $razao_social_unidade = substr($linha, 72, 30);
                        $unidade = $unidadesTable->find('all', ['conditions' => ['razao_social_arquivo_retorno' => $razao_social_unidade]])->first();
                        $io->out('Arquivo refenrente à unidade <info>' . $unidade->nome . '</info>');
                        continue;
                      }
                    else if($numero_linha == 1)
                      {
                        continue;
                      }
                    $segmento         = substr($linha, 13, 1);
                    $codigo_movimento = substr($linha, 15, 2);
                    if($segmento == 'T')
                      {
                        $segunda_linha    = $linhas[($numero_linha + 1)];
                        $segundo_segmento = substr($segunda_linha, 13, 1); 
                        if($segundo_segmento == 'U')
                          {
                            $nosso_numero    = substr($linha, 40, 12);
                            $valor_pago      = substr($segunda_linha, 77, 15);
                            $valor_liquido   = substr($segunda_linha, 92, 15);
                            $data_pagamento  = $this->formatarData(substr($segunda_linha, 137, 8));
                            $data_efetivacao = $this->formatarData(substr($segunda_linha, 145, 8));
                            $boleto = $boletosTable->find('all', 
                              [
                                'conditions' =>
                                  [
                                    'numero_interno' => ltrim($nosso_numero, '0'),
                                    'unidade_id'     => $unidade->id
                                  ]
                              ])->first();
                            $boleto_id = ($boleto) ? $boleto->id : '0';
                            $registro_pagamento = 
                              [
                                'nosso_numero'     => $nosso_numero,
                                'unidade_id'       => $unidade->id,
                                'boleto'           => $boleto_id,
                                'arquivo_nome'     => $arquivo,
                                'codigo_movimento' => $codigo_movimento,
                                'valor_pago'       => (int)$valor_pago,
                                'valor_liquido'    => (int)$valor_liquido,
                                'data_pagamento'   => $data_pagamento,
                                'data_recebimento' => $data_efetivacao
                              ];
                            $io->out('<success>Registro de pagamento gerado! (Nosso número: ' . $nosso_numero . ', valor pago: ' . $this->formatarGrana($valor_pago) . ', valor liquido: ' . $this->formatarGrana($valor_liquido) . ', data_pagamento: ' . $this->formatarData($data_pagamento, true, true) . ')</success>');
                            $registro = $registrosPagamentoTable->newEntity($registro_pagamento);
                            $existente = $registrosPagamentoTable->find('all', 
                              [
                                'conditions' => 
                                  [
                                    'nosso_numero' => $nosso_numero,
                                    'arquivo_nome'  => $arquivo
                                  ]
                              ])->count();
                            if($existente == 0)
                              {
                                $registrosPagamentoTable->save($registro);
                              	$io->out('<success>Registro inserido no banco com sucesso!</success>');
                              }
                            else
                              {
                              	$io->out('<warning>Registro já presente no banco de dados!</warning>');
                              }
                          }
                      }
                  } 
                
              }
            else
              {
              	$io->out('<warning>Esse arquivo não possui o sufixo ' . $file_suffix . '</warning>');
              }
            $io->out('Movendo ' . $arquivo . ' para a pasta de arquivos lidos');
            rename($arquivos_retorno_path->novos . DS . $arquivo, $arquivos_retorno_path->lidos . DS . $arquivo);
          }
      }
  }