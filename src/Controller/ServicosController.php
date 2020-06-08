<?php 
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;
use Cake\Mailer\Email;
use Cake\Log\Log;


class ServicosController extends AppController
  {
  	public function initialize(): void
      {
      	parent::initialize();
       	$user = $this->Auth->user();
        $this->set('logged', (bool)$user);
      }
    public function index()
      {
        $this->set('titulo', "Lista de serviços | Aldeia Montessori");
        $servicosTable = TableRegistry::get('ServicosAux');
        $servicos = $servicosTable->find('all', 
          [
            'conditions' => ['ativo' => 1],
            'order' => ['ordenacao ASC']
          ])->toArray();
        $this->set('servicos', $servicos);
        $this->set('config', $this->servicosConfig());
      }
    public function novo()
      {
        $this->set('config', $this->servicosConfig());
        $this->set('titulo', "Novo serviço | Aldeia Montessori");
      }
    public function edicaoEmLote()
      {
        $this->set('config', $this->servicosConfig());
        $this->set('titulo', "Editar serviços em lote | Aldeia Montessori");
      }
    private $servicos_aux = 
      [
        'Unidades' => 'unidades',
        'ServicosAux' => 'servicos',
        'Cursos' => ['callable' => 'resultadosCursosEntities', 'varName' => 'cursos'],
        'Turnos' => ['callable' => 'resultadosTurnosEntities', 'varName' => 'turnos'],
      ];
    private function servicosConfig()
      {
        return $this->config($this->servicos_aux);
      }
    public function inserirServico()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $user = $this->Auth->user();
            $servicosTable  = TableRegistry::get('Servicos');
            $valoresTable   = TableRegistry::get('Valores');
            
            $servico = $servicosTable->newEntity($data, ['associated' => []]);

            if(!count($servico->getErrors()))
              {
                $data['valor'] = str_replace([' ', ".", ","], "", $data['valor']);
                $servicosTable->save($servico);
                $valor = $valoresTable->newEntity($data['valor'], ['associated' => []]);
                $valor->servico = $servico->id;
                $valoresTable->save($valor);
                $response = json_encode(['success' => true]);
              }
            else
              {
                $errors = $servico->getErrors();
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
    public function inserirValor()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $user = $this->Auth->user();
            $valoresTable = TableRegistry::get('Valores');
            $data['valor'] = str_replace([',', '.'], "", $data['valor']);
            $valor = $valoresTable->newEntity($data, ['associated' => []]);
            if(!count($valor->getErrors()))
              {
                $valoresTable->save($valor);
                $response = json_encode(['success' => true]);
              }
            else
              {
                $errors = $valor->getErrors();
                $response =  json_encode([
                    'success' => false,
                    'errors' => $errors
                  ]); 
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody($response);
            return $this->response;
          }
      }
    public function sessaoListaServicos()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();            
            $data       = $this->request->getData();
            $scope      = (int)$data['scope'];
            $key        = (int)$data['key'];
            $parent_key = (int)$data['parent_key'];
            $id         = $data['id'];
            $blocks     = 
              [
              ];
            switch ($scope)
              {
                case 0:
                  $unidadesTable = TableRegistry::get('Unidades');
                  $conds = [];
                  $unidades = $unidadesTable->find('list', ['keyField' => 'id', 'valueField' => 'nome', 'conditions' => $conds])->order(['nome ASC'])->toArray();
                  foreach($unidades as $k => $nome)
                    {
                      $prospects = 0;
                      $prospectsTable = TableRegistry::get('Prospects');
                      $conds = 
                        [
                        ];
                      $conds = count($conds) ? $conds : false;
                      $options = ['conditions' => $conds, 'unidade' => $k];
                      if($this->numeroServicos($key, $k) > 0)
                        {
                          $block = 
                              [
                                'scope' => 1,
                                'key'   => $k,
                                'parent_key' => $key,
                                'parent_scope' => $scope,
                                'nome' => $nome,
                                'unique' => uniqid(),
                                'parent_id' => $id,
                                'path' => [(int)$key, (int)$k]
                              ];
                            array_push($blocks, $block);
                        }
                    }
                  $this->set('servicos_categoria', $this->numeroServicos($key));
                break;
                case 1:
                  $cursosTable       = TableRegistry::get('Cursos');
                  $unidadesTable     = TableRegistry::get('Unidades');
                  $agrupamentosTable = TableRegistry::get('Agrupamentos');
                  $unidade           = $unidadesTable->get($key);
                  $cursos = $cursosTable->find('all', 
                    [
                      'order'      => ['ordenacao ASC'],
                    ])->toArray();
                  $path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                  foreach($cursos as $curso)
                    {
                      $agrupamentos = $curso->agrupamentos_array;
                      $_path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                      array_walk($_path, function(&$item, $i)
                        {
                          $item = (int)$item;
                        });
                      $_path[] = $curso->id;
                      if(count(array_intersect($agrupamentos, $unidade->agrupamentos_array)))
                        {
                           if($this->numeroServicos($_path[0], $_path[1], $_path[2]) > 0)
                            {
                              $block = 
                                [
                                  'scope' => 2,
                                  'key'   => $curso->id,
                                  'parent_key' => $key,
                                  'parent_scope' => $scope,
                                  'nome' => $curso->nome,
                                  'unique' => uniqid(),
                                  'parent_id' => $id,
                                  'path' => $_path
                                ];
                              array_push($blocks, $block);
                           }
                        }
                    $this->set('servicos_categoria', $this->numeroServicos($path[0], $path[1]));
                    }
                break;
                case 2:
                  $agrupamentosTable = TableRegistry::get('Agrupamentos');
                  $unidadesTable     = TableRegistry::get('Unidades');
                  $cursosTable       = TableRegistry::get('Cursos');
                  $curso             = $cursosTable->get($key);
                  $unidade           = $unidadesTable->get($parent_key);
                  $agrupamentos      = $agrupamentosTable->find('all', 
                    [
                      'order' => ['ordenacao ASC'] 
                    ])->toArray();
                  $path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                  foreach($agrupamentos as $agrupamento)
                    {

                      if(
                        (in_array($agrupamento->id, $curso->agrupamentos_array))
                        &&
                        (in_array($agrupamento->id, $unidade->agrupamentos_array))
                      )
                        {
                          $_path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                          array_walk($_path, function(&$item, $i)
                            {
                              $item = (int)$item;
                            });
                          $_path[] = $agrupamento->id;
                          if($this->numeroServicos($_path[0], $_path[1], $_path[2], $_path[3]) > 0)
                            {
                              $block = 
                                [
                                  'scope' => 3,
                                  'key'   => $agrupamento->id,
                                  'parent_key' => $key,
                                  'parent_scope' => $scope,
                                  'nome' => $agrupamento->nome,
                                  'unique' => uniqid(),
                                  'parent_id' => $id,
                                  'path' => $_path
                                ];
                              array_push($blocks, $block);
                            }
                        }
                    }
                  $this->set('servicos_categoria', $this->numeroServicos($path[0], $path[1], $path[2]));
                break;
                case 3:
                  $agrupamentosTable = TableRegistry::get('Agrupamentos');
                  $niveisTable       = TableRegistry::get('Niveis');
                  $agrupamento       = $agrupamentosTable->get($key);
                  $niveis            = $niveisTable->find('all',
                    [
                      'conditions' =>
                        [
                          'id IN(' . implode(', ', $agrupamento->niveis_array) . ')',
                          'ativo' => 1
                        ],
                      'order' => 
                        [
                          'ordenacao ASC'
                        ]
                    ])->toArray();
                  $path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                  foreach($niveis as $nivel)
                    {
                      $_path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                      array_walk($_path, function(&$item, $i)
                        {
                          $item = (int)$item;
                        });
                      $_path[] = $nivel->id;
                      if($this->numeroServicos($_path[0], $_path[1], $_path[2], $_path[3], $_path[4]) > 0)
                        {
                          $block = 
                            [
                              'scope' => 4,
                              'key'   => $nivel->id,
                              'parent_key' => $key,
                              'parent_scope' => $scope,
                              'nome' => $nivel->nome,
                              'unique' => uniqid(),
                              'parent_id' => $id,
                              'path' => $_path
                            ];
                          array_push($blocks, $block);
                        }
                    }
                  $this->set('servicos_categoria', $this->numeroServicos($path[0], $path[1], $path[2], $path[3]));
                break;
                case 4:
                  $turnosTable       = TableRegistry::get('Turnos');
                  $turnos            = $turnosTable->find('all',
                    [
                      'conditions' =>
                        [
                          'ativo' => 1
                        ],
                      'order' => 
                        [
                          'ordenacao ASC'
                        ]
                    ])->toArray();
                  $path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                  foreach($turnos as $turno)
                    {
                      $_path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                      array_walk($_path, function(&$item, $i)
                        {
                          $item = (int)$item;
                        });
                      $_path[] = $turno->id;
                      if($this->numeroServicos($_path[0], $_path[1], $_path[2], $_path[3], $_path[4], $_path[5]) > 0)
                        {
                          $block = 
                            [
                              'scope' => 5,
                              'key'   => $turno->id,
                              'parent_key' => $key,
                              'parent_scope' => $scope,
                              'nome' => $turno->nome,
                              'unique' => uniqid(),
                              'parent_id' => $id,
                              'path' => $_path
                            ];
                          array_push($blocks, $block);
                        } 
                    }
                  $this->set('servicos_categoria', $this->numeroServicos($path[0], $path[1], $path[2], $path[3], $path[4]));
                break;
                case 5:
                  $turnosTable       = TableRegistry::get('Turnos');
                  $turno             = $turnosTable->get($key);
                  $permanencias      = $turno->permanencias_entities;
                  $path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                  foreach($permanencias as $permanencia)
                    {
                      $_path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                      array_walk($_path, function(&$item, $i)
                        {
                          $item = (int)$item;
                        });
                      $_path[] = $permanencia->id;
                      if($this->numeroServicos($_path[0], $_path[1], $_path[2], $_path[3], $_path[4], $_path[5], $_path[6]) > 0)
                        {
                          $block = 
                            [
                              'scope' => 6,
                              'key'   => $permanencia->id,
                              'parent_key' => $key,
                              'parent_scope' => $scope,
                              'nome' => $permanencia->nome,
                              'unique' => uniqid(),
                              'parent_id' => $id,
                              'path' => $_path
                            ];
                          array_push($blocks, $block);
                        }
                    }
                  $this->set('servicos_categoria', $this->numeroServicos($path[0], $path[1], $path[2], $path[3], $path[4], $path[5]));
                break;
                case 6:
                  $permanenciasTable = TableRegistry::get('Permanencias');
                  $permanencia       = $permanenciasTable->get($key);
                  $turnosTable       = TableRegistry::get('Turnos');
                  $path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                  $turno             = $turnosTable->get($path[5]);
                  foreach($permanencia->horarios_entities as $horario)
                    {
                      $_path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                      array_walk($_path, function(&$item, $i)
                        {
                          $item = (int)$item;
                        });
                      $_path[] = $horario->id;
                      if($this->numeroServicos($_path[0], $_path[1], $_path[2], $_path[3], $_path[4], $_path[5], $_path[6], $_path[7]) > 0)
                        {
                          $block = 
                            [
                              'scope' => 7,
                              'key'   => $horario->id,
                              'parent_key' => $key,
                              'parent_scope' => $scope,
                              'nome' => $horario->nome,
                              'unique' => uniqid(),
                              'parent_id' => $id,
                              'path' => $_path
                            ];
                          array_push($blocks, $block);
                        }
                    }
                  $this->set('servicos_categoria', $this->numeroServicos($path[0], $path[1], $path[2], $path[3], $path[4], $path[5], $path[6]));
                break;
              case 7:
                  $path = (is_array($data['path'])) ? $data['path'] : json_decode($data['path'], true);
                  $blocks = 'form';
                  $servico = $this->numeroServicos($path[0], $path[1], $path[2], $path[3], $path[4], $path[5], $path[6], $path[7], false)[0];
                  $valoresTable = TableRegistry::get('Valores');
                  $valores = $valoresTable->find('all', 
                    [
                      'conditions' => 
                        [
                          'servico' => $servico->id
                        ],
                      'order' =>
                        [
                          'data_inicio DESC'
                        ]
                    ])->toArray();
                  $this->set('servico', $servico);
                  $this->set('valores', $valores);
                  $this->set('scope', ((int)$scope + 1));
                  $this->set('key', $servico->id);
                  $this->set('parent_key', $key);
                  $this->set('parent_id', $id);
                  $this->set('parent_scope', $scope);
                  $this->set('servicos_categoria', false);
                break;
              }
            $this->set('blocks', $blocks);
          }
      }
    public function editarLote()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $servicosTable = TableRegistry::get('Servicos');
            $valoresTable = TableRegistry::get('Valores');
            $lote_config = $data;
            unset($lote_config['valor']);
            unset($lote_config['data_inicio']);
            unset($lote_config['data_final']);
            $lote = $servicosTable->find('lote', ['lote_config' => $lote_config])->toArray();
            $valor = (float)str_replace(',', ".", str_replace('.', "", $data['valor']));
            foreach($lote as $servico)
              {
                $ultimo = $valoresTable->find('all', ['conditions' => ['servico' => $servico->id], 'order' => ['data_inicio DESC']])->first();
                $valor_adicional = round((
                  ($valor*(int)$ultimo->valor)/100
                ));
                $novo_valor = ((int)$ultimo->valor + $valor_adicional);
                $novo =
                  [
                    'servico' => $servico->id,
                    'data_inicio' => $data['data_inicio'],
                    'data_final' => $data['data_final'],
                    'valor'    => $novo_valor
                  ];                
                $entity = $valoresTable->newEntity($novo, ['associated' => []]);
                $valoresTable->save($entity);
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(
              [
                'success' => true,
                'count'  => count($lote)
              ]));
            return $this->response;
          }
      }
  }
?>