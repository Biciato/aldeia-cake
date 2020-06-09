<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;

class AppController extends Controller
  {
    public function info()
      {
        phpinfo();
        exit();
      }
    protected $escolaridade = 1;
    protected $hotelaria = 3;
    protected $tipos_boleto =
      [
        [
          'nome' => "Cota mensal de anuidade escolar",
          'sigla' => 'CMAE'
        ],
        [
          'nome' => 'Cota de composição',
          'sigla' => 'CC'
        ],
        [
          'nome' => 'Cota de reserva de vaga',
          'sigla' => 'CRV'
        ]
      ];

    protected $mapa_auxiliares = [];

    public function initialize(): void
      {
        $this->loadComponent('Auth', 
          [
                'authenticate' => [
                    'Form' => [
                        'fields' => ['username' => 'email', 'password' => 'senha'],
                        'userModel' => 'Login',
                        'passwordHasher' => 'Default',
                        'unauthorizedRedirect' => '/login',
                        'finder' => 'authenticate'
                ]
            ],
                'authorize' => 'Controller'
          ]);
        $user = $this->Auth->user();
        $this->mapa_auxiliares = 
          [
            'parentesco' =>  
              [
                'form' => 'default',
                'label' => 'Parentescos',
                'tableClass' => 'Parentescos'
              ],
            'permanencias' =>  
              [
                'form' => 
                 [
                    'rows' =>
                      [
                        [
                          'nome' => 
                            [
                              'type' => 'text',
                              'label' => 'Nome',
                              'col' => 11,
                            ],
                          'ativo' =>
                            [
                              'type' => 'status-switch',
                              'label' => 'Ativo?',
                              'col' => 1
                            ]
                        ],
                        [
                          'turnos' =>
                            [
                              'type' => 'checkbox',
                              'options_src' => [$this, 'opcoesTurnos'],
                              'label' => 'Turnos',
                              'col' => 12
                            ]
                        ]
                      ],
                    'status-switch-row' => 0
                  ],
                'label' => 'Permanências',
                'tableClass' => 'Permanencias'
              ],
            'turnos' =>  
              [
                'form' => 'default',
                'label' => 'Turnos',
                'tableClass' => 'Turnos'
              ],
            'meios_conhecimento' =>  
              [
                'form' => 'default',
                'label' => 'Meios de conhecimento',
                'tableClass' => 'MeiosConhecimento'
              ],
            'meios_atendimento' =>  
              [
                'form' => 'default',
                'label' => 'Meios de atendimento',
                'tableClass' => 'MeiosAtendimento'
              ],
            'acompanhamentos_sistematicos' =>  
              [
                'form' => 'default',
                'label' => 'Acompanhamentos Sistemáticos',
                'tableClass' => 'AcompanhamentosSistematicos'
              ],
            'unidades' => 
              [
                'form' => 
                  [
                    'rows' =>
                      [
                        [
                          'nome' => 
                            [
                              'type' => 'text',
                              'label' => 'Nome',
                              'col'   => 4
                            ],
                          'descricao' => 
                            [
                              'type' => 'text',
                              'label' => 'Descrição',
                              'col' => 4
                            ],
                          'extende' =>
                            [
                              'type'  => 'select',
                              'label' => 'Extensão de...',
                              'options_src' => [$this, 'opcoesUnidades'],
                              'col' => 3
                            ],
                          'ativo' =>
                            [
                              'type' => 'status-switch',
                              'label' => 'Ativo?',
                              'col' => 1
                            ]
                        ],
                        [
                          'razao_social' =>
                            [
                              'type' => 'text',
                              'label' => 'Razão social',
                              'col' => 3
                            ],
                          'cnpj' =>
                            [
                              'type' => 'text',
                              'label' => 'CNPJ',
                              'mask' => '99.999.999/9999-99',
                              'col' => 3
                            ],
                          'ie' =>
                            [
                              'type' => 'text',
                              'label' => 'IE',
                              'col' => 3
                            ],
                          'im' =>
                            [
                              'type' => 'text',
                              'label' => 'Im',
                              'col' => 3
                            ],
                        ],
                        [
                          'logradouro' =>
                            [
                              'type' => 'text',
                              'label' => 'Logradouro',
                              'col' => 3
                            ],
                          'numero' =>
                            [
                              'type' => 'text',
                              'label' => 'Número',
                              'col' => 3
                            ],
                          'cidade' =>
                            [
                              'type' => 'text',
                              'label' => 'Cidade',
                              'col' => 3
                            ],
                          'estado' =>
                            [
                              'type' => 'text',
                              'label' => 'Estado',
                              'col' => 3
                            ],
                        ],
                        [
                          'agencia' =>
                            [
                              'type' => 'text',
                              'label' => 'Agência',
                              'col' => 3
                            ],
                          'banco' =>
                            [
                              'type' => 'text',
                              'label' => 'Banco',
                              'col' => 3
                            ],
                          'carteira' =>
                            [
                              'type' => 'text',
                              'label' => 'Carteira',
                              'col' => 3
                            ],
                          'conta' =>
                            [
                              'type' => 'text',
                              'label' => 'Conta',
                              'col' => 3
                            ],
                        ],
                        [
                          'aliquota' =>
                            [
                              'type' => 'text',
                              'label' => 'Alíquota',
                              'col' => 3
                            ],
                          'codigo_transmissao' =>
                            [
                              'type' => 'text',
                              'label' => 'Código de transmissão',
                              'col' => 3
                            ],
                          'sigla_empresa' =>
                            [
                              'type' => 'text',
                              'label' => 'Sigla',
                              'col' => 3
                            ],
                          'descricao' =>
                            [
                              'type' => 'text',
                              'label' => 'Descrição',
                              'col' => 3
                            ],
                        ],
                        [
                          'conta_corrente_registro' =>
                            [
                              'type' => 'text',
                              'label' => 'CC registro',
                              'col' => 4
                            ],
                          'digito_verificador_conta_corrente_registro' =>
                            [
                              'type' => 'text',
                              'label' => 'Dígito verificador (cc)',
                              'col' => 2
                            ],
                          'agencia_registro' =>
                            [
                              'type' => 'text',
                              'label' => 'Agência registro',
                              'col' => 4
                            ],
                          'digito_verificador_agencia_registro' =>
                            [
                              'type' => 'text',
                              'label' => 'Dígito verificador (agência)',
                              'col' => 2
                            ],
                        ],
                        [
                          'razao_social_arquivo_retorno' =>
                            [
                              'type' => 'text',
                              'label' => 'Razão social (arquivos de retorno)',
                              'col' => 4
                            ],
                          'convenio' =>
                            [
                              'type' => 'text',
                              'label' => 'Convênio',
                              'col' => 4
                            ],
                          'codigo_beneficiario' =>
                            [
                              'type' => 'text',
                              'label' => 'Código beneficiário',
                              'col' => 4
                            ],
                        ],
                        [
                          'agrupamentos' =>
                            [
                              'type' => 'checkbox',
                              'label' => 'Agrupamentos',
                              'options_src' => [$this, 'opcoesAgrupamentos'],
                              'col' => 12
                            ],
                        ]
                      ],
                    'status-switch-row' => 0
                  ],
                'label' => 'Unidades',
                'tableClass' => 'Unidades'
              ],
            'agrupamentos' =>
              [
                'form' => 
                  [
                    'rows' =>
                      [
                        [
                          'nome' => 
                            [
                              'type' => 'text',
                              'label' => 'Nome',
                              'col' => 5
                            ],
                          'idade_inicial' => 
                            [
                              'type' => 'number',
                              'label' => 'Idade Inicial (meses)',
                              'col' => 3
                            ],
                          'idade_final' => 
                            [
                              'type' => 'number',
                              'label' => 'Idade Final (meses)',
                              'col' => 3
                             ],
                          'ativo' =>
                            [
                              'type' => 'status-switch',
                              'label' => 'Ativo?',
                              'col' => 1
                            ]
                        ],
                        [
                          'niveis' =>
                            [
                              'type' => 'checkbox',
                              'label' => 'Níveis',
                              'options_src' => [$this, 'opcoesNiveis'],
                              'col' => 12
                            ]
                        ],
                      ],
                    'status-switch-row' => 0
                  ],
                'label' => 'Agrupamentos',
                'tableClass' => 'Agrupamentos'
              ],
            'horarios' =>
              [
                'form' => 
                  [
                    'rows' =>
                      [
                        [
                          'horario_entrada' => 
                            [
                              'type' => 'text',
                              'label' => 'Horário de entrada',
                              'col' => 6,
                              'mask' => '99:99',
                              'field-val' => 'horario_entrada_formatado'
                            ],
                          'horario_saida' => 
                            [
                              'type' => 'text',
                              'label' => 'Horário de saída',
                              'col' => 5,
                              'mask' => '99:99',
                              'field-val' => 'horario_saida_formatado'
                             ],
                          'ativo' =>
                            [
                              'type' => 'status-switch',
                              'label' => 'Ativo?',
                              'col' => 1
                            ]
                        ]
                      ],
                    'status-switch-row' => 0
                  ],
                'label' => 'Horários',
                'tableClass' => 'Horarios'
              ],
            'tipos_interacao' =>  
              [
                'form' => 'default',
                'label' => 'Tipos de interação',
                'tableClass' => 'TiposInteracao'
              ],
            'funcoes_colaboradores' => 
              [
                'form' => 'default',
                'label' => 'Funções de colaboradores',
                'tableClass' => 'FuncoesColaboradores'
              ],
            'estados' => 
              [
                'form' => 
                 [
                    'rows' =>
                      [
                        [
                          'nome' => 
                            [
                              'type' => 'text',
                              'label' => 'Nome',
                              'col' => 8,
                            ],
                          'sigla' => 
                            [
                              'type' => 'text',
                              'label' => 'Sigla',
                              'col' => 3,
                            ],
                          'ativo' =>
                            [
                              'type' => 'status-switch',
                              'label' => 'Ativo?',
                              'col' => 1
                            ]
                        ]
                      ],
                    'status-switch-row' => 0
                  ],
                'label' => 'Estados',
                'tableClass' => 'Estados'
              ],
            'nacionalidades' => 
              [
                'form' => 'default',
                'label' => 'Nacionalidades',
                'tableClass' => 'Nacionalidades'
              ],
            'cores' => 
              [
                'form' => 'default',
                'label' => 'Cores',
                'tableClass' => 'Cores'
              ],
            'estados_civis' => 
              [
                'form' => 'default',
                'label' => 'Estados civis',
                'tableClass' => 'EstadosCivis'
              ],
            'niveis' =>
              [
                'form' => 'default',
                'label' => 'Níveis',
                'tableClass' => 'Niveis'
              ],
            'cursos' =>  
              [
                'form' => 
                 [
                    'rows' =>
                      [
                        [
                          'nome' => 
                            [
                              'type' => 'text',
                              'label' => 'Nome',
                              'col' => 11,
                            ],
                          'ativo' =>
                            [
                              'type' => 'status-switch',
                              'label' => 'Ativo?',
                              'col' => 1
                            ]
                        ],
                        [
                          'agrupamentos' =>
                            [
                              'type' => 'checkbox',
                              'options_src' => [$this, 'opcoesAgrupamentos'],
                              'label' => 'Agrupamentos',
                              'col' => 12
                            ]
                        ],
                      ],
                    'status-switch-row' => 0
                  ],
                'label' => 'Cursos',
                'tableClass' => 'Cursos'
              ],
            'servicos' =>  
              [
                'form' => 
                 [
                    'rows' =>
                      [
                        [
                          'nome' => 
                            [
                              'type' => 'text',
                              'label' => 'Nome',
                              'col' => 8,
                            ],
                          'obrigatorio' =>
                            [
                              'type'  => 'select',
                              'label' => 'Serviço obrigatório?',
                              'options_src' => [$this, "opcoesObrigatorio"],
                              'col' => 3
                            ],
                          'ativo' =>
                            [
                              'type' => 'status-switch',
                              'label' => 'Ativo?',
                              'col' => 1
                            ]
                        ],
                      ],
                    'status-switch-row' => 0
                  ],
                'label' => 'Serviços',
                'tableClass' => 'ServicosAux'
              ],
           ];
          ksort($this->mapa_auxiliares);
        $this->mapa_modulos = 
          [
            [
              'controller' => 'dashboard',
              'action' => 'index'
            ],
            [
              'controller' => 'documentos',
              'action' => 'index'
            ],
            [
              'controller' => 'documentos',
              'action' => 'ver'
            ],
            [
              'controller' => 'prospects',
              'action' => 'index',
              'alias' => 'prospects/lista'
            ],
            [
              'controller' => 'prospects',
              'action' => 'novo'
            ],
            [
              'controller' => 'prospects',
              'action' => 'interacoes'
            ],
            [
              'controller' => 'alunos',
              'action' => 'index',
              'alias' => 'alunos/lista'
            ],
            [
              'controller' => 'alunos',
              'action'     => 'novo'
            ],
            [
              'controller' => 'servicos',
              'action' => 'index',
              'alias' => 'servicos/lista'
            ],
            [
              'controller' => 'servicos',
              'action'     => 'novo'
            ],
            [
              'controller' => 'colaboradores',
              'action' => 'novo'
            ],
            [
              'controller' => 'colaboradores',
              'action' => 'index',
              'alias' => 'colaboradores/lista'
            ],
            [
              'controller' => 'configuracoes',
              'action' => 'configurar',
              'alias' => 'configurar/auxiliar'
            ],
          ];
        if(($user)&&(!$this->request->is('POST'))&&(strtolower($this->request->getParam('action')) != 'thumb'))
          {
            $this->set('auxiliares', $this->mapa_auxiliares);
            $this->set('menu_permissions', $this->menuPermissions());
            $this->set('user', $user);
            $notificacoesTable = TableRegistry::get('Notificacoes');
            $notificacoes = $notificacoesTable->find('all')->where(
              [
                'usuario' => $user['id']
              ])->toArray();
            $this->set('notificacoes_usuario', $notificacoes);
            $naoLidas = $notificacoesTable->find('all')->where(
              [
                'usuario' => $user['id'],
                'lida IS NULL'
              ])->count();
            $this->set('naoLidas', $naoLidas);
          }
      }
    protected $mapa_menu =
          [
            [
              'type' => 'single',
              'controller' => 'dashboard',
              'action' => 'index',
            ],
            [
              'type' => 'single',
              'controller' => 'documentos',
              'action' => 'index',
            ],
            [
              'type' => 'multiple',
              'submenu' => 
                [
                  [
                    'type' => 'single',
                    'controller' => 'prospects',
                    'action' => 'index',
                  ],
                  [
                    'type' => 'single',
                    'controller' => 'prospects',
                    'action' => 'novo',
                  ],
                  [
                    'type' => 'single',
                    'controller' => 'prospects',
                    'action' => 'interacoes',
                  ],
                ]
            ],
            [
              'type' => 'multiple',
              'submenu' => 
                [
                  [
                    'type' => 'single',
                    'controller' => 'colaboradores',
                    'action' => 'index',
                  ],
                  [
                    'type' => 'single',
                    'controller' => 'colaboradores',
                    'action' => 'novo',
                  ],
                ]
            ],
            [
              'type' => 'multiple',
              'submenu' => 
                [
                  [
                    'type' => 'single',
                    'controller' => 'servicos',
                    'action' => 'index',
                  ],
                  [
                    'type' => 'single',
                    'controller' => 'servicos',
                    'action' => 'novo',
                  ],
                ]
            ],
            [
              'type' => 'multiple',
              'submenu' => 
                [
                  [
                    'type' => 'single',
                    'controller' => 'alunos',
                    'action' => 'index',
                  ],
                  [
                    'type' => 'single',
                    'controller' => 'alunos',
                    'action' => 'novo',
                  ],
                ]
            ],
            [
              'type' => 'multiple',
              'submenu' => 
                [
                  [
                    'type' => 'config_module',
                    'controller' => 'configuracoes',
                    'action' => 'configurar'
                  ]
                ]
            ],
          ];
    public function booleanVal()
     {
        return [0 => 'Não', 1 => 'Sim'];
     } 
    public function opcoesObrigatorio()
      {
        return [0 => 'Não', 1 => 'Sim', 2 => 'Sistema creche'];
      }
    public function thumb($dim, $id = false)
      {
        $pessoasTable = TableRegistry::get('Pessoas');
        if(!$id)
          {
            $user = $this->Auth->user();
            $id = $user['pessoa_id'];
          }
        $pessoa = $pessoasTable->get($id);
        if($pessoa->caminho_arquivo_avatar)
          {
            $image = new \Imagick($pessoa->caminho_arquivo_avatar);
            $image->cropThumbnailImage($dim, $dim);
            $mime = mime_content_type($pessoa->caminho_arquivo_avatar);
            $this->response = $this->response->withType($mime);
            $this->response = $this->response->withStringBody($image->getImageBlob());
            return $this->response;
          }
        else
          {
            throw new NotFoundException("Avatar não encontrado", 404);
          }
      }
    protected function menuPermissions()
      {
        $menu = $this->mapa_menu;
        $user = $this->Auth->user();
        $permissions = [];
        if($user)
          {
            foreach($menu as $key => $item)
              {
                if($user['modulos_acesso'] == "*")
                  {
                    $permissions[$key] = true;
                  }
                else if($item['type'] == 'single')
                  {
                    $url = strtolower($item['controller'] . "/" . $item['action']);
                    $permissions[$key] = in_array($url, $user['modulos_acesso_array']);
                  }
                else if($item['type'] == 'multiple')
                  {
                    $qtd = count($item['submenu']);
                    $sub_permissions = [];
                    $denied = 0;
                    foreach($item['submenu'] as $sub_key => $sub_item)
                      {
                        $url = strtolower($sub_item['controller'] . "/" . $sub_item['action']);
                        if(in_array($url, $user['modulos_acesso_array']))
                          {
                            $sub_permissions[$sub_key] = true;
                            continue;
                          }
                        $sub_permissions[$sub_key] = false;
                        $denied++;
                      }
                    $p = ($qtd == $denied) ? false : $sub_permissions;
                    $p = ($qtd == (count($sub_permissions) - $denied)) ? true : $p;
                    $permissions[$key] = $p;
                  }
                else if($item['type'] == 'config_module')
                  {
                    $url = strtolower($item['controller'] . "/" . $item['action']);
                    $permissions[$key] = in_array($url, $user['modulos_acesso_array']);
                  }
              }
          }
        return $permissions;
      }
    protected function config($aux_array)
      {
        $config = [];
        foreach($aux_array as $tableClass => $var)
          {
            $table   = TableRegistry::get($tableClass);
            if(is_string($var))
              {
                $opts = ['keyField' => 'id', 'valueField' => 'nome', 'conditions' => ['ativo' => true]];
                if(in_array($table->getTable(), array_keys($this->mapa_auxiliares)))
                  {
                    $opts['order'] = 'ordenacao ASC, id ASC';
                  }
                $results = $table->find('list', $opts)->toArray();
                $config[$var] = $results;
              }
            else
              {
                $config[$var['varName']] = call_user_func([$this, $var['callable']]);
              }
          }
        return $config;
      }
    public function opcoesAgrupamentos()
      {
        $agrupamentosTable = TableRegistry::get('Agrupamentos');
        return $agrupamentosTable->find('list', ['keyField' => 'id', 'valueField' => 'nome'])->toArray(); 
      }
    public function opcoesNiveis()
      {
        $niveisTable = TableRegistry::get('Niveis');
        return $niveisTable->find('list', ['keyField' => 'id', 'valueField' => 'nome'])->toArray(); 
      }
     public function opcoesTurnos()
      {
        $turnosTable = TableRegistry::get('Turnos');
        return $turnosTable->find('list', ['keyField' => 'id', 'valueField' => 'nome'])->toArray(); 
      }
    public function opcoesHorarios()
      {
        $horariosTable = TableRegistry::get('Horarios');
        return $horariosTable->find('list', ['keyField' => 'id', 'valueField' => 'nome'])->toArray(); 
      }
    public function opcoesUnidades()
      {
        $unidadesTable = TableRegistry::get('Unidades');
        return $unidadesTable->find('list', ['keyField' => 'id', 'valueField' => 'nome'])->toArray(); 
      }
    private function modulos_permissionados()
      {
        $modulos = $this->mapa_modulos;
        $arr = [];
        foreach($modulos as $m)
          {
            array_push($arr, strtolower($m['controller'] . "/" . $m['action']));
          }
        return $arr;
      }
    public function isAuthorized($user = null)
      {
        if((is_array($user))&&(strtolower($this->request->getParam('action')) != 'thumb'))
          {
            if($user['modulos_acesso'] == '*')
              {
                return true;
              }       
            $modulos = json_decode($user['modulos_acesso'], true);
            if(is_array($modulos))
              {
                $modulo_str = $this->request->getParam('controller') . "/" . $this->request->getParam('action');
                if(!in_array(strtolower($modulo_str), $this->modulos_permissionados()))
                  {
                    return true;
                  }
                if(!in_array(strtolower($modulo_str), $modulos))
                  {
                    $this->response = $this->response->withStatus(403);
                    throw new ForbiddenException("Você não tem permissão para acessar essa página", 403);
                  }
                return true;
              }
            throw new ForbiddenException("Você não tem permissão para acessar essa página", 403);
          }
        return true;
      }
    public function resultadosCursosEntities()
      {
        $cursosTable = TableRegistry::get('Cursos');
        return $cursosTable->find('all', 
          [
            'order' =>  
              [
                'Cursos.ordenacao ASC',
              ]
          ])->toArray();
      }
    public function resultadosTurnosEntities()
      {
        $turnos = TableRegistry::get('Turnos');
        return $turnos->find('all', 
          [
            'order' =>  
              [
                'Turnos.ordenacao ASC',
              ]
          ])->toArray();
      }
    public function numeroServicos($servico_aux = null, $unidade = null, $curso = null, $agrupamento = null, $nivel = null, $turno = null, $permanencia = null, $horario = null, $counting = true)
      {
        $conds = 
          [

          ];
        if($servico_aux)
          {
            $conds['servico'] = $servico_aux;
          }
        if($unidade)
          {
            $conds['unidade'] = $unidade;
          }
        if($curso)
          {
            $conds['curso'] = $curso;
          }
        if($agrupamento)
          {
            $conds['agrupamento'] = $agrupamento;
          }
        if($nivel)
          {
            $conds['nivel'] = $nivel;
          }
        if($turno)
          {
            $conds['turno'] = $turno;
          }
        if($permanencia)
          {
            $conds['permanencia'] = $permanencia;
          }
        if($horario)
          {
            $conds['horario'] = $horario;
          }
        $servicosTable = TableRegistry::get('Servicos');
        $servicos = $servicosTable->find('all', ['conditions' => $conds])->toArray();
        return ($counting) ? count($servicos) : $servicos;
      }
    /*##########################################################################################################*/
    /*################## PASSAR AS COISAS PARA CLASSE DE COMMAND DEPOIS QUE TIVER PRONTO #######################*/
    /*##########################################################################################################*/
    /* Dependências:
      use Cake\ORM\TableRegistry;
      use Cake\Mailer\Email;
    */
    public function notificarInteracoesDia()
      {
        $interacoesTable = TableRegistry::get('Interacoes');
        $notificacoesTable = TableRegistry::get('Notificacoes');
        $now = new \DateTime();
        $interacoesANotificar = $interacoesTable->find('all', 
          [
            'conditions' =>
              [
                'concluida' => false, 
                'data' => $now->format('Y-m-d'),
              ]
          ])->contain(
          [
            'Prospects' =>
              [
                'Pessoas'
              ],
            'Responsaveis' =>
              [
                'Pessoas' => 
                  [
                    'Login'
                  ]
              ]
          ])->toArray();
          $this->set('interacoesANotificar', $interacoesANotificar);
        $notificacoes = [];
        foreach($interacoesANotificar as $interacao)
          {
            $existente = $notificacoesTable->find('all', 
              [
                'conditions' =>
                  [
                    'tipo' => 1,
                    'metadata' => '{"interacao":' . $interacao->id . '}'
                  ]
              ])->count();
            if(!$existente)
              {
                $notificacao = 
                  [
                    'tipo' => 1,
                    'metadata' => json_encode(
                      [
                        'interacao' => $interacao->id,
                      ]),
                    'multiplos_usuarios' => 0,
                    'usuario' => $interacao->responsavel->pessoa->login->id,
                    'titulo' => 'Interação hoje',
                    'texto' => 'Você tem uma interação agendada para hoje',
                  ];
                $notificacoes[] = $notificacoesTable->newEntity($notificacao);
              }
          }
        if(count($notificacoes))
          {
            $notificacoesTable->saveMany($notificacoes);
          }
        return $this->render('/Dashboard/index');
      }
    public function resultadosBooleanSelectbox()
      {
        return [0 => 'Não', 1 => 'Sim'];
      }
    public function resultadosModulos()
      {
        $modulos = $this->mapa_modulos;
        $opcoes = [];
        foreach($modulos as $modulo)
          {
            if(isset($modulo['alias']))
              {
                $label = $modulo['alias'];
              }
            else
              {
                $action = ($modulo['action'] == 'index') ? '' : "/" . $modulo['action'];
                $label = '/' . $modulo['controller'] . $action;
              }
            $opcoes[$modulo['controller'] . "/" . $modulo['action']] = $label; 
          }
        return $opcoes;
      }
    private $email_interaction_type = 2;
    public function processarInteracoes()
      {
        $interacoesTable = TableRegistry::get('Interacoes');
        $now = new \DateTime();
        $interacoesAProcessar = $interacoesTable->find('all', 
          [
            'conditions' =>
              [
                'tipo' => $this->email_interaction_type,
                'concluida' => false,
                'OR' =>
                  [
                      [
                        'data <' => $now->format('Y-m-d')  
                      ],
                    'AND' =>
                      [
                        'data' => $now->format('Y-m-d'),
                        'hora <=' => $now->format('H:i:s') 
                      ]
                  ]
              ]
          ])->contain(
          [
            'Prospects' => 
              [
                'Pessoas', 
                'Parentes' => 
                  [
                    'Pessoas'
                  ]
              ]
          ])->toArray();
        foreach($interacoesAProcessar as $interacao)
          {
            $envio = $this->enviarEmailInteracao($interacao);
            if($envio)
              {
                $interacao->concluida = true;
                $interacoesTable->save($interacao);
              }
          }
        return $this->render('/Dashboard/index');
      }
    private function enviarEmailInteracao(&$interacao)
      {
        $email = new Email('default');
        $email->viewBuilder()->setTemplate('mensagem_interacao');
        $email->addBcc('vinicius@aigen.com.br')
          ->setEmailFormat('html')
          ->setSubject($interacao->titulo)
          ->setViewVars(['interacao' => $interacao])
          ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
        $parentes = [];
        foreach($interacao->prospect->parentes as $parente)
          {
            if($parente->notificacoes)
              {
                array_push($parentes, $parente->pessoa->nome  . " (" . $parente->pessoa->email . ")");
                $email->addTo([$parente->pessoa->email => $parente->pessoa->nome]);
              }
          }
        if(count($parentes))
          {
            $plural = (count($parentes) > 1) ? "s" : "";
            $interacao->informacao = "Email enviado automaticamente pelo sistema para " . count($parentes) . " parente" . $plural . "|Parente" . $plural . ": " . implode(', ', $parentes) . "|Timestamp: " . date('d/m/Y H:i:s');
          }
        if($interacao->caminho_arquivo)
          {
            $email->attachments([$interacao->titulo_arquivo => $interacao->caminho_arquivo]);
          }
        return (bool) $email->send();
      } 
    private function formatarData($data, $padrao_br = false)
      {
        $data_pieces = 
          [
            'Y' => substr($data, 4, 4),
            'm' => substr($data, 2, 2),
            'd' => substr($data, 0, 2),
          ];
        if(checkdate($data_pieces['m'], $data_pieces['d'], $data_pieces['Y']))
          {
            return (!$padrao_br) ? implode('-', $data_pieces) : implode('/', array_reverse($data_pieces));
          } 
        return false;
      }  
    public function baixaBoletos()
      {
        $this->autoRender = false; 
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
        $arquivos = scandir($arquivos_retorno_path->novos);
        foreach($arquivos as $arquivo)
          {
            if(in_array($arquivo, ['.', '..']))
              {
                continue;
              }
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
                              }
                          }
                      }
                  } 

              }
            rename($arquivos_retorno_path->novos . DS . $arquivo, $arquivos_retorno_path->lidos . DS . $arquivo);
          }
      }
    /*##########################################################################################################*/
    /*##########################################################################################################*/
    /*##########################################################################################################*/
  }
?>
