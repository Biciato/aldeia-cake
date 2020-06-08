<?php 
namespace App\Controller;

use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;

class FinanceiroController extends AppController
  {
  	public function initialize(): void
      {
        parent::initialize();
        $action = $this->request->getParam('action');
        if((strtolower($action) == 'controlenotafiscal')||(strtolower($action) == 'sessaolistacobranca')||(strtolower($action) == 'sessaolistanotas'))
          {
            $this->viewBuilder()->setHelpers(['Grana']);
          }
       	$user = $this->Auth->user();
      }
    protected $tipos_remessa =
      [
        0 => 'Entrada de título',
        1 => 'Pedido de baixa',
        2 => 'Alteração de título'
      ];
    public function index()
      {
        $this->set('titulo', "Lista de prospects | Aldeia Montessori");
      }
    public function gerarBoletos()
      {
        $this->set('titulo', 'Gerar boletos | Aldeia Montessori');
      }
    public function boleto($id)
      {
        $this->viewBuilder()->disableAutoLayout();
        $boletosTable = TableRegistry::get('Boletos');
        $alunosTable  = TableRegistry::get('Alunos');
        $unidadesTable = TableRegistry::get('Unidades');
        $pessoasTable = TableRegistry::get('Pessoas');
        $boleto = $boletosTable->get($id);

        if ($boleto) 
          {
            $unidade = $unidadesTable->get($boleto->unidade_id);
            if (is_null($boleto['Boleto']['data_liquidacao'])) {

              $aluno = $alunosTable->find('all', 
                [
                  'conditions' =>
                    [
                      'Alunos.pessoa_id' => $boleto->pessoa_id
                    ]
                ])->contain(
                  [
                    'Parentes' =>
                      [
                        'Pessoas'
                      ],
                    'Pessoas' => 'Enderecos'
                  ])->first();
              if(count($aluno->responsavel->pessoa->enderecos) > 0)
                {
                  $endereco = $aluno->responsavel->pessoa->enderecos[0];
                }
              else
                {
                  $endereco = $aluno->pessoa->enderecos[0];
                }
              $valor_boleto                = $boleto->valor_sem_desconto;
              $cobrado                     = $valor_boleto;
              $desconto_concedido_exibicao = 0;
              $multa                       = 0;
              $instrucoes_valor            = '';

              if (strtotime($boleto->data_vencimento->format('Y-m-d')) >= strtotime(date('Y-m-d'))) {
                if ((strtotime($boleto->data_processamento) < strtotime('2019-09-24'))) 
                  {
                  $desconto_concedido_exibicao = $boleto->valor_desconto;
                  $valor_boleto                = $boleto->valor_com_desconto;

                  $instrucoes_valor            = 'Desconto de 10% para pagamento até ' . date('d/m/Y', strtotime($boleto->data_vencimento)) . ' de acordo o Edital de Matrícula.';
                }


                $cobrado = $valor_boleto;
              } else {
                $di = 0;
                $d1 = strtotime($boleto->data_vencimento);
                $d2 = strtotime(date('Y-m-d'));

                while ($d1 < $d2) 
                  {
                    $di += 1;
                    $d1 = strtotime('+ 1 day', $d1);
                  }

                $dias_atraso = $di;

                $mora            = round(($valor_boleto * 2 / 100));

                $boleto->data_vencimento = date('Y-m-d');
                $instrucoes_valor = 'JUROS E MORA CALCULADOS PARA PAGAMENTO EM ' . date('d/m/Y') . '.';

                $multa        = (($boleto->juros_reais * $boleto->dias_atrasados) + $mora);
                $valor_boleto = $boleto->valor_atualizado + $mora;
                $cobrado      = $valor_boleto;
              }

              $this->set('boleto', $boleto);
              $this->set('unidade', $unidade);
              $this->set('aluno', $aluno);
              $this->set('endereco', $endereco);
              $this->set('responsavel', $aluno->responsavel);
              $this->set('matriculado_atual', true);
              $this->set('dataLimiteDesconto', '2019-09-24');
              $this->set('cobrado', $cobrado);
              $this->set('instrucoes_valor', $instrucoes_valor);
              $this->set('multa', $multa);
              $this->set('desconto_concedido_exibicao', $desconto_concedido_exibicao);
              $this->set('reais_juros_dia', $boleto->juros_reais);
              $this->set('valor_boleto', $valor_boleto);
        } 
      }
    }
    public function baixarBoletoIndividual()
      {
        $this->set('titulo', 'Baixar boleto individualmente | Aldeia Montessori');
      }
    public function baixaBoleto()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $boletosTable = TableRegistry::get('Boletos');

            if(isset($data['codigo']))
              {
                $numero = trim(preg_replace('/\D/', '', $data['codigo']));
                if(in_array(strlen($numero), [47, 44]))
                  {
                    if(strlen($numero) == 47)
                      {
                        $nosso_numero = substr($numero, 14, 6) . substr($numero, 21, 5);
                        $convenio     = substr($numero, 5, 4) . substr($numero, 10, 3);
                      }
                    elseif(strlen($numero) == 44)
                      {
                        $nosso_numero = substr($numero, 27, 12);
                        $convenio     = substr($numero, 20, 7);
                      }
                    $nosso_numero = (int)trim(preg_replace('/\D/', '', $nosso_numero));
                    $convenio     = (int)trim(preg_replace('/\D/', '', $convenio));
                    $boleto_baixa = $boletosTable->find('all', 
                      [
                        'conditions' =>
                          [
                            'Boletos.numero_interno' => $nosso_numero,
                            'OR' => 
                              [
                                'Unidades.convenio' => $convenio,
                                'Unidades.codigo_beneficiario' => $convenio
                              ]
                          ]
                      ])->contain(['Unidades', 'Pessoas'])->first();
                    if($boleto_baixa)
                      {
                        if($boleto_baixa->data_liquidacao)
                          {
                            $this->response = $this->response->withStringBody(json_encode(
                              [
                                'success' => false,
                                'error'   => 'Esse boleto já foi baixado anteriormente.'
                              ]
                            ));
                            return $this->response;
                          }
                        else
                          {
                            $this->viewBuilder()->disableAutoLayout();
                            $this->viewBuilder()->setTemplate('confirmar_baixa_individual');
                            $this->set('codigo', trim($data['codigo']));
                            $this->set('boleto', $boleto_baixa);
                            $this->set('usuario', $this->Auth->user());
                          }
                      }
                    else
                      {
                        $this->response = $this->response->withStringBody(json_encode(
                          [
                            'success' => false,
                            'error'   => 'Boleto não encontado.'
                          ]
                        ));
                        return $this->response;
                      }
                  }
                else
                  {
                    $this->response = $this->response->withStringBody(json_encode(
                      [
                        'success' => false,
                        'error'   => 'Quantidade de caracteres numéricos não confere com código de barras ou linha digitável.'
                      ]
                    ));
                    return $this->response;
                  }
              }
            else if(isset($data['confirmar_baixa']))
              {
                $boleto = $boletosTable->get($data['id']);
                $boleto->baixado_manualmente    = true;
                $boleto->valor_pago_sacado      = str_replace(['.', ',', ' '], '', $data['valor']);
                $boleto->valor_liquido_recebido = str_replace(['.', ',', ' '], '', $data['valor']);
                $boleto->data_liquidacao        = implode('-', array_reverse(explode('/', $data['data_liquidacao'])));
                $error = "";
                if($boletosTable->save($boleto))
                  {
                    $success = true;
                  }
                else
                  {
                    $success = false;
                    $error = "Erro ao dair baixa no boleto!";
                  }
                $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'error' => $error]));
                return $this->response;
              }
          }

      }

    public function processarBoletos()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $erro = false;
            $unidadesTable    = TableRegistry::get('Unidades');
            $boletosTable     = TableRegistry::get('Boletos');
            $alunosTable      = TableRegistry::get('Alunos');
            $servicosTable    = TableRegistry::get('Servicos');
            $valoresTable     = TableRegistry::get('Valores');
            if($data['tipo'] === 'cota-mensal')
              {
                 $data_considerada = date('Y-m-d', strtotime($data['ano'] . '-' . $data['mes'] . '-01'));
                 $unidades = $unidadesTable->find('all', 
                  [
                    'conditions' =>
                      [
                        'ativo' => true
                      ]
                  ])->toArray();
                 $boletos = [];
                 $gerados = $boletosTable->find('all', [
                    'conditions' => 
                      [
                        'mes_referencia' => $data['mes'],
                        'ano_referencia' => $data['ano'],
                        'tipo_boleto' => 0
                      ] 
                  ])->count();
                 if($gerados == 0)
                   {
                    foreach($unidades as $unidade)
                      {
                         $alunos = $alunosTable->find('all', 
                           [
                             'conditions' =>
                               [
                                 'ano_letivo' => $data['ano'],
                                 'data_inicio >=' => $data_considerada,
                                 'unidade' => $unidade->id
                               ]
                           ])->toArray();
                         foreach($alunos as $aluno)
                           {
                             $nosso_numero   = $boletosTable->find('nossoNumero')->first(); 
                             $nosso_numero   = (!$ultimo) ? rand(1000000, 9999999) : ((int)$ultimo->numero_interno + 1);
                             $valor_total    = 0;
                             $valor_desconto = 0;
                             $vencimento     = ($aluno->dia_vencimento) ? date('Y-m-' . $aluno->dia_vencimento, strtotime($data_considerada)) : date('Y-m-02', strtotime($data_considerada));
                             $servicos = $servicosTable->find('all' , 
                               [
                                 'conditions' => 
                                   [
                                     'id IN(' . implode(', ', $aluno->servicos_array) . ')'
                                   ]
                               ])->toArray();
                             foreach($servicos as $servico)
                               {
                                $valor = $valoresTable->find('all', 
                                    [
                                      'conditions' =>
                                        [
                                          'servico' => $servico->id,
                                          'data_inicio <= "' . $data_considerada . '"',
                                          'data_final >= "' . $data_considerada . '"' 
                                        ]
                                    ])->first();
                                 if($valor)
                                   {
                                     if($aluno->financeiro_array[$servico->id])
                                       {
                                         $_valor_desconto = round((((int)$valor->valor*(int)$aluno->financeiro_array[$servico->id])/100));
                                         $valor_desconto += $_valor_desconto;
                                       }
                                     $valor_total += (int)$valor->valor;
                                   }
                               }
                             $boleto = $boletosTable->newEntity(
                               [
                                 'pessoa_id' => $aluno->pessoa_id,
                                 'unidade_id' => $unidade->id,
                                 'numero_documento' => $nosso_numero,
                                 'numero_interno' => $nosso_numero,
                                 'data_processamento' => date('Y-m-d'),
                                 'data_vencimento' => $vencimento,
                                 'valor_com_desconto' => ($valor_total - $valor_desconto),
                                 'valor_sem_desconto' => $valor_total,
                                 'valor_desconto' => $valor_desconto,
                                 'motivo_boleto' => "Cota mensal de anuidade escolar (" . $data['mes'] . "/" . $data['ano'] . ")",
                                 'tipo_boleto' => 0,
                                 'ano_referencia' => $data['ano'],
                                 'mes_referencia' => $data['mes'],
                               ]); 
                             $boletos[] = $boleto;
                             if(!$boletosTable->save($boleto))
                               {
                                 $erro = "Erro ao inserir o boleto!";
                                 break;
                               }
                           }
                      }
                     if(count($boletos) < 1)
                       {
                         $erro = "Não foram encontrados alunos matriculados nesse período";
                       }

                   }
                  else
                    {
                      $erro = "Cota mensal de anuidade escolar já gerada para esse mês/ano";
                    }
                  $resposta = 
                    [
                      'success' => !((bool)$erro),
                      'mensagem' => $erro,
                      'quantidade' => count($boletos)
                    ];
              }
            elseif($data['tipo'] === 'cota-composicao')
              {
                $aluno = $alunosTable->get($data['aluno_id']);
                $nosso_numero = $boletosTable->find('nossoNumero');
                $boleto = $boletosTable->newEntity(
                  [
                    'pessoa_id' => $aluno->pessoa_id,
                    'data_processamento' => date('Y-m-d'),
                    'unidade_id' => $aluno->unidade,
                    'numero_documento' => $nosso_numero,
                    'numero_interno' => $nosso_numero,
                    'data_vencimento' => implode('-', array_reverse(explode('/', $data['vencimento']))),
                    'valor_com_desconto' => str_replace(['.', ','], '', $data['valor']),
                    'valor_sem_desconto' => str_replace(['.', ','], '', $data['valor']),
                    'valor_desconto' => 0,
                    'motivo_boleto' => $data['motivo'],
                    'tipo_boleto' => 1,
                    'ano_referencia' => date('Y'),
                    'mes_referencia' => date('m'),
                  ]); 
                if(!$boletosTable->save($boleto))
                  {
                    $erro = 'Erro ao gerar o boleto!';
                  }
                $resposta = 
                  [
                    'success' => !((bool)$erro),
                    'mensagem' => $erro,
                  ];
              }

            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode($resposta));
            return $this->response;
          }
      }
    public function arquivosDeRemessa()
      {
        $unidadesTable = TableRegistry::get('Unidades');
        $this->set('unidades', $unidadesTable->find('all', ['conditions' => ['ativo' => true]])->toArray());
        $this->set('titulo', 'Arquivos de remessa | Aldeia Montessori'); 
      }
    public function verificarArquivosRemessa()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $unidadesTable        = TableRegistry::get('Unidades');
            $boletosTable         = TableRegistry::get('Boletos');
            $remessaTable         = TableRegistry::get('Remessas');
            $detalhesRemessaTable = TableRegistry::get('DetalhesRemessa');
            //Entradas de título
            $remessas_entrada = $detalhesRemessaTable->find('list', 
              [
                'keyField' => 'id',
                'valueField' => 'boleto_id',
                'conditions' =>
                  [
                    'tipo' => 0, //Tipo de detalhe de entrada de título
                    'unidade_id' => $data['unidade'],
                  ]
              ])->toArray();
            $entrada_conds    = (count($remessas_entrada)) ? 'Boletos.id NOT IN(' . implode(', ', $remessas_entrada) . ')' : 'Boletos.id IS NOT NULL';
            $boletos_entrada  = $boletosTable->find('all', 
              [
              'conditions' =>
                [
                  'data_liquidacao IS NULL',
                  'excluido IS NULL',
                  'valor_com_desconto > 0',
                  'unidade_id' => $data['unidade'],
                  $entrada_conds
                ]
              ])->contain(['Pessoas'])->toArray();
                //Pedidos de baixa
              $remessas_baixa = $detalhesRemessaTable->find('list', 
                [
                  'keyField' => 'id',
                  'valueField' => 'boleto_id',
                  'conditions' =>
                  [
                    'tipo' => 1, //Tipo de detalhe de pedido de baixa
                    'unidade_id' => $data['unidade'],
                  ]
                ])->toArray();
            $baixa_conds = (count($remessas_baixa)) ? 'Boletos.id NOT IN(' . implode(', ', $remessas_baixa) . ')' : 'Boletos.id IS NOT NULL';
            $boletos_baixa = $boletosTable->find('all', 
              [
                'conditions' =>
                  [
                    'data_liquidacao IS NOT NULL',
                    'baixado_manualmente' => TRUE,
                    'unidade_id' => $data['unidade'],
                    $baixa_conds,
                  ]
              ])->contain(['Pessoas'])->toArray();
            $this->set(compact('boletos_entrada', 'boletos_baixa'));
            $this->set('tipos', $this->tipos_boleto);
            $this->set('unique', uniqid());
            $this->set('unidade', $data['unidade']);
          }
      }

    public function gerarArquivoRemessa()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $unique = array_keys($data)[0];
            $data = $data[$unique];
            $this->response = $this->response->withType('application/json');
            if((isset($data['entrada']))||(isset($data['baixa'])))
              {
                $remessasTable        = TableRegistry::get('Remessas');
                $detalhesRemessaTable = TableRegistry::get('DetalhesRemessa');
                $unidadesTable        = TableRegistry::get('Unidades');
                $unidade = $unidadesTable->get($data['unidade']);

                $ultima = $remessasTable->find('all', 
                  [
                    'conditions' =>
                      [
                        'unidade_id' => $data['unidade']
                      ],
                    'order' => ['numero_sequencial DESC'],
                    'limit' => 1,
                    'fields' => ['numero_sequencial']
                  ])->first();
                $numero_sequencial = ($ultima) ? ((int)$ultima->numero_sequencial + 1) : 1;
                $remessa = $remessasTable->newEntity(
                  [
                    'unidade_id' => $data['unidade'],
                    'numero_sequencial' => $numero_sequencial
                  ]);
                $remessasTable->save($remessa);
                $detalhesRemessa = [];
                //O header contém os dados da unidade
                $this->gerarHeaderArquivo($unidade, $numero_sequencial);
                //O header do lote contém os dados desse lote de boleto
                $this->gerarHeaderLote($unidade, $numero_sequencial);
                if(@$data['entrada'])
                  {
                    foreach($data['entrada'] as $boleto_id)
                      {
                        $ultimo_detalhe = $detalhesRemessaTable->find('all', 
                          [
                              'conditions' =>
                                [
                                    'remessa_id' => $remessa->id
                                ],
                              'order' => 
                                [
                                    'numero_sequencial DESC'
                                ]
                          ])->first();
                        if(!$ultimo_detalhe) 
                          {
                            $numero = 1;
                          }
                        else
                          {
                              $numero = ((int)$ultimo_detalhe->numero_sequencial + 1);
                          }
                        $detalhe = $detalhesRemessaTable->newEntity(
                          [
                            'boleto_id' => $boleto_id,
                            'unidade_id' => $data['unidade'],
                            'remessa_id' => $remessa->id,
                            'numero_sequencial' => $numero,
                            'tipo'       => 0
                            ]
                          );
                          $detalhesRemessaTable->save($detalhe);
                          $this->gerarDetalheArquivoEntrada($unidade, $detalhe);
                          $detalhesRemessa[] = $detalhe;
                        }
                      }
                      if(@$data['baixa'])
                      {
                        foreach($data['baixa'] as $boleto_id)
                        {
                          $ultimo_detalhe = $detalhesRemessaTable->find('all', 
                          [
                            'conditions' =>
                            [
                              'remessa_id' => $remessa->id
                                ],
                                'order' => 
                                [
                                  'numero_sequencial DESC'
                                ]
                            ])->first();
                          if(!$ultimo_detalhe) 
                            {
                              $numero = 1;
                            }
                          else
                            {
                              $numero = ((int)$ultimo_detalhe->numero_sequencial + 1);
                            }
                          $detalhe = $detalhesRemessaTable->newEntity(
                            [
                              'boleto_id' => $boleto_id,
                              'unidade_id' => $data['unidade'],
                              'remessa_id' => $remessa->id,
                              'numero_sequencial' => $numero,
                              'tipo'       => 1
                            ]
                          );
                          $detalhesRemessaTable->save($detalhe);
                          $this->gerarDetalheArquivoBaixa($unidade, $detalhe);
                          $detalhesRemessa[] = $detalhe;
                      }
                  }
                $this->gerarTrailerLote($remessa->numero_sequencial);
                $this->gerarTrailerArquivo('1');
                $remessa->conteudo_arquivo   = $this->conteudo_arquivo;
                $remessa->quantidade_boletos = count($detalhesRemessa);
                $arquivo_nome         = $unidade->sigla_empresa . "_" . date('Y_m_d') . "_" . $remessa->id . ".rem";
                $remessasTable->save($remessa); 
                $this->response = $this->response->withStringBody(json_encode(['success' => true, 'conteudo_arquivo' => base64_encode($this->conteudo_arquivo), 'nome_arquivo' => $arquivo_nome]));
              } 
             else
               {
                 $this->response = $this->response->withStringBody(json_encode(['success' => true]));
               }
            return $this->response;
          }
      }
      private $conteudo_arquivo = '';
      private function gerarHeaderArquivo($unidade, $numero_sequencial)
        {
          $conteudo_linha = '03300000        2' . str_pad(preg_replace('/\D/', '', $unidade->cnpj), 15, '0', STR_PAD_LEFT) . str_pad($unidade->codigo_transmissao, 15, '0', STR_PAD_LEFT) . '                         ' . str_pad($unidade->razao_social_arquivo_remessa, 30) . str_pad('BANCO SANTANDER', 30) . '          1' . date('dmY') . '      ' . str_pad($numero_sequencial, 6, '0', STR_PAD_LEFT) . '040                                                                          ';
          $this->conteudo_arquivo .= $conteudo_linha . "\r\n";            
        }
        /*
      private function header_arquivo($raw, $numero_inscricao = 0, $codigo_transmissao = 0, $nome_empresa = '', $numero_sequencial = 0)
        {
          $dados = array(
            ALDEIA NUCLEO DE EDUCACAO MONT
            '033', 
             '0000', 
             '0', 
             str_pad('', 8),  
             '2', 
             str_pad($numero_inscricao, 15, '0', STR_PAD_LEFT), 
             str_pad($codigo_transmissao, 15, '0', STR_PAD_LEFT), 
             str_pad('', 25), 
             str_pad($nome_empresa, 30), 
             str_pad('BANCO SANTANDER', 30), 
             str_pad('', 10), 
             '1', 
             date('dmY'), 
             str_pad('', 6), 
             str_pad($numero_sequencial, 6, '0', STR_PAD_LEFT), 
             '040', 
             str_pad('', 74) 
          );


          return $this->imprimir($raw, $dados);
        } //ok*/
      /* LOTE */
  private function gerarHeaderLote($unidade, $numero_sequencial)
    {
      $conteudo_linha = '033' . str_pad('1', 4, '0', STR_PAD_LEFT) . '1R01  030 2' . str_pad(preg_replace('/\D/', '', $unidade->cnpj), 15, '0', STR_PAD_LEFT) . '                    ' . str_pad($unidade->codigo_transmissao, 15, '0', STR_PAD_LEFT) . '     ' . str_pad($unidade->razao_social_arquivo_remessa, 30) . '                                                                                ' . str_pad($numero_sequencial, 8, '0', STR_PAD_LEFT) . date('dmY') . '                                         ';
      $this->conteudo_arquivo .= $conteudo_linha . "\r\n";
    }
    /*
	private function header_lote($raw, $numero_lote_arquivo = 0, $numero_inscricao = 0, $codigo_transmissao = 0, $nome_empresa = '', $numero_remessa = 0)
	{
		$dados = array(

			'033', 
			 str_pad($numero_lote_arquivo, 4, '0', STR_PAD_LEFT), 
			 '1', 
			 'R', 
			 '01', 
			 str_pad('', 2), 
			 '030', 
			 ' ', 
			 '2', 
			 str_pad($numero_inscricao, 15, '0', STR_PAD_LEFT), 
			 str_pad('', 20), 
			 str_pad($codigo_transmissao, 15, '0', STR_PAD_LEFT), 
			 str_pad('', 5), 
			 str_pad($nome_empresa, 30), 
			 str_pad('', 40), 
			 str_pad('', 40), 
			 str_pad($numero_remessa, 8, '0', STR_PAD_LEFT), 
			 date('dmY'), 
			 str_pad('', 41) 
		);

		return $this->imprimir($raw, $dados);
  } //ok*/
  /*
    $this->ArquivoRemessa->LoteRemessa->DetalheRemessa->salvar_detalhe_p(
						$lote_remessa['LoteRemessa']['id'],
						$lote_remessa['LoteRemessa']['numero_lote_arquivo'],
						$empresa['Empresa']['conta_corrente_registro'],
						$empresa['Empresa']['digito_verificador_conta_corrente_registro'],
						$boleto['Boleto']['nosso_numero'] . $this->modulo_11($this->formata_numero($boleto['Boleto']['nosso_numero'], 7, 0), 9, 0),
						$boleto['Boleto']['numero_documento'],
						$boleto['Boleto']['data_vencimento'],
						$boleto['Boleto']['valor_sem_desconto'],
						$empresa['Empresa']['agencia_registro'],
						$empresa['Empresa']['digito_verificador_agencia_registro'],
						$boleto['Boleto']['data_processamento'],
						$valor_juros_dia,
						$desconto_reais,
						$boleto['Boleto']['id'],
						$boleto_tipos['tipo']
					); //ok
  public function salvar_detalhe_p($lote_remessa_id, $numero_lote_remessa, $numero_conta_corrente, $digito_verificador, $nosso_numero, $numero_documento, $data_vencimento, $valor_nominal, $agencia, $digito_agencia, $data_emissao, $valor_juros, $desconto_reais, $boleto_id, $codigo_movimento_remessa)
		{
			$this->create(FALSE);

			$this->save(array
			(
				'lote_remessa_id'         => $lote_remessa_id,
				'tipo'                    => 'P',
				'numero_lote_remessa'     => $numero_lote_remessa,
				'numero_registro_no_lote' => $this->getNumeroSequencial($lote_remessa_id),
				'numero_conta_corrente'   => $numero_conta_corrente,
				'digito_verificador'      => $digito_verificador,
				'nosso_numero'            => $nosso_numero,
				'numero_documento'        => $numero_documento,
				'data_vencimento'         => $data_vencimento,
				'valor_nominal'           => $valor_nominal,
				'agencia'                 => $agencia,
				'digito_agencia'          => $digito_agencia,
				'data_emissao'            => $data_emissao,
				'valor_juros'             => $valor_juros,
				'desconto_reais'          => $desconto_reais,
				'boleto_id'               => $boleto_id,
				'codigo_movimento_remessa' => $codigo_movimento_remessa
			));

			return $this->getInsertID();
    } */
  private $quantidade_linhas = 0;
  private function gerarDetalheArquivoEntrada($unidade, $detalhe)
    {
      $boletosTable = TableRegistry::get('Boletos');
      $boleto = $boletosTable->find('all', 
        [
          'conditions' => 
            [
              'Boletos.id' => $detalhe->boleto_id 
            ]
        ])->contain(['Pessoas' => ['Alunos', 'Enderecos']])->first();
      $this->gerarSegmentoP($unidade, $detalhe, $boleto);
      $this->gerarSegmentoQ($unidade, $detalhe, $boleto, '01', '2'); 
      $this->gerarSegmentoR($detalhe, $boleto, '01', '3');
    }
  private function gerarDetalheArquivoBaixa($unidade, $detalhe)
    {
      $boletosTable = TableRegistry::get('Boletos');
      $boleto = $boletosTable->find('all', 
        [
          'conditions' => 
            [
              'Boletos.id' => $detalhe->boleto_id 
            ]
        ])->contain(['Pessoas' => ['Alunos', 'Enderecos']])->first();
        $this->gerarSegmentoQ($unidade, $detalhe, $boleto, '06', '1'); 
        $this->gerarSegmentoR($detalhe, $boleto, '06', '2');
    }
 /*
	private function detalhe_p($raw, $numero_lote_remessa = 0, $numero_registro_no_lote = 0, $numero_conta_corrente = 0, $digito_verificador = 0, $nosso_numero = 0, $numero_documento = 0, $data_vencimento = 0, $valor_nominal = 0, $agencia = 0, $digito_agencia = 0, $data_emissao = 0, $valor_juros = 0, $desconto_reais = 0, $codigo_movimento_remessa)
	{
		$dados = array(

			'033', 
			 str_pad($numero_lote_remessa, 4, '0', STR_PAD_LEFT), 
			 '3', 
			 str_pad($numero_registro_no_lote, 5, '0', STR_PAD_LEFT),
			 'P', 
			 ' ', 
			 $codigo_movimento_remessa,
			 str_pad($agencia, 4, '0', STR_PAD_LEFT), 
			 str_pad($digito_agencia, 1, '0', STR_PAD_LEFT), 
			 str_pad($numero_conta_corrente, 9, '0', STR_PAD_LEFT),
			 str_pad($digito_verificador, 1, '0', STR_PAD_LEFT),
			 str_pad($numero_conta_corrente, 9, '0', STR_PAD_LEFT), 
			 str_pad($digito_verificador, 1, '0', STR_PAD_LEFT), 
			 '  ', 
			 str_pad($nosso_numero, 13, '0', STR_PAD_LEFT),
			 '5' 
			 '1', 
			 '1', 
			 ' ', 
			 ' ', 
			 str_pad($numero_documento, 15, '0', STR_PAD_LEFT),
			 date('dmY', strtotime($data_vencimento)),
			 str_pad(number_format($valor_nominal, 2, '', ''), 15, '0', STR_PAD_LEFT),
			 str_pad($agencia, 4, '0', STR_PAD_LEFT),
			 str_pad($digito_agencia, 1, '0', STR_PAD_LEFT),
			 ' ', 
			 '04', 
			 'N',
			 date('dmY', strtotime($data_emissao)),
			 '1', 
			 date('dmY', strtotime($data_vencimento)), 
			 str_pad(number_format($valor_juros, 2, '', ''), 15, '0', STR_PAD_LEFT), 
			 '1', 
			 date('dmY', strtotime($data_vencimento)), 
			 str_pad(number_format($desconto_reais, 2, '', ''), 15, '0', STR_PAD_LEFT), 
			 '000000000000000', 
			 '000000000000000', 
			 str_pad('', 25, '0', STR_PAD_LEFT),
			 '0', 
			 '00', 
			 '1', 
			 '0', 
			 '30', 
			 '00', 
			 str_pad('', 11) 
		);

		return $this->imprimir($raw, $dados);
  } //ok*/
  private function gerarSegmentoP($unidade, $detalhe, $boleto, $sequencia = 1)
    {
      $valor_juros = round(($boleto->valor_sem_desconto / 100) / 30);
      if($valor_juros < 1)
        {
				  $valor_juros = 1;
				}
      $conteudo_linha = '033' . 
      str_pad($detalhe->numero_sequencial, 4, '0', STR_PAD_LEFT) . 
      '3' . 
      str_pad($sequencia, 5, '0', STR_PAD_LEFT) . 
      'P' . 
      ' 01' . 
      str_pad($unidade->agencia_registro, 4, '0', STR_PAD_LEFT) . 
      str_pad($unidade->digito_verificador_agencia_registro, 1, '0', STR_PAD_LEFT) . 
      str_pad($unidade->conta_corrente_registro, 9, '0', STR_PAD_LEFT) . 
      str_pad($unidade->digito_verificador_conta_corrente_registro, 1, '0', STR_PAD_LEFT) . 
      str_pad($unidade->conta_corrente_registro, 9, '0', STR_PAD_LEFT) . 
      str_pad($unidade->digito_verificador_conta_corrente_registro, 1, '0', STR_PAD_LEFT) . 
      '  ' . 
      str_pad($this->modulo_11(str_pad($boleto->numero_interno, 7, '0'), 9, 0), 13, '0', STR_PAD_LEFT) . 
      '511  ' . 
      str_pad($boleto->numero_documento, 15, '0', STR_PAD_LEFT) . 
      $boleto->data_vencimento->format('dmY') . 
      str_pad($boleto->valor_sem_desconto, 15, 0, STR_PAD_LEFT) . 
      str_pad($unidade->agencia, 4, "0", STR_PAD_LEFT) . 
      str_pad($unidade->digito_verificador_agencia_registro, 1, '0', STR_PAD_LEFT) . 
      ' 04N' . 
      $boleto->data_processamento->format('dmY') . 
      '1' . 
      $boleto->data_vencimento->format('dmY') . 
      str_pad($valor_juros, 15, '0', STR_PAD_LEFT) .  
      '1' . 
      $boleto->data_vencimento->format('dmY') . 
      str_pad($boleto->valor_desconto, 15, '0', STR_PAD_LEFT) . 
      '0000000000000000000000000000000000000000000000000000000000103000           ';
      //Substituir os 15 zeros pelo valor do juros posteriormente (15 zeros logo após a data de vencimento que tá logo depois da data de processamento seguida pelo número 1)
      //Substituir os próximos 15 zeros pelos descontos reais
      $this->conteudo_arquivo .= $conteudo_linha  . "\r\n";
      $this->quantidade_linhas++;
      //033 0001 3 00001 P 01 3795 5 013000555 7 013000555 7  0000000000000 511  000000003169750 24012020 000000000010234 3795 5 04N 22012020 1 24012020 000000000000000 000000000000000 0000000000000000000000000000000000000000000000000000000000 103000

      //033 0001 3 00001 P 01 3795 5 013000555 7 013000555 7  0000000775932 511  000000000077593 31012020 000000000032451 3795 5 04N 28012020 1 31012020 000000000000011 131012020000000 0000000000000000000000000000000000000000000000000000000000000000000 103000          
    }
  private function gerarSegmentoQ($unidade, $detalhe, $boleto, $movimento, $sequencia = 1)
    {
      $sacado = $boleto->pessoa->aluno->responsavel;
      $endereco = (count($sacado->pessoa->enderecos)) ? $sacado->pessoa->enderecos[0] : $boleto->pessoa->enderecos[0]; 
      $conteudo_linha = '033' . str_pad($detalhe->numero_sequencial, 4, '0', STR_PAD_LEFT) . '3' . str_pad($sequencia, 5, '0', STR_PAD_LEFT) . 'Q ' . $movimento . '1' . str_pad(preg_replace('/\D/', '', $sacado->pessoa->cpf), 15, '0', STR_PAD_LEFT) . str_pad($this->formataStringRemessa($sacado->pessoa->nome), 40) . str_pad($this->formataStringRemessa($endereco->logradouro) . ' ' . $this->formataStringRemessa($endereco->numero) . ' ' . $this->formataStringRemessa($endereco->complemento), 40) . str_pad($this->formataStringRemessa($endereco->bairro), 15) . substr(preg_replace('/\D/', '', $endereco->cep), 0, 5) . substr(preg_replace('/\D/', '', $endereco->cep), 5) . str_pad($this->formataStringRemessa($endereco->cidade), 15) . str_pad($endereco->estado, 2) . '0000000000000000                                        000000000000                   ';
      $this->quantidade_linhas++;
      $this->conteudo_arquivo .= $conteudo_linha  . "\r\n";
      //0330001300001Q 061000002917315709FABIO DE FREITAS RIGHETTI DA SILVA      RUA MAGALHAES COUTO 434 11             MEIER         20735180RIO DE JANEIRO RJ00000000000000                                        000000000000                   
      //0330001300002Q 011000002917315709FABIO DE FREITAS RIGHETTI DA SILVA      RUA MAGALHAES COUTO 434 CASA 11         MEIER          20735180RIO DE JANEIRO RJ0000000000000000                                        000000000000                   
    }
  private function gerarSegmentoR($detalhe, $boleto, $movimento, $sequencia = 1)
     {
      $conteudo_linha = '033' . str_pad($detalhe->numero_sequencial, 4, '0', STR_PAD_LEFT) . '3' . str_pad($sequencia, 5, '0', STR_PAD_LEFT) . 'R ' . $movimento . '000000000000000000000000                        2' . $boleto->data_vencimento->format('dmY') . str_pad(number_format(2, 2, '', ''), 15, '0', STR_PAD_LEFT) . '                                                                                                                                                       ';
      $this->quantidade_linhas++;
      $this->conteudo_arquivo .= $conteudo_linha . "\r\n";
    }
  /*
    private function detalhe_r($raw, $numero_lote_remessa = 0, $numero_sequencial_no_lote = 0, $data_vencimento = 0, $valor_nominal = 0, $codigo_movimento_remessa)
	{
		$dados = array(

			'033', 
			 str_pad($numero_lote_remessa, 4, '0', STR_PAD_LEFT),
			 '3', 
			 str_pad($numero_sequencial_no_lote, 5, '0', STR_PAD_LEFT),
			 'R', 
			 ' ', 
			 $codigo_movimento_remessa,
			 '0',
			 '00000000',
			 '000000000000000',
			 str_pad('', 24), 
			 '2', 
			 date('dmY', strtotime($data_vencimento)), 
			 str_pad(number_format(2, 2, '', ''), 15, '0', STR_PAD_LEFT), 
			 str_pad('', 10), 
			 str_pad('', 40), 
			 str_pad('', 40), 
			 str_pad('', 61) 
		);

		return $this->imprimir($raw, $dados);
	} //ok
  */
    /* 
      private function detalhe_q($raw, $numero_lote_remessa = 0, $numero_sequencial_no_lote = 0, $nome_sacado = '', $endereco_sacado = '', $bairro_sacado = '', $cep_sacado = '', $sufixo_cep_sacado  = '', $cidade_sacado = '', $uf_sacado = '', $cpf_sacador = '', $nome_sacador = '', $codigo_movimento_remessa)
	{
		$dados = array(

			'033',
			 str_pad($numero_lote_remessa, 4, '0', STR_PAD_LEFT),
			 '3',
			 str_pad($numero_sequencial_no_lote, 5, '0', STR_PAD_LEFT),
			 'Q',
			 ' ',
			 $codigo_movimento_remessa,
			 '1', 
			 str_pad($cpf_sacador, 15, '0', STR_PAD_LEFT),
			 str_pad($nome_sacado, 40), 
			 str_pad($endereco_sacado, 40),
			 str_pad($bairro_sacado, 15),
			 str_pad($cep_sacado, 5, '0', STR_PAD_LEFT),
			 str_pad($sufixo_cep_sacado, 3, '0', STR_PAD_LEFT),
			 str_pad($cidade_sacado, 15),
			 str_pad($uf_sacado, 2),
			 '0', 
			 str_pad('', 15, '0', STR_PAD_LEFT), 
			 str_pad('', 40),
			 '000',
			 '000', 
			 '000', 
			 '000',
			 str_pad('', 19)
		);

		return $this->imprimir($raw, $dados);
	} //ok

    */
  private function formataStringRemessa($string)
    {
      return strtoupper($this->removeAcentos($string));
    }
  private function removeAcentos($string)
    {
        if ( !preg_match('/[\x80-\xff]/', $string) )
          {
            return $string;
          }

        $chars = [
        // Decompositions for Latin-1 Supplement
        chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
        chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
        chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
        chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
        chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
        chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
        chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
        chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
        chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
        chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
        chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
        chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
        chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
        chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
        chr(195).chr(191) => 'y',
        // Decompositions for Latin Extended-A
        chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
        chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
        chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
        chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
        chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
        chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
        chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
        chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
        chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
        chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
        chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
        chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
        chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
        chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
        chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
        chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
        chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
        chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
        chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
        chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
        chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
        chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
        chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
        chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
        chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
        chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
        chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
        chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
        chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
        chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
        chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
        chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
        chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
        chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
        chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
        chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
        chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
        chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
        chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
        chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
        chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
        chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
        chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
        chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
        chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
        chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
        chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
        chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
        chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
        chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
        chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
        chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
        chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
        chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
        chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
        chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
        chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
        chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
        chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
        chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
        chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
        chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
        chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        ];

        $string = strtr($string, $chars);

        return $string;
    }

  /*
    $this->ArquivoRemessa->LoteRemessa->DetalheRemessa->salvar_detalhe_q(
					$lote_remessa['LoteRemessa']['id'],
					$lote_remessa['LoteRemessa']['numero_lote_arquivo'],
					$this->remover_acentos($responsavel['Pessoa']['nome']),
					$this->remover_acentos($responsavel['Logradouro']['nome'] . ' ' . $responsavel['Endereco']['numero'] . ' ' . $responsavel['Endereco']['complemento']),
					$this->remover_acentos($responsavel['Bairro']['nome']),
					substr($responsavel['Logradouro']['cep'], 0, 5),
					substr($responsavel['Logradouro']['cep'], 5, 3),
					$this->remover_acentos($responsavel['Cidade']['nome']),
					$this->remover_acentos($responsavel['Cidade']['sigla_uf']),
					$this->remover_acentos($responsavel['Pessoa']['cpf']),
					$this->remover_acentos($boleto['Pessoa']['nome']),
					$boleto['Boleto']['id'],
					$tipo_arquivo
        ); //ok
        $raw = $this->detalhe_q(
					$raw,
					$detalhe['DetalheRemessa']['numero_lote_remessa'],
					$detalhe['DetalheRemessa']['numero_registro_no_lote'],
					$detalhe['DetalheRemessa']['nome_sacado'],
					$detalhe['DetalheRemessa']['endereco_sacado'],
					$detalhe['DetalheRemessa']['bairro_sacado'],
					$detalhe['DetalheRemessa']['cep_sacado'],
					$detalhe['DetalheRemessa']['sufixo_cep_sacado'],
					$detalhe['DetalheRemessa']['cidade_sacado'],
					$detalhe['DetalheRemessa']['uf_sacado'],
					$detalhe['DetalheRemessa']['cpf_sacador'],
					$detalhe['DetalheRemessa']['nome_sacador'],
					$detalhe['DetalheRemessa']['codigo_movimento_remessa']
				);
  */


  private function modulo_11($num, $base = 9, $r = 0)
    {
      $soma = 0;
      $fator = 2;

      for ($i = strlen($num); $i > 0; $i--) {
        $numeros[$i] = substr($num, $i - 1, 1);

        $parcial[$i] = $numeros[$i] * $fator;

        $soma += $parcial[$i];

        if ($fator == $base) {
          $fator = 1;
        }

        $fator++;
      }

      if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;

        if ($digito == 10) {
          $digito = 0;
        }

        return $digito;
      } elseif ($r == 1) {
        $resto = $soma % 11;
        return $resto;
      }
    }
  private function gerarTrailerLote($numero_lote)
    {
      $conteudo_linha = '033' . str_pad($numero_lote, 4, '0', STR_PAD_LEFT) . '5' . '         ' . str_pad($this->quantidade_linhas, 6, '0', STR_PAD_LEFT) . '                                                                                                                                                                                                                         ';
      $this->conteudo_arquivo .= $conteudo_linha   . "\r\n";
    }
  private function gerarTrailerArquivo($quantidade_lotes)
    {
      $conteudo_linha = '03399999         ' . str_pad($quantidade_lotes, 6, '0', STR_PAD_LEFT) . str_pad(($this->quantidade_linhas + 4), 6, '0', STR_PAD_LEFT) . '                                                                                                                                                                                                                   ';
      $this->conteudo_arquivo .= $conteudo_linha . "\r\n";
    }
   /*
    private function trailer_lote($raw, $numero_lote_arquivo = 0, $quantidade_linhas_lote = 0)
	{
		$dados = array(

			'033', 
			 str_pad($numero_lote_arquivo, 4, '0', STR_PAD_LEFT)
			 '5'
			 str_pad('', 9)
			 str_pad($quantidade_linhas_lote, 6, '0', STR_PAD_LEFT)
			 str_pad('', 217
		);

		return $this->imprimir($raw, $dados);
	} //ok

  private function trailer_arquivo($raw, $quantidade_lotes = 0, $quantidade_linhas_arquivo = 0)
	{
		$dados = array(

			'033', /* 
			 '9999', 
			 '9', 
			 str_pad('', 9), 
			 str_pad($quantidade_lotes, 6, '0', STR_PAD_LEFT), 
			 str_pad($quantidade_linhas_arquivo, 6, '0', STR_PAD_LEFT), 
			 str_pad('', 211) 
		);

		return $this->imprimir($raw, $dados);
	} //ok
*/
  public function remessasGeradas()
    {
      $remessasTable = TableRegistry::get('Remessas');
      $remessas = $remessasTable->find('all', 
        [
          'order' => 
            [
              'Remessas.id DESC'  
            ]
        ])->contain(['Unidades'])->toArray();
      $this->set('remessas', $remessas);      
      $this->set('titulo', 'Remessas geradas | Aldeia Montessori');
    }
  private $servicos_grupo =
    [
      1 => 'Educacionais',
      2 => 'Alimentação',
      3 => 'Hotelaria',
      5 => 
        [
          'nome' => 'Natação',
          'agrupados' =>
            [
              5,9,10,11
            ]
        ],
      7 => 'Capoeira'
    ];
  public function controleNotaFiscal()
    {
      $unidadesTable = TableRegistry::get('Unidades');
      $alunosTable   = TableRegistry::get('Alunos');
      $servicosTable = TableRegistry::get('Servicos');
      $unidades = $unidadesTable->find('all', 
      [
        'conditions' =>
        [
          'ativo' => true
        ]
      ])->toArray();
      $_servicos = [];
      foreach($unidades as &$unidade)
        {
          $servicosUnidade = [];
          $_servicosUnidade = $servicosTable->find('all', 
            [
              'conditions' => 
                [
                  'unidade' => $unidade->id,
                  'ServicosAux.ativo' => true,
                ]
            ])->distinct(['servico'])
            ->contain(['ServicosAux'])
            ->toArray();
          foreach($_servicosUnidade as $servico)
            {
              $exp = explode(' ', $servico->ServicoAux->nome);
              if(count($exp) > 1)
                {
                  if(!in_array($exp[0], $servicosUnidade))
                    {
                      $servicosUnidade[5] = $exp[0];
                    }
                }
              else
                {
                  $servicosUnidade[$servico->servico] = $servico->ServicoAux->nome;
                }
            }
          $unidade->servicos = $servicosUnidade;
          $alunos = $alunosTable->find('all',
            [
              'conditions' =>
                [
                  'unidade' => $unidade->id,
                  'ano_letivo' => date('Y')
                ]
            ])->contain(['Pessoas'])->toArray();
          foreach($alunos as &$aluno)
            {
              $servicos_aluno = $aluno->servicos_array;
              $financeiro     = $aluno->financeiro_array;
              $valores        = [];
              foreach($servicos_aluno as $servico)
                {
                  if(!isset($_servicos[$servico]))
                    {
                      $servico = $servicosTable->get($servico);
                    }
                  else
                    {
                      $servico = $_servicos[$servico];
                    }
                  $valor = $servico->valor_atual;
                  if($valor)
                    {
                      $percentual = (isset($aluno->financeiro_array[$servico->id])) ? (int) $aluno->financeiro_array[$servico->id] : 0;
                      $desconto = (((int)$valor->valor*$percentual)/100);
                      $valor_servico = ((int)$valor->valor - $desconto);
                      $s = (in_array((int)$servico->servico, [5,9,10,11])) ? 5 : $servico->servico;
                      $valores[$s] = $valor_servico;
                    }
                }
              $aluno->valores_servico = $valores;
            }
          $unidade->alunos = $alunos;
        }
      $this->set('titulo', 'Controle de notas fiscais | Aldeia Montessori');
      $this->set('servicos_grupo', $this->servicos_grupo);
      $this->set('unidades', $unidades);
    }
  public function atualizarAlunoNotaFiscal()
    {
      if($this->request->is('POST'))
        {
          $this->response = $this->response->withType('application/json');
          $data = $this->request->getData();
          $success = false;
          $alunosTable = TableRegistry::get('Alunos');
          $aluno = $alunosTable->get($data['aluno']);
          $aluno->emite_nota_fiscal = ($data['ativar'] === "false") ? false : true;
          if($alunosTable->save($aluno))
            {
              $success = true;
            }
          $this->response = $this->response->withStringBody(json_encode(['success' => $success]));
          return $this->response;
        }
    }
  public function cobranca()
    {
      $unidadesTable = TableRegistry::get('Unidades');
      $unidades = $unidadesTable->find('all', 
        [
          'conditions' =>
            [
              'ativo' => true
            ]
        ])->toArray();
      $this->set('titulo', 'Cobrança | Aldeia Montessori');
      $this->set('unidades', $unidades);
    }
  public $meses_pt    = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
  public function sessaoListaCobranca()
    {
      if($this->request->is('POST'))
        {
          $alunosTable = TableRegistry::get('Alunos');
          $this->viewBuilder()->disableAutoLayout();            
          $data       = $this->request->getData();
          $scope      = (int)$data['scope'];
          $key        = (int)$data['key'];
          $parent_key = (int)$data['parent_key'];
          $id         = $data['id'];
          $blocks     = [];
          switch($data['scope'])
            {
              case '1':
                if(strpos($data['id'], 'inativas_') !== false)
                  {
                    $alunos = $alunosTable->find('all',
                      [
                        'conditions' =>
                          [
                            'Alunos.matricula_cancelada' => true,
                            'Alunos.unidade' => $data['parent_key']
                          ]
                      ])->contain(['Pessoas' => ['BoletosVencidos']])->distinct('Alunos.ano_letivo')->order('Alunos.ano_letivo DESC')->toArray();
                      foreach($alunos as $aluno)
                        {
                          $block =
                            [
                              'scope' => 2,
                              'key' => $aluno->ano_letivo . '_' . $aluno->unidade,
                              'parent_key' => $key,
                              'parent_scope' => $scope,
                              'nome' => $aluno->ano_letivo,
                              'unique' => 'ano_letivo_' . $aluno->ano_letivo . '_unidade_' . $aluno->unidade,
                              'parent_id' => $id
                            ];
                          $blocks[] = $block;
                        }
                  }
                elseif(strpos($data['id'], 'ativas_') !== false)
                  {
                    $alunos = $alunosTable->find('all', 
                      [
                        'conditions' =>
                          [
                            'Alunos.matricula_cancelada' => false,
                            'Alunos.unidade' => $data['parent_key'],
                          ]
                      ])->contain(['Pessoas' => ['BoletosVencidos']])->toArray();
                    foreach($alunos as $aluno)
                      {
                        if(@count($aluno->pessoa->boletos_vencidos) < 1)
                          {
                            continue;
                          }
                        $valor_atrasado = 0;
                        $maior_atraso = new \DateTime();
                        $today = new \DateTime();
                        foreach($aluno->pessoa->boletos_vencidos as $boleto)
                          {
                            $valor_juros = round(((int)$boleto->valor_sem_desconto / 100) / 30);
                            if($valor_juros < 1)
                            {
                              $valor_juros = 1;
                            }
                            $atraso = new \DateTime($boleto->data_vencimento->format('Y-m-d'));
                            $interval = $atraso->diff($today);
                            $valor_atrasado += (int)$boleto->valor_sem_desconto + ((int)$interval->format('%a')*$valor_juros);
                            if($maior_atraso > $atraso)
                              {
                                $maior_atraso = $atraso;
                              }
                          }
                        $block = 
                          [
                            'scope' => 2,
                            'key'   => $aluno->id,
                            'parent_key' => $key,
                            'parent_scope' => $scope,
                            'nome' => $aluno->pessoa->nome,
                            'unique' => uniqid(),
                            'parent_id' => $id,
                            'dados_boletos' => 
                              [
                                'valor' => $valor_atrasado,
                                'maior_atraso' => $maior_atraso
                              ]
                          ];
                        $blocks[] = $block;
                      }
                  }
                break;
                case '2':
                  if(strpos($data['id'], 'ano_letivo_') !== false)
                    {
                      $termos = explode('_', $data['key']);
                      $alunos = $alunosTable->find('all', 
                        [
                          'conditions' => 
                            [
                              'Alunos.matricula_cancelada' => true,
                              'Alunos.ano_letivo' => $termos[0],
                              'Alunos.unidade' => $termos[1]
                            ]
                        ])->contain(['Pessoas' => ['BoletosVencidos']])->distinct('Alunos.ano_letivo')->order('Alunos.ano_letivo DESC')->toArray();
                      foreach($alunos as $aluno)
                        {
                          if(@count($aluno->pessoa->boletos_vencidos) < 1)
                            {
                              continue;
                            }
                          $valor_atrasado = 0;
                          $maior_atraso = new \DateTime();
                          $today = new \DateTime();
                          foreach($aluno->pessoa->boletos_vencidos as $boleto)
                            {
                              $valor_juros = round(((int)$boleto->valor_sem_desconto / 100) / 30);
                              if($valor_juros < 1)
                                {
                                  $valor_juros = 1;
                                }
                              $atraso = new \DateTime($boleto->data_vencimento->format('Y-m-d'));
                              $interval = $atraso->diff($today);
                              $valor_atrasado += (int)$boleto->valor_sem_desconto + ((int)$interval->format('%a')*$valor_juros);
                              if($maior_atraso > $atraso)
                                {
                                  $maior_atraso = $atraso;
                                }
                            }
                          $block =
                            [
                              'scope' => 3,
                              'key' => $aluno->id,
                              'parent_key' => $key,
                              'parent_scope' => $scope,
                              'nome' => $aluno->pessoa->nome,
                              'unique' => uniqid(),
                              'parent_id' => $id,
                              'dados_boletos' => 
                                [
                                  'valor' => $valor_atrasado,
                                  'maior_atraso' => $maior_atraso
                                ]
                            ];
                          $blocks[] = $block;
                        }
                    }
                  else
                    {
                      $blocks = 'aluno';
                      $aluno = $alunosTable->find('all', 
                        [
                          'conditions' => 
                            [
                              'Alunos.id' => $data['key']
                            ]
                        ])->contain(['Pessoas' => ['BoletosVencidos', 'Cobrancas']])->first();
                      $this->set('aluno', $aluno);
                      $this->set('scope', 3);
                      $this->set('key', $aluno->id);
                      $this->set('parent_key', $key);
                      $this->set('parent_scope', $scope);
                      $this->set('parent_id', $id);
                    }
                break;
                case '3':
                      $blocks = 'aluno';
                      $aluno = $alunosTable->find('all', 
                        [
                          'conditions' => 
                            [
                              'Alunos.id' => $data['key']
                            ]
                        ])->contain(['Pessoas' => ['BoletosVencidos', 'Cobrancas']])->first();
                      $this->set('aluno', $aluno);
                      $this->set('scope', 4);
                      $this->set('key', $aluno->id);
                      $this->set('parent_key', $key);
                      $this->set('parent_scope', $scope);
                      $this->set('parent_id', $id);
                break;
              }
          $this->set('blocks', $blocks);
        }
    }
    public function enviarCobranca()
      {
        if($this->request->is('POST'))
          {
            $success = false;
            $data = $this->request->getData();
            $alunosTable = TableRegistry::get('Alunos');
            $aluno = $alunosTable->find('all', 
              [
                'conditions' => 
                  [
                    'Alunos.id' => $data['aluno']
                  ]
              ])->contain(['Pessoas' => ['BoletosVencidos']])->first();
            $cobrancasTable = TableRegistry::get('Cobrancas');
            $cobranca = $cobrancasTable->newEntity(
              [
                'tipo' => 0,
                'pessoa_id' => $aluno->pessoa->id,
                'assunto' => 'Por favor, verifique. Boletos em Aberto.',
                'aluno_id' => $aluno->id,
                'data_envio' => date('Y-m-d H:i:s')
              ]);
            $cobrancasTable->save($cobranca);
            $email = new Email('default');
            $email->viewBuilder()->setTemplate('cobranca');
            $email->viewBuilder()->setHelpers(['Grana', 'Url']);
            $email->addBcc('vinicius@aigen.com.br')
                ->setEmailFormat('html')
                ->setSubject('Por favor, verifique. Boletos em Aberto.')
                ->setViewVars(['aluno' => $aluno, 'cobranca' => $cobranca])
                ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
            $email->addTo([$aluno->responsavel->pessoa->email => $aluno->responsavel->pessoa->nome]);
            if($email->send())
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(
              [
                'success' => $success
              ]));
            return $this->response;
          }
      }
    public function atualizarTabelaCobrancas()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $alunosTable = TableRegistry::get('Alunos');
            $aluno = $alunosTable->find('all', 
              [
                'conditions' => 
                  [
                    'Alunos.id' => $data['aluno']
                  ]
              ])->contain(['Pessoas' => ['BoletosVencidos', 'Cobrancas']])->first();
            $this->set('aluno', $aluno);
          }
      }
    public function geracaoNotaFiscal()
      {
        $this->set('titulo', 'Gerar e enviar notas fiscais | Aldeia Montessori');
      }
    public function gerarRps()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $unidadesTable = TableRegistry::get('Unidades');
            $alunosTable = TableRegistry::get('Alunos');
            $lotesRPSTable = TableRegistry::get('LotesRPS');
            $RPSTable = TableRegistry::get('RPS');
            $servicosTable = TableRegistry::get('Servicos');
            $unidades  = $unidadesTable->find('all', ['order' => 'ordenacao ASC'])->toArray();
            $dados_nf = [];
            foreach($unidades as $unidade)
              {
                /*
                f($aluno['NivelEmpresa']['empresa_id'] == $empresa['Empresa']['id'])
						{
							if(isset($emitem[$aluno['Aluno']['id']]) == true)
							{
								if(isset($boletos[$aluno['Aluno']['id']]['total']) == true)
								{
									if($boletos[$aluno['Aluno']['id']]['total'] > 0)
                */
                $lote = $lotesRPSTable->find('all', 
                  [
                    'conditions' =>
                      [
                        'MONTH(data_criacao)' => date('m'),
                        'YEAR(data_criacao)' => date('Y'),
                        'unidade' => $unidade->id
                      ]
                  ])->contain(['RPS'])->first();
                $_servicos = [];
                $alunos = $alunosTable->find('all', 
                  [
                    'conditions' =>
                     [
                      'Alunos.emite_nota_fiscal' => TRUE,
                      'Alunos.unidade' => $unidade->id,
                     ]
                  ])->contain(['Pessoas' => ['Enderecos'], 'Cursos'])->toArray();
                $_rps = 0;
                if((is_null($lote))&&(count($alunos) > 0))
                  {
                    //$numero = 1;
                    $numero = 2898;
                    $ultimo = $lotesRPSTable->find('all', ['unidade' => $unidade->id])->order(['id DESC'])->first();
                    if(!is_null($ultimo))
                      {
                        $numero = ((int)$ultimo->numero_sequencial + 1);
                      }
                    $lote = $lotesRPSTable->newEntity(
                      [
                        'unidade' => $unidade->id,
                        'numero_sequencial' => $numero,
                        'conteudo_xml' => ' '
                      ]);
                    $lotesRPSTable->save($lote);

                      $lista_rps_xml = "";
                      foreach($alunos as $aluno)
                        {
                          $servicos_aluno = $aluno->servicos_array;
                          $financeiro     = $aluno->financeiro_array;
                          $valor          = 0;
                          foreach($servicos_aluno as $servico)
                            {
                              if(!isset($_servicos[$servico]))
                                {
                                  $servico = $servicosTable->get($servico);
                                  $_servicos[$servico->id] = $servico;
                                }
                              else
                                {
                                  $servico = $_servicos[$servico];
                                }
                              if($servico->valor_atual)
                                {
                                  $desconto = (((int)$servico->valor_atual->valor*(int)$aluno->financeiro_array[$servico->id])/100);
                                  $valor_servico = ((int)$servico->valor_atual->valor - $desconto);
                                  $valor += $valor_servico;
                                }
                            }
                          $numero = 14053;
                          $ultimo = $RPSTable->find('all', ['lote_id' => $lote->id])->order(['id DESC'])->first();
                          if(!is_null($ultimo))
                            {
                              $numero = ((int)$ultimo->numero_sequencial + 1);
                            }
                          $endereco = (count($aluno->responsavel->pessoa->enderecos) < 1) ? $aluno->pessoa->enderecos[0] : $aluno->responsavel->pessoa->enderecos[0];
                          $rps = $RPSTable->newEntity(
                            [
                              'lote_id' => $lote->id,
                              'aluno' => $aluno->id,
                              'numero_sequencial' => $numero
                            ]);
                          $RPSTable->save($rps);
                          $_rps++;
                          $lista_rps_xml .= '<Rps>
                            <InfRps>
                                <IdentificacaoRps>
                                    <Numero>' . $rps->numero_sequencial . '</Numero>
                                    <Serie>ABC</Serie>
                                    <Tipo>1</Tipo>
                                </IdentificacaoRps>
                                <DataEmissao>' . $rps->data_criacao->format('Y-m-d') . 'T' . $rps->data_criacao->format('H:i:s') . '</DataEmissao>
                                <NaturezaOperacao>1</NaturezaOperacao>
                                <OptanteSimplesNacional>1</OptanteSimplesNacional>
                                <IncentivadorCultural>2</IncentivadorCultural>
                                <Status>1</Status>
                                <Servico>
                                    <Valores>
                                        <ValorServicos>' . number_format(($valor/100), 2, ".", "") . '</ValorServicos>
                                        <IssRetido>2</IssRetido>
                                    </Valores>
                                    <ItemListaServico>0801</ItemListaServico>
                                    <CodigoTributacaoMunicipio>' . $aluno->Curso->codigo_tributacao . '</CodigoTributacaoMunicipio>
                                    <Discriminacao>Prestação de Serviços Educacionais para ' . $aluno->pessoa->nome . ', referente ao mês de ' . $this->meses_pt[date('m')-1] . ' de ' . date('Y') . '  Em conformidade com a Lei 12.741/2012, empresa enquadrada no Simples Nacional alíquota ' . $unidade->aliquota . '%.</Discriminacao>
                                    <CodigoMunicipio>3304557</CodigoMunicipio>
                                </Servico>
                                <Prestador>
                                    <Cnpj>' . preg_replace('/\D/', '', $unidade->cnpj) . '</Cnpj>
                                    <InscricaoMunicipal>' . $unidade->im . '</InscricaoMunicipal>
                                </Prestador>
                                <Tomador>
                                    <IdentificacaoTomador>
                                        <CpfCnpj>
                                            <Cpf>' . preg_replace('/\D/', '', $aluno->responsavel->pessoa->cpf) . '</Cpf>
                                        </CpfCnpj>
                                    </IdentificacaoTomador>
                                    <RazaoSocial>' . $aluno->responsavel->pessoa->nome . '</RazaoSocial>
                                    <Endereco>
                                        <Endereco>' . $endereco->logradouro . '</Endereco>
                                        <Numero>' . $endereco->numero . '</Numero>
                                        <Complemento>' . $endereco->complemento . '</Complemento>
                                        <Bairro>' . $endereco->bairro . '</Bairro>
                                        <Uf>' . $endereco->estado . '</Uf>
                                        <Cep>' . preg_replace('/\D/', '', $endereco->cep) . '</Cep>
                                    </Endereco>
                                    <Contato>
                                        <Email>' . $aluno->responsavel->pessoa->email . '</Email>
                                    </Contato>
                                </Tomador>
                            </InfRps>
                        </Rps>
                        ';
                        }
                      $xml = '<?xml version="1.0" encoding="utf-8"?>
                        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                              <RecepcionarLoteRpsRequest xmlns="http://notacarioca.rio.gov.br/">
                                  <inputXML>
                                  ' . str_replace(['<', '>'], ['&lt;', '&gt;'],'<EnviarLoteRpsEnvio xmlns="http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd">
                                  <LoteRps>
                                    <NumeroLote>'.$lote->numero_sequencial.'</NumeroLote>
                                    <Cnpj>'. preg_replace('/\D/', '', $unidade->cnpj) .'</Cnpj>
                                    <InscricaoMunicipal>'.$unidade->im .'</InscricaoMunicipal>
                                     <QuantidadeRps>'. str_pad($_rps, 4, "0", STR_PAD_LEFT) .'</QuantidadeRps>
                                    <ListaRps>'
                                    . $lista_rps_xml .
                                    '</ListaRps>
                                  </LoteRps>
                              </EnviarLoteRpsEnvio>') . '
                              </inputXML>
                              </RecepcionarLoteRpsRequest>
                            </soap:Body>
                        </soap:Envelope>';
                        $lote->conteudo_xml = base64_encode($xml);
                        $lotesRPSTable->save($lote);
                        $dados_nf[$unidade->id] = ['unidade' => $unidade, 'lote' => $lote]; 
                  }
                elseif((!is_null($lote))&&(!$lote->enviado))
                  {
                    $rps = $RPSTable->find('all', ['conditions' => ['lote_id' => $lote->id]])->toArray();
                    $lote->rps = $rps;
                    $dados_nf[$unidade->id] = ['unidade' => $unidade, 'lote' => $lote]; 
                  }
                  $this->set('dados_nf', $dados_nf);
              }
          }
      }
    public function enviarLotes()
      {
        if($this->request->is('POST'))
          {
            $this->autoRender = false;
            $lotesRPSTable = TableRegistry::get('LotesRPS');
            $lotes = $lotesRPSTable->find('all', 
              [
                'conditions' =>
                  [
                    'MONTH(LotesRPS.data_criacao)' => date('m'),
                    'YEAR(LotesRPS.data_criacao)' => date('Y'),
                    'LotesRPS.enviado' => false
                  ]
              ])->contain(['Unidades'])->toArray();
            foreach($lotes as $lote)
              {
                $envio = $this->enviarXML(base64_decode($lote->conteudo_xml), 'http://notacarioca.rio.gov.br/RecepcionarLoteRps', preg_replace('/\D/', '', $lote->Unidade->cnpj));
                $dados = $this->conteudoXML($envio);
                $valido = ((isset($dados['NumeroLote']))&&(isset($dados['DataRecebimento']))&&(isset($dados['Protocolo'])&&(count($dados) == 3)));
                if($valido)
                  {
                    $lote->enviado = true;
                    $lote->resposta_envio = base64_encode($envio);
                    $lotesRPSTable->save($lote);
                  }
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(
              ['success' => true]));
            return $this->response;
          }
      }

    private function enviarXML($xml, $action, $cnpj)
      {
        //$url = "https://homologacao.notacarioca.rio.gov.br/WSNacional/nfse.asmx";
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
    private function conteudoXML($string, $input = false)
      {
        $start = (!$input) ? '<outputXML>' : '<inputXML>';
        $end   = (!$input) ? '</outputXML>' : '</inputXML>';
        $string = " ".$string;
        $ini = strpos($string,$start);
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        $conteudo = substr($string,$ini,$len);
        $obj = simplexml_load_string(str_replace(['&lt;', '&gt;'], ['<', '>'], $conteudo));
        $array = json_decode(json_encode($obj), true);
        return $array;
      }
    public function listaNotasFiscais()
      {
        $unidadesTable = TableRegistry::get('Unidades');
        $unidades = $unidadesTable->find('all', 
          [
            'conditions' =>
              [
                'ativo' => true
              ],
            'order' =>
              [
                'ordenacao ASC'
              ]
          ])->toArray();
        $this->set('unidades', $unidades);
        $this->set('titulo', 'Lotes de notas fiscais | Aldeia Montessori');
      }
    public function sessaoListaNotas()
      {
        if($this->request->is('POST'))
          {
            $alunosTable = TableRegistry::get('Alunos');
            $lotesRPSTable = TableRegistry::get('LotesRPS');
            $this->viewBuilder()->disableAutoLayout();            
            $data       = $this->request->getData();
            $scope      = (int)$data['scope'];
            $key        = (int)$data['key'];
            $parent_key = (int)$data['parent_key'];
            $id         = $data['id'];
            $blocks     = [];
            switch($data['scope'])
              {
                case '0':
                  $lotes = $lotesRPSTable->find('all', 
                    [
                      'conditions' =>
                        [
                          'unidade' => $data['key']
                        ]
                    ])->distinct('YEAR(data_criacao)')->toArray();
                  foreach($lotes as $lote)
                    {
                      $block = 
                        [
                          'scope' => 1,
                          'key' => $lote->data_criacao->format('Y'),
                          'parent_key' => $key,
                          'parent_scope' => $scope,
                          'nome' => $lote->data_criacao->format('Y'),
                          'unique' => uniqid(),
                          'parent_id' => $id
                        ];
                      $blocks[] = $block;
                    }
                break;
                case '1':
                  $lotes = $lotesRPSTable->find('all', 
                    [
                      'conditions' =>
                        [
                          'YEAR(data_criacao)' => $key,
                          'unidade' => $parent_key
                        ]
                    ])->distinct('MONTH(data_criacao)')->toArray();
                  foreach($lotes as $lote)
                    {
                      $block =
                        [
                          'scope' => 2,
                          'key' => $lote->data_criacao->format('m_Y') . '_' . $lote->unidade,
                          'parent_key' => $key,
                          'parent_scope' => $scope,
                          'nome' => $this->meses_pt[((int)$lote->data_criacao->format('m')-1)],
                          'unique' => uniqid(),
                          'parent_id' => $id
                        ];
                      $blocks[] = $block;
                    }
                  break;
                  case '2':
                    $termos = explode('_', $data['key']);
                    $lote = $lotesRPSTable->find('all', 
                      [
                        'conditions' =>
                          [
                            'MONTH(LotesRPS.data_criacao)' => $termos[0],
                            'YEAR(LotesRPS.data_criacao)' => $termos[1],
                            'LotesRPS.unidade' => $termos[2]
                          ]
                      ])->contain(['RPS' => ['Alunos' => ['Pessoas'], 'NotasFiscais']])->first();
                    if($lote)
                      {
                        foreach($lote->rps as $rps)
                          {
                            $block =
                              [
                                'scope' => 3,
                                'key' => $rps->id,
                                'parent_key' => $key,
                                'parent_scope' => $scope,
                                'nome' => $rps->Aluno->pessoa->nome,
                                'unique' => uniqid(),
                                'parent_id' => $id,
                                'nf_lancada' => ($rps->nota_fiscal)
                              ];
                            $blocks[] = $block;
                          }
                      }
                  break;
                  case '3':
                        $blocks = 'rps';
                        $RPSTable = TableRegistry::get('RPS');
                        $rps = $RPSTable->find('all', 
                          [
                            'conditions' =>
                              [
                                'RPS.id' => $key
                              ]
                          ])->contain(['NotasFiscais', 'LotesRPS'])->first();
                        $conteudo = $this->conteudoXML(base64_decode($rps->lote->conteudo_xml), 'input');
                        foreach($conteudo['LoteRps']['ListaRps'] as $rps_xml)
                          {
                            if($rps_xml['InfRps']['IdentificacaoRps']['Numero'] == $rps->numero_sequencial)
                            {
                              $rps->conteudo_rps = $rps_xml['InfRps'];
                              break;
                            }
                          }
                        $this->set('rps', $rps);
                        $this->set('scope', 4);
                        $this->set('key', $rps->id);
                        $this->set('parent_key', $key);
                        $this->set('parent_scope', $scope);
                        $this->set('parent_id', $id);
                  break;
                }
            $this->set('blocks', $blocks);
          }
        }
    /*
      //Configure::write('debug', 2);

			//$this->autoRender = false;

			$this->layout = 'webarch';

			$this->set('nomePagina',    'Financeiro');
			$this->set('nomePaginaPos', 'Nota Fiscal Carioca');


			if($code != '44818b4b7930c99efb95322a34bc310a')
			{
				exit;
			}

			$meses    = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

			$empresas = $this->Empresa->getEmpresas();
			$emitem   = $this->AlunoEmiteNfse->get_emitem();
			$alunos   = $this->Aluno->get_alunos_emissao_nfse_lista();
			$boletos  = $this->Aluno->get_valor_com_desconto_alunos_agora();

			$valor_nfs = 0;
			$qtd_nfs = 0;
			$unidades = array();

			foreach($empresas as $key => $empresa)
			{
				// if para limitar a geração a partir de parâmetro
				if ($empresa['Empresa']['id'] < 3){
					foreach($alunos as $aluno)
					{
						if($aluno['NivelEmpresa']['empresa_id'] == $empresa['Empresa']['id'])
						{
							if(isset($emitem[$aluno['Aluno']['id']]) == true)
							{
								if(isset($boletos[$aluno['Aluno']['id']]['total']) == true)
								{
									if($boletos[$aluno['Aluno']['id']]['total'] > 0)
									{
										$responsavel = $this->Parente->get_responsavel_legal($aluno['Aluno']['id']);

										if(is_null($responsavel['Endereco']['id']) === true)
										{
											$endereco = $this->Aluno->Endereco->get_endereco_aluno($aluno['Aluno']['id']);

											if((!isset($endereco['Endereco']['id'])) || is_null($endereco['Endereco']['id']) === true)
											{
												pr(array('endereco', $aluno));
												exit;
												continue;
											}
											else
											{
												$responsavel['Endereco']    = $endereco['Endereco'];
												$responsavel['Logradouro']  = $endereco['Logradouro'];
												$responsavel['Bairro']      = $endereco['Bairro'];
												$responsavel['Cidade']      = $endereco['Cidade'];
											}
										}

										if(strlen(trim($responsavel['Endereco']['numero'])) == 0)
										{
											$responsavel['Endereco']['numero'] = '-';
										}

										if(strlen(trim($responsavel['Endereco']['complemento'])) == 0)
										{
											$responsavel['Endereco']['complemento'] = '-';
										}

										if(filter_var($responsavel['Pessoa']['email'], FILTER_VALIDATE_EMAIL) === false)
										{
											$responsavel['Pessoa']['email'] = $responsavel['Pessoa']['email_secundario'];

											if(filter_var($responsavel['Pessoa']['email'], FILTER_VALIDATE_EMAIL) === false)
											{
												$responsavel['Pessoa']['email'] = 'atendimento@aldeiamontessori.com.br';
											}
										}

										$codigo_tributacao_municipio = null;

										if($aluno['Curso']['id'] == 1 || $aluno['Curso']['id'] == 2)
										{
											$codigo_tributacao_municipio = '080101';
										}

										if($aluno['Curso']['id'] == 3)
										{
											$codigo_tributacao_municipio = '080102';
										}

										if(is_null($codigo_tributacao_municipio) === true)
										{
											pr(array('curso', $aluno));
											exit;
											continue;
										}

										$dados = array
										(
											'cpf_cnpj_tomador'              => $responsavel['Pessoa']['cpf'],
											'razao_social_tomador'          => $responsavel['Pessoa']['nome'],
											'endereco_tomador'              => $responsavel['Logradouro']['nome'],
											'numero_tomador'                => $responsavel['Endereco']['numero'],
											'complemento_tomador'           => $responsavel['Endereco']['complemento'],
											'bairro_tomador'                => $responsavel['Bairro']['nome'],
											'uf_tomador'                    => $responsavel['Cidade']['sigla_uf'],
											'cep_tomador'                   => $responsavel['Logradouro']['cep'],
											'email_tomador'                 => $responsavel['Pessoa']['email'],
											'serie'                         => 'ABC',
											'tipo'                          => '1',
											'data_emissao'                  => date('Y-m-d').' '.date('H:i:s'),
											'natureza_operacao'             => '1',
											'optante_simples_nacional'      => '1',
											'incentivador_cultural'         => '2',
											'status'                        => '1',
											'valor_servicos'                => $boletos[$aluno['Aluno']['id']]['total'],
											'iss_retido'                    => '2',
											'item_lista_servico'            => '0801',
											'codigo_tributacao_municipio'   => $codigo_tributacao_municipio,
											'discriminacao'                 => "Prestação de Serviços Educacionais para {$aluno['Aluno']['nome']}, referente ao mês de ".$meses[date('m')-1]." de ".date('Y')."  Em conformidade com a Lei 12.741/2012, empresa enquadrada no Simples Nacional alíquota ".$empresa['Empresa']['aliquota']."%.",
											'codigo_municipio'              => 3304557,
											'cnpj_prestador'                => $empresa['Empresa']['cnpj'],
											'inscricao_municipal_prestador' => $empresa['Empresa']['im'],
											'cod_aluno'                     => $aluno['Aluno']['codigo_aluno_access'],
											'pessoa_id'                     => $aluno['Aluno']['id']
										);

										$empresas[$key]['alunos'][]          = $dados;
										$empresas[$key]['quantidade_alunos'] = count($empresas[$key]['alunos']);

										//var_dump($aluno['Aluno']['id']);
										//var_dump($boletos);
										//var_dump($boletos[$aluno['Aluno']['id']]['total']);
										$valor_nfs += $boletos[$aluno['Aluno']['id']]['total'];
										$qtd_nfs++;
									}
								}
							}
						}
					}
					$unidades[] = array(
						'unidade' => $empresa['Empresa']['nome'],
						'valor' => $valor_nfs,
						'quantidade' => $qtd_nfs
					);
					$valor_nfs = 0;
					$qtd_nfs = 0;
				}
			}
    */
  }

?>