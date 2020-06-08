<?php 
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;
use Cake\Mailer\Email;
use Cake\Log\Log;

class AlunosController extends AppController
  {
  	public function initialize(): void
      {
      	parent::initialize();
       	$user = $this->Auth->user();
        $this->set('logged', (bool)$user);
      }
    public function index()
      {
        $this->set('titulo', "Lista de alunos | Aldeia Montessori");
        $alunosTable = TableRegistry::get('Alunos');
        $alunos = $alunosTable->find('completo', 
          [
            'conditions' => false
          ])->toArray();
          $pessoasTable = TableRegistry::get('Pessoas');
          $pessoas = $pessoasTable->find('all')->contain(['Alunos', 'Colaboradores'])->where(['OR' => ['Alunos.id IS NOT NULL', 'Colaboradores.id IS NOT NULL']])->order(['Pessoas.nome ASC'])->toArray();
          $lista_pessoas = [];
          foreach($pessoas as $pessoa)
            {
              $nome = $pessoa->nome;
              if($pessoa->aluno)
                {
                  foreach($pessoa->aluno->turmas_entities as $turma)
                    {
                      if($turma->servico == $this->escolaridade)
                        {
                          $nome .= " T" . str_pad($turma->nome, 3, "0", STR_PAD_LEFT);
                        }
                        if($turma->servico == $this->hotelaria)
                        {
                          $nome .= " SC" . str_pad($turma->nome, 3, "0", STR_PAD_LEFT);
                        }
                    }
                }
              array_push($lista_pessoas, $nome);
            }
          $tagsTable = TableRegistry::get('Tags');
          $tags = $tagsTable->find('list', ['keyField' => 'id', 'valueField' => 'nome'])->toArray();
          $lista_tags = [];
          foreach($tags as $tag)
            {
              array_push($lista_tags, $tag);
            }
          $this->set('lista_tags', $lista_tags);
          $this->set('lista_pessoas', $lista_pessoas);
        $this->set('alunos', $alunos);
        $this->set('config', $this->alunosConfig());
      }
    public function novo()
      {
        $this->set('config', $this->alunosConfig());
        $this->set('titulo', "Novo aluno | Aldeia Montessori");
      }
    private $alunos_aux = 
      [
        'Parentescos' => 'parentescos',
        'Nacionalidades' => 'nacionalidades',
        'Estados' => 'naturalidades',
        'Unidades' => 'unidades',
        'Cursos' => ['callable' => 'resultadosCursosEntities', 'varName' => 'cursos'],
        'Turnos' => ['callable' => 'resultadosTurnosEntities', 'varName' => 'turnos'],
      ];
    private function alunosConfig()
      {
        return $this->config($this->alunos_aux);
      }
    private function alunosConfigEdicao($aluno)
      {
        $base = $this->config($this->alunos_aux);
        $data = 
          [
            'unidade' => $aluno->unidade,
            'curso'   => $aluno->curso,
            'agrupamento' => $aluno->agrupamento,
            'nivel' => $aluno->nivel,
            'turno' => $aluno->turno,
            'permanencia' => $aluno->permanencia,
          ];
        $extras = $this->opcoesAtendimento($data, true);
        $servicosTable = TableRegistry::get('Servicos');
        $servicos_atribuidos = $servicosTable->find('all',
          [
            'conditions' =>
              [
                'unidade' => $aluno->unidade,
                'curso'   => $aluno->curso,
                'agrupamento' => $aluno->agrupamento,
                'nivel' => $aluno->nivel,
                'turno' => $aluno->turno,
                'permanencia' => $aluno->permanencia,
              ],
            'order' =>
              [
                'obrigatorio DESC'
              ]
          ])->contain(['ServicosAux'])->toArray();
        $extras['servicos_atribuidos'] = $servicos_atribuidos;
        $servicos_atribuidos_financeiro = $servicosTable->find('all', 
          [
            'conditions' =>
              [
                'Servicos.id IN(' . implode(", ", $aluno->servicos_array) . ')'
              ],
            'order' =>
              [
                'obrigatorio DESC'
              ]
          ])->contain(['ServicosAux'])->toArray();
        $extras['servicos_atribuidos_financeiro'] = $servicos_atribuidos_financeiro;
        return array_merge($base, $extras); 
      }
    public function inserirAluno()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $user = $this->Auth->user();
            $alunosTable        = TableRegistry::get('Alunos');
            $pessoasTable       = TableRegistry::get('Pessoas');
            $parentesTable      = TableRegistry::get('Parentes');
            $enderecosTable     = TableRegistry::get('Enderecos');
            if(isset($data['servicos']))
              {
                $data['servicos']   = json_encode($data['servicos']);
                if(!is_array($data['financeiro']))
                  {
                    $data['financeiro'] = json_decode($data['financeiro'], true);
                  }
                array_walk($data['financeiro'], function(&$item, $i)
                  {
                    $item = intval($item);
                  });
                $data['financeiro'] = json_encode($data['financeiro']);
              }
            
            $parentes           = [];
            $enderecos          = [];
            if(isset($data['id']))
              {
                $aluno = $alunosTable->get($data['id']);
                $aluno = $alunosTable->patchEntity($aluno, $data, ['associated' => []]);
              }
            else
              {
                $aluno = $alunosTable->newEntity($data, ['associated' => []]);
              }
            if(!count($aluno->getErrors()))
              {
                if(isset($aluno->turmas))
                  {
                    $aluno->turmas = json_encode($data['turmas']);
                  }
                if(isset($data['pessoa-aluno']['telefones']))
                  {
                    $data['pessoa-aluno']['telefones'] = json_encode($data['pessoa-aluno']['telefones']);
                  }
                if(!isset($aluno['pessoa-aluno']['id']))
                  {
                    $pessoa = $pessoasTable->newEntity($data['pessoa-aluno']);
                  }
                else
                  {
                    $pessoa = $pessoasTable->get($data['pessoa-aluno']['id']);
                    $pessoa =  $pessoasTable->patchEntity($pessoa, $data['pessoa-aluno'], ['associated' => []]);
                  }
                $pessoasTable->save($pessoa, ['associated' => []]);
                $aluno->pessoa_id = $pessoa->id;
                $alunosTable->save($aluno, ['associated' => []]);
                $responsavel = $data['responsavel_legal'];
                foreach($data['parentes'] as $k => $parente)
                  {
                    if(!isset($parente['pessoa-parente']['id']))
                      {
                        $parentePessoa = $pessoasTable->newEntity($parente['pessoa-parente'], ['associated' => []]);
                      }
                    else
                      {
                        $parentePessoa = $pessoasTable->get($parente['pessoa-parente']['id']);
                        $parentePessoa = $pessoasTable->patchEntity($parentePessoa ,$parente['pessoa-parente'], ['associated' => []]);
                      }
                    $parentePessoa->telefones = json_encode($parente['pessoa-parente']['telefones']); 
                    $pessoasTable->save($parentePessoa, ['associated' => []]);
                    if($parente['endereco']['mesmo_endereco'] == 0)
                      {
                        $enderecosParente = $enderecosTable->newEntity($parente['endereco'], ['associated' => []]);
                        $enderecosParente->pessoa_id = $parentePessoa->id;
                        $enderecosTable->save($enderecosParente);
                      }
                    $parente['aluno_id']  = $aluno->id;
                    $parente['pessoa_id'] = $parentePessoa->id;
                    $parente['tipo'] = 1;
                    $parente['atribuicoes'] =  json_encode(@$parente['atribuicoes']);
                    if(!isset($parente['id']))
                      {
                        $parenteEntity = $parentesTable->newEntity($parente, ['associated' => []]);
                      }
                    else
                      {
                        $parenteEntity = $parentesTable->get($parente['id']);
                        $parenteEntity = $parentesTable->patchEntity($parenteEntity, $parente, ['associated' => []]);
                      }
                    $parentes[$k] = $parenteEntity;
                  }
                foreach($data['enderecos'] as $endereco)
                  {
                    $endereco['pessoa_id'] = $aluno->pessoa_id;
                    if(!isset($endereco['id']))
                      {
                        $enderecoEntity = $enderecosTable->newEntity($endereco, ['associated' => []]);
                      }
                    else
                      {
                        $enderecoEntity = $enderecosTable->get($endereco['id']);
                        $enderecoEntity = $enderecosTable->patchEntity($enderecoEntity, $endereco, ['associated' => []]);
                      }
                    array_push($enderecos, $enderecoEntity);
                  }
                $parentes = $parentesTable->saveMany($parentes, ['associated' => []]);
                $enderecosTable->saveMany($enderecos, ['associated' => []]);
                if(isset($data['removed']))
                  {
                    $removed = json_decode($data['removed'], true);
                    foreach($removed as $module => $ids)
                      {
                        foreach($ids as $removed_id)
                          {
                            switch ($module) 
                              {
                                case 'parentes':
                                  $removed = $parentesTable->get($removed_id);
                                  $parentesTable->delete($removed);
                                  break;
                                case 'enderecos':
                                  $removed = $enderecosTable->get($removed_id);
                                  $enderecosTable->delete($removed);
                                  break;
                              }
                          }
                      }
                  }
                $aluno->responsavel_id = $parentes[$responsavel]->id;
                $alunosTable->save($aluno);
                $response = json_encode(['success' => true]);
              }
            else
              {
                $errors = $aluno->getErrors();
                $response = json_encode(
                  [
                    'success' => false,
                    'errors'  => $errors
                  ]);
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody($response);
            return $this->response;
          }
      }
    public function sessaoListaAlunos()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();            
            $data       = $this->request->getData();
            $key        = (int)$data['key'];
            $parent_key = (int)$data['parent_key'];
            $id         = $data['id'];
            $alunosTable = TableRegistry::get('Alunos');
            $aluno = $alunosTable->find('all', ['conditions' => ['Alunos.id' => $key]])->contain(['Pessoas' => ['Enderecos', 'Boletos'], 'Parentes' => ['Pessoas' => ['Enderecos']]])->first();
            $ocorrenciasTable = TableRegistry::get('Ocorrencias');
            $ocorrencias = $ocorrenciasTable->find('all')->where(['comentario_de IS NULL'])->contain(['Comentarios' => ['Pessoas'], 'Pessoas'])->order(['Ocorrencias.data_criacao DESC'])->toArray();
            $_ocorrencias = [];
            foreach($ocorrencias as $ocorrencia)
              {
                if(in_array($aluno->pessoa->id, $ocorrencia->mencoes_array))
                  {
                    $_ocorrencias[] = $ocorrencia;
                  }
              }
            $aluno->ocorrencias = $_ocorrencias;
            $new_scope = 1;
            $block =
              [
                'key'   => $aluno->id,
                'parent_key' => $key,
                'unique' => uniqid(),
                'parent_id' => $id,
                'aluno' => $aluno
              ];
            $this->set($block);
            $turmasTable = TableRegistry::get('Turmas');
            $servicosTable = TableRegistry::get('Servicos');
            $servicos = $servicosTable->find('all', 
              [
                'conditions' =>
                  [
                    'Servicos.id IN(' . implode(', ', $aluno->servicos_array) . ')',
                    'ServicosAux.id != 2' //Alimentação
                  ]
              ])->contain(['ServicosAux'])->toArray();
            $turmas_servico = [];
            foreach($servicos as $servico)
              {
                $turmas = $turmasTable->find('all', 
                  [
                    'conditions' =>
                      [
                        'Turmas.servico' => $servico->servico,
                        'Turmas.unidade' => $aluno->unidade,
                        'Turmas.ano_letivo' => date('Y'),
                      ]
                  ])->toArray();
                $turmas_servico[$servico->servico] = 
                  [
                    'turmas' => $turmas,
                    'servico' => $servico
                  ];
              }
              
            $this->set('turmas_servico', $turmas_servico);
            $this->set('config', $this->alunosConfigEdicao($aluno));
          }
      }
    public function buscarTurmas()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $turmasTable = TableRegistry::get('Turmas');
            $servicosTable = TableRegistry::get('Servicos');
            $servicos = $servicosTable->find('all', 
              [
                'conditions' =>
                  [
                    'Servicos.id IN(' . implode(', ', $data['servicos']) . ')',
                    'ServicosAux.id != 2' //Alimentação
                  ]
              ])->contain(['ServicosAux'])->toArray();
            $turmas_servico = [];
            foreach($servicos as $servico)
              {
                $turmas = $turmasTable->find('all', 
                  [
                    'conditions' =>
                      [
                        'Turmas.servico' => $servico->servico,
                        'Turmas.unidade' => $data['unidade'],
                        'Turmas.ano_letivo' => date('Y'),
                      ]
                  ])->toArray();
                $turmas_servico[$servico->servico] = 
                  [
                    'turmas' => $turmas,
                    'servico' => $servico
                  ];
              }
            $this->set('turmas_servico', $turmas_servico);
            $this->set('key', $data['key']);
          }
      }
    public function buscarServicos()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $servicosTable = TableRegistry::get('Servicos');
            $servicos = $servicosTable->find('all', 
              [
                'conditions' =>
                  [
                    'unidade' => $data['unidade'],
                    'curso'   => $data['curso'],
                    'agrupamento' => $data['agrupamento'],
                    'nivel' => $data['nivel'],
                    'horario' => $data['horario'],
                    'permanencia' => $data['permanencia'],
                    'turno'  => $data['turno'],
                    'OR' => 
                      [
                        'obrigatorio' => 1,
                        'sistema_creche' => 1
                      ]
                  ]
              ])->contain(['ServicosAux'])->toArray();
            $this->set('servicos', $servicos);
            $this->set('key', $data['key']);
          }
      }
    public function buscarFinanceiro()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $servicosTable = TableRegistry::get('Servicos');
            $servicos = $servicosTable->find('all', 
              [
                'conditions' =>
                  [
                    'unidade' => $data['unidade'],
                    'curso'   => $data['curso'],
                    'agrupamento' => $data['agrupamento'],
                    'nivel' => $data['nivel'],
                    'horario' => $data['horario'],
                    'permanencia' => $data['permanencia'],
                    'turno'  => $data['turno'],
                    'OR' => 
                      [
                        'obrigatorio' => 1,
                        'sistema_creche' => 1
                      ]
                  ]
              ])->contain(['ServicosAux'])->toArray();
            $this->set('servicos', $servicos);
            $this->set('key', $data['key']);
          }
      }
    public function buscarFinanceiroExtra()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $servicosTable = TableRegistry::get('Servicos');
            $servicos = $servicosTable->find('all', 
              [
                'conditions' =>
                  [
                    'Servicos.id IN(' . implode(', ', array_keys($data['servicos'])) . ')'
                  ],
                'order' =>
                  [
                    'obrigatorio DESC'
                  ]
              ])->contain(['ServicosAux'])->toArray();
            $this->set('servicos', $servicos);
            $this->set('key', $data['key']);
            $this->set('valores_servicos', $data['servicos']);
          }
      }
    public function buscarTurmasExtra()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $turmasTable   = TableRegistry::get('Turmas');
            $servicosTable = TableRegistry::get('Servicos');
            $alunosTable   = TableRegistry::get('Alunos');
            $aluno = $alunosTable->get($data['aluno']);
            $servicos = $servicosTable->find('all', 
              [
                'conditions' =>
                  [
                    'Servicos.id IN(' . implode(', ', $data['servicos']) . ')',
                    'ServicosAux.id != 2' //Alimentação
                  ]
              ])->contain(['ServicosAux'])->toArray();
            $turmas_servico = [];
            foreach($servicos as $servico)
              {
                $turmas = $turmasTable->find('all', 
                  [
                    'conditions' =>
                      [
                        'Turmas.servico' => $servico->servico,
                        'Turmas.unidade' => $data['unidade'],
                        'Turmas.ano_letivo' => date('Y'),
                      ]
                  ])->toArray();
                $turmas_servico[$servico->servico] = 
                  [
                    'turmas' => $turmas,
                    'servico' => $servico
                  ];
              }
            $this->set('turmas_servico', $turmas_servico);
            $this->set('aluno', $aluno);
            $this->set('key', $data['key']);
          }
      }
    public function opcoesHorariosAgendamento()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $servicosTable = TableRegistry::get('Servicos');
            $servicos = $servicosTable->find('all', 
              [
                'conditions' =>
                  [
                    'unidade' => $data['unidade'],
                    'curso'   => $data['curso'],
                    'agrupamento' => $data['agrupamento'],
                    'nivel' => $data['nivel'],
                    'permanencia' => $data['permanencia'],
                    'turno'  => $data['turno']
                  ]
              ])->contain(['Horarios'])->toArray();
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode($servicos));
            return $this->response;
          }
      }
    public function buscarOpcoes($data = false)
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $options = $this->opcoesAtendimento($data);
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode($options));
            return $this->response;
          }
      }
    public function opcoesAtendimento($data, $all = false)
      {
        $options = [];
        if(!$all)
          {
            switch ($data['name']) 
              {
                case 'unidade':
                  $cursosTable = TableRegistry::get('Cursos');
                  $cursos = $cursosTable->find("all")->toArray();
                  foreach($cursos as $curso)
                    {
                      if($this->numeroServicos(null, $data['unidade'], $curso->id) > 0)
                        {
                          $options[$curso->id] = $curso->nome;
                        }
                    }
                  break;
                case 'curso':
                  $agrupamentosTable = TableRegistry::get('Agrupamentos');
                  $agrupamentos      = $agrupamentosTable->find('all')->toArray();
                  foreach($agrupamentos as $agrupamento)
                    {
                      if($this->numeroServicos(null, $data['unidade'], $data['curso'], $agrupamento->id) > 0)
                        {
                          $options[$agrupamento->id] = $agrupamento->nome;
                        }
                    }
                  break;
                case 'agrupamento':
                  $niveisTable = TableRegistry::get('Niveis');
                  $niveis      = $niveisTable->find('all')->toArray();
                  foreach($niveis as $nivel)
                    {
                      if($this->numeroServicos(null, $data['unidade'], $data['curso'], $data['agrupamento'], $nivel->id) > 0)
                        {
                          $options[$nivel->id] = $nivel->nome;
                        }
                    }
                  break;
                case 'turno':
                  $permanenciasTable = TableRegistry::get('Permanencias');
                  $permanencias      = $permanenciasTable->find('all')->toArray();
                  foreach($permanencias as $permanencia)
                    {
                      if($this->numeroServicos(null, $data['unidade'], $data['curso'], $data['agrupamento'], $data['nivel'], $data['turno'], $permanencia->id) > 0)
                        {
                          $options[$permanencia->id] = $permanencia->nome;
                        }
                    }
                  break;
                case 'permanencia':
                  $horariosTable = TableRegistry::get('Horarios');
                  $horarios      = $horariosTable->find('all')->toArray();
                  foreach($horarios as $horario)
                    {
                      if($this->numeroServicos(null, $data['unidade'], $data['curso'], $data['agrupamento'], $data['nivel'], $data['turno'], $data['permanencia'], $horario->id) > 0)
                        {
                          $options[$horario->id] = $horario->nome;
                        }
                    }
                  break;
                default:
                  # code...
                  break;
              }
          }
        else
          {
            $fields = 
              [
                'unidade' => 'cursos', 
                'curso' => 'agrupamentos',
                'agrupamento' => 'niveis',
                'turno' => 'permanencias',
                'permanencia' => 'horarios',
              ];
            foreach($fields as $parent => $field)
              {
                $data['name']    = $parent;
                $options[$field] = $this->opcoesAtendimento($data);
              }
          }
        return $options;
      }
    public function opcoesSelect()
      {
        if($this->request->is('GET'))
          {
            $data = $this->request->getQueryParams();
            $alunosTable = TableRegistry::get('Alunos');
            $alunos = $alunosTable->find('completo', 
              [
                'conditions' => 
                  [
                    'Pessoas.nome LIKE "%' . $data['q'] . '%"'
                  ]
              ])->toArray();
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['results' => $alunos]));
            return $this->response;
          }
      }
    
  }
?>