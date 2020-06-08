<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;

class TurmasController extends AppController
  {
    public function initialize(): void
      {
        parent::initialize();
        $user = $this->Auth->user();
        $this->set('logged', (bool)$user);
      }
    public function index()
      {
        $this->set('titulo', 'Listar turmas | Aldeia Montessori');
        $turmasTable = TableRegistry::get('Turmas');
        $alunosTable = TableRegistry::get('Alunos');
        $anos        = $turmasTable->find('all')->distinct(['ano_letivo'])->order(['ano_letivo DESC'])->toArray();
        $todas       = $turmasTable->find('all')->toArray();
        $alunos_ano  = [];
        foreach($todas as $turma)
          {
            if(!isset($alunos_ano[$turma->ano_letivo]))
              {
                $alunos_ano[$turma->ano_letivo] = [];
              }
            $alunos_turma = $alunosTable->find('list', 
              [
                'conditions' => 
                  [
                    'Alunos.turmas LIKE "%\"' . $turma->servico . '\":\"' . $turma->id . '\"%"'
                  ],
                'keyField' => 'id',
                'valueField' => 'id'
              ])->toArray();
            foreach($alunos_turma as $id)
              {
                if(!in_array($id, $alunos_ano[$turma->ano_letivo]))
                  {
                    array_push($alunos_ano[$turma->ano_letivo], $id);
                  }
              }
          }
        $this->set('alunos_ano', $alunos_ano);
        $this->set('anos', $anos);
      }
    private $natacao = 
      [
        'nome' => 'Natação',
        'servicos' => 
          [
            5,
            9,
            10,
            11
          ],
        'servico_principal' => 5
      ];
    public function sessaoListaTurmas()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $alunosTable = TableRegistry::get('Alunos');
            $turmasTable = TableRegistry::get('Turmas');
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
                  $turmas         = $turmasTable->find('all', 
                    [
                      'conditions' =>
                        [
                          'Turmas.ano_letivo' => $key
                        ]
                    ])->distinct(['unidade'])->contain(['Unidades'])->order(['Unidades.ordenacao ASC'])->toArray();
                  $todas          = $turmasTable->find('all',
                    [
                      'conditions' =>
                        [
                          'Turmas.ano_letivo' => $key
                        ]
                    ])->toArray();
                  $alunos_unidade = [];
                  foreach($todas as $turma)
                    {
                      if(!isset($alunos_unidade[$turma->unidade]))
                        {
                          $alunos_unidade[$turma->unidade] = [];
                        }
                      $alunos_turma = $alunosTable->find('list', 
                        [
                          'conditions' => 
                            [
                              'Alunos.turmas LIKE "%\"' . $turma->servico . '\":\"' . $turma->id . '\"%"'
                            ],
                          'keyField' => 'id',
                          'valueField' => 'id'
                        ])->toArray();
                      foreach($alunos_turma as $aluno)
                        {
                          if(!in_array($aluno, $alunos_unidade[$turma->unidade]))
                            {
                              array_push($alunos_unidade[$turma->unidade], $aluno);
                            }
                        }
                    }
                  foreach($turmas as $turma)
                    {
                      $block =
                        [
                          'scope' => 1,
                          'key' => $turma->Unidade->id,
                          'parent_key' => $key,
                          'parent_scope' => $scope,
                          'nome' => $turma->Unidade->nome,
                          'unique' => 'ano_' . $data['key'] . '_unidade_' . $turma->Unidade->id,
                          'parent_id' => $id,
                          'quantity' => count($alunos_unidade[$turma->unidade])
                        ];
                      $blocks[] = $block;
                    }
                break;
                case '1':
                  $turmas = $turmasTable->find('all',
                    [
                      'conditions' =>
                        [
                          'unidade' => $key,
                          'ano_letivo' => $parent_key
                        ],
                      'order' => 
                        [
                          'ServicosAux.nome ASC'
                        ]
                    ])->distinct(['servico'])->contain(['ServicosAux']);
                  $todas          = $turmasTable->find('all',
                    [
                      'conditions' =>
                        [
                          'unidade' => $key,
                          'ano_letivo' => $parent_key
                        ]
                    ])->contain(['ServicosAux'])->toArray();
                  $alunos_servico = [];
                  foreach($todas as $turma)
                  {
                      $servico = (in_array($turma->Servico->id, $this->natacao['servicos'])) ? $this->natacao['servico_principal'] : $turma->Servico->id;
                      if(!isset($alunos_servico[$servico]))
                        {
                          $alunos_servico[$servico] = [];
                        }
                      $alunos_turma = $alunosTable->find('list', 
                        [
                          'conditions' => 
                            [
                              'Alunos.turmas LIKE "%\"' . $turma->Servico->id . '\":\"' . $turma->id . '\"%"'
                            ],
                          'keyField' => 'id',
                          'valueField' => 'id'
                        ])->toArray();
                      foreach($alunos_turma as $aluno)
                        {
                          if(!in_array($aluno, $alunos_servico[$servico]))
                            {
                              array_push($alunos_servico[$servico], $aluno);
                            }
                        }
                    }
                  
                    $natacao_adicionada = false;
                    foreach($turmas as $turma)
                      {
                        if((!$natacao_adicionada)&&(in_array($turma->Servico->id, $this->natacao['servicos'])))
                          {
                            $nome       = $this->natacao['nome'];
                            $servico_id = $this->natacao['servico_principal'];
                            $natacao_adicionada = true;
                          }
                        elseif(!in_array($turma->Servico->id, $this->natacao['servicos']))
                          {
                            $nome       = $turma->Servico->nome;
                            $servico_id = $turma->Servico->id;
                          }
                        else
                          {
                            continue;
                          }
                        $block =
                          [
                          'scope' => 2,
                          'key' => $servico_id,
                          'parent_key' => $key,
                          'parent_scope' => $scope,
                          'nome' => $nome,
                          'unique' => 'ano_' . $parent_key . '_unidade_' . $key . '_servico_' . $servico_id,
                          'parent_id' => $id,
                          'quantity' => count($alunos_servico[$servico_id])
                          ];
                        $blocks[] = $block;
                      }
                break;
                case '2':
                  $pedacos = explode('_', $id);
                  if($pedacos[5] != $this->natacao['servico_principal'])
                    {
                      $conds = 
                        [
                          'ano_letivo' => $pedacos[1],
                          'unidade' => $pedacos[3],
                          'servico' => $pedacos[5]
                        ];
                    }
                  else
                    {
                      $conds = 
                        [
                          'ano_letivo' => $pedacos[1],
                          'unidade' => $pedacos[3],
                          'servico IN(' . implode(', ' ,$this->natacao['servicos']) . ')'  
                        ];
                    }
                  $turmas  = $turmasTable->find('all', 
                    [
                      'conditions' => $conds
                    ])->toArray();
                  foreach($turmas as $turma)
                    {
                      $quantidade_alunos = [];
                      $alunos_turma = $alunosTable->find('list', 
                        [
                          'conditions' => 
                            [
                              'Alunos.turmas LIKE "%\"' . $turma->servico . '\":\"' . $turma->id . '\"%"'
                            ],
                          'keyField' => 'id',
                          'valueField' => 'id'
                        ])->toArray();
                      foreach($alunos_turma as $aluno)
                        {
                          if(!in_array($aluno, $quantidade_alunos))
                            {
                              array_push($quantidade_alunos, $aluno);
                            }
                        }
                      $block =
                        [
                        'scope' => 3,
                        'key' => $turma->id,
                        'parent_key' => $key,
                        'parent_scope' => $scope,
                        'nome' => $turma->nome,
                        'unique' => uniqid(),
                        'parent_id' => $id,
                        'quantity' => count($quantidade_alunos) . ' (' . ((int)$turma->quantidade_vagas - count($quantidade_alunos)) . ')'
                        ];
                      $blocks[] = $block;
                    }
                break;
                case '3':
                  $blocks = 'turma';
                  $turma = $turmasTable->find("all", 
                    [
                      'conditions' =>
                        [
                          'Turmas.id' => $key 
                        ]
                    ])->first();
                  $alunos = $alunosTable->find('all', 
                    [
                      'conditions' => 
                        [
                          'Alunos.turmas LIKE "%\"' . $turma->servico . '\":\"' . $turma->id . '\"%"'
                        ],
                      'order' => 
                        [
                          'Pessoas.nome'
                        ]
                    ])->contain(['Pessoas'])->toArray();
                  $this->set('update', $turma);
                  $this->set('alunos', $alunos);
                  $this->set('parent_id', $id);
                  $this->set('parent_key', $key);
                  $this->set($this->opcoesFormTurmas());
                break;
              }
            $this->set('blocks', $blocks);
          }
      }
    public function listaAlunos($turma_id)
      {
        $this->viewBuilder()->disableAutoLayout();
        $turmasTable = TableRegistry::get('Turmas');
        $alunosTable = TableRegistry::get('Alunos');
        $turma       = $turmasTable->find('all', 
          [
            'conditions' =>
              [
                'Turmas.id' => $turma_id
              ]
          ])->contain(['Unidades', 'ServicosAux'])->first();
        $alunos      = $alunosTable->find('all', 
          [
            'conditions' => 
              [
                'Alunos.turmas LIKE "%\"' . $turma->servico . '\":\"' . $turma->id . '\"%"'
              ],
            'order' => 
              [
                'Pessoas.nome'
              ]
          ])->contain(['Pessoas', 'Niveis', 'Cursos', 'Agrupamentos', 'Permanencias', 'Horarios', 'Turnos'])->toArray();
        $this->set('alunos', $alunos);
        $this->set('turma', $turma);
      }
    public function listaFrequencia($ano, $mes, $turma_id)
      {
        $this->viewBuilder()->disableAutoLayout();
        $turmasTable = TableRegistry::get('Turmas');
        $alunosTable = TableRegistry::get('Alunos');
        $colaboradoresTable = TableRegistry::get('Colaboradores');
        $turma = $turmasTable->find('all', 
          [
            'conditions' =>
              [
                'Turmas.id' => $turma_id
              ]
          ])->contain(['Unidades', 'ServicosAux'])->first();
        $alunos      = $alunosTable->find('all', 
          [
            'conditions' => 
              [
                'Alunos.turmas LIKE "%\"' . $turma->servico . '\":\"' . $turma->id . '\"%"'
              ],
            'order' => 
              [
                'Pessoas.nome'
              ]
          ])->contain(['Pessoas'])->toArray();
        $colaboradores = [];
        foreach($turma->colaboradores_array as $colaborador)
          {
            $colaboradores[$colaborador] = $colaboradoresTable->find('all', 
              [
                'conditions' => 
                  [
                    'Colaboradores.id' => $colaborador
                  ]
              ])->contain(['Pessoas'])->first();
          }
        $this->set('turma', $turma);
        $this->set('alunos', $alunos);
        $this->set('colaboradores', $colaboradores);
        $this->set('mes', $mes);
        $this->set('ano', $ano);
      }
    public function nova()
      {
        $this->set('titulo', 'Nova turma | Aldeia Montessori');
        $this->set($this->opcoesFormTurmas());
      }
    private function opcoesFormTurmas()
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
        $servicosTable = TableRegistry::get('ServicosAux');
        $servicos = $servicosTable->find('all', 
          [
            'conditions' => 
              [
                'ativo' => true,
                'id !=' => 2,
              ],
            'order' => 
              [
                'ordenacao ASC',
              ]
          ])->toArray();
        return compact('unidades', 'servicos');
      }
    public function opcoesColaboradores()
      {
        if($this->request->is('GET'))
          {
            $data = $this->request->getQueryParams();
            $colaboradoresTable = TableRegistry::get('Colaboradores');
            $colaboradores = $colaboradoresTable->find('all', 
              [
                'conditions' => 
                  [
                    'Pessoas.nome LIKE "%' . $data['q'] . '%"'
                  ]
              ])->contain(['Pessoas'])->toArray();
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['results' => $colaboradores]));
            return $this->response;
          }
      }
    public function inserir()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $turmasTable = TableRegistry::get('Turmas');
            $data['ano_letivo'] = date('Y');
            if(!isset($data['id']))
              {
                $turma = $turmasTable->newEntity($data);
              }
            else
              {
                $turma = $turmasTable->get($data['id']);
                $turma = $turmasTable->patchEntity($turma, $data, ['associated' => []]);
              }
            $errors = $turma->getErrors();
            $this->response = $this->response->withType('application/json');
            if(count($errors) > 0)
              {
                $this->response = $this->response->withStringBody(json_encode(
                  [
                    'success' => false,
                    'errors' => $errors
                  ]
                ));
                return $this->response;
              }
              else
              {
                $success = false;
                $turma->colaboradores = json_encode($data['colaboradores']);
                $turma->dias_semana   = json_encode($data['dias_semana']);
                if($turmasTable->save($turma))
                  {
                    $success = true;
                  }
                $this->response = $this->response->withStringBody(json_encode(['success' => $success]));
                return $this->response;
              }
          }
      }
    public function selecionarColaborador($colaborador_id)
      {
        if($this->request->is('GET'))
          {
            $colaboradoresTable = TableRegistry::get('Colaboradores');
            $colaborador = $colaboradoresTable->find('all', 
              [
                'conditions' =>
                  [
                    'Colaboradores.id' => $colaborador_id
                  ]
              ])->contain(['Pessoas'])->first();
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(
              [
                'pessoa' => ['nome' => $colaborador->pessoa->nome],
                'id'   => $colaborador->id
              ]));
            return $this->response;
          }
      }
  }