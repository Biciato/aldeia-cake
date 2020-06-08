<?php 
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;
use Cake\Mailer\Email;
use Cake\Log\Log;

class ProspectsController extends AppController
  {
    private $allowed = 
      [
        'novo',
        'inserirProspectExterno'
      ];
    private $tipo_visita = 4;
  	public function initialize(): void
      {
      	parent::initialize();
       	$user = $this->Auth->user();
        $this->Auth->allow($this->allowed);
        $this->set('logged', (bool)$user);
        if((strtolower($this->request->getParam('action')) === 'novo')&&(!$user))
          {
            $this->prospects_aux['Unidades'] =  ['callable' => 'resultadosUnidadesDescricao', 'varName' => 'unidades'];
          }
      }
    public function index()
      {
        $this->set('titulo', "Lista de prospects | Aldeia Montessori");
        $this->set($this->prospectConfig());
        $this->set($this->filtrosConfig());
      }
    public function adicionarProspect()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $prospectsTable = TableRegistry::get('Prospects');
            $prospect       = $prospectsTable->newEntity($data);
            $success        = false;
            $errors         = $prospect->getErrors();
            if($prospectsTable->save($prospect, ['associated' => []]))
              {
                $success    = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'errors' => $errors]));
            return $this->response;
          }
      }
    public function editarProspect()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $prospectsTable = TableRegistry::get('Prospects');
            $prospect = $prospectsTable->get($data['id']);
            $prospect = $prospectsTable->patchEntity($prospect, $data);
            $success = false;
            $errors  = $prospect->getErrors();
            if($prospectsTable->save($prospect, ['associated' => []]))
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'errors' => $errors]));
            return $this->response;
          }
      }
    public function excluir()
      {
        if($this->request->is('POST'))
          {
            $_data      = $this->request->getData();
            $data       = $_data['dados'];
            $success    = false;
            $tableClass = ($data['tipo'] == 'documento') ? "Documentos" : "Capitulos";
            $table      = TableRegistry::get($tableClass);
            $entity     = $table->get($data['id']);
            if($table->delete($entity))
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success]));
            return $this->response;
          }
      }
    public function resultadosColaboradores()
      {
        $colaboradoresTable = TableRegistry::get('Colaboradores');
        $colaboradores = $colaboradoresTable->find('comPessoa')->select(['Colaboradores.id', 'Pessoas.nome'])->toArray();
        $opts = [];
        foreach($colaboradores as $colaborador)
          {
            $opts[$colaborador->id] = $colaborador->pessoa->nome;
          }
        return $opts;
      }
    public function resultadosUnidadesDescricao()
      {
        $unidadesTable = TableRegistry::get('Unidades');
        $list = $unidadesTable->find('list', 
          [
            'conditions' =>
              [
                'ativo' => true
              ],
            'keyField' => 'id',
            'valueField' => 'nome_completo'
          ])->toArray();
        return $list;
      }
    public function resultadosAlunosProspects()
      {
        $prospectsTable = TableRegistry::get('Prospects');
        $prospects      = $prospectsTable->find('all')->contain(['Pessoas'])->toArray();
        $opts = [];
        foreach($prospects as $prospect)
          {
            $opts[$prospect->id] = $prospect->pessoa->nome;
          } 
        return $opts;
      }
    public function resultadosStatusInteracoes()
      {
        return 
          [
            1 => 'Concluída',
            2 => 'Em atraso', 
            3 => 'Em aberto'
          ];
      }
    public function resultadosStatusInteracoesProspects()
      {
        return 
          [
            1 => 'Sem interações',
            2 => 'Todas interações concluídas',
            3 => 'Interações concluídas ou em aberto (sem atrasos)', 
            4 => 'Uma ou mais interação atrasada'
          ];
      }
    private $prospects_aux = 
      [
        'Unidades' => 'unidades',
        'Parentescos' => 'parentescos',
        'Permanencias' => 'permanencias',
        'MeiosAtendimento' => 'meios_atendimento',
        'MeiosConhecimento' => 'meios_conhecimento',
        'AcompanhamentosSistematicos' => 'acompanhamentos_sistematicos',
        'Turnos' => 'turnos',
        'Horarios' => 'horarios',
        'TiposInteracao' => 'tipos_interacao',
        'Colaboradores' => ['callable' => 'resultadosColaboradores', 'varName' => 'responsaveis']
      ];
    private $prospects_aux_email = 
      [
        'Unidades' => 'unidades',
        'Parentescos' => 'parentescos',
        'MeiosConhecimento' => 'meios_conhecimento',
      ];
    private $interacoes_aux = 
      [
        'TiposInteracao' => 'tipos_interacao',
        'Colaboradores' => ['callable' => 'resultadosColaboradores', 'varName' => 'responsaveis'],
        'Parentescos' => 'parentescos'
      ];
    private $filtros_prospects = 
      [
        'Unidades' => 'unidades',
        'Agrupamentos' => 'agrupamentos',
        'Status' => ['callable' => 'resultadosStatusInteracoes', 'varName' => 'status_interacoes'],
      ];
    private $filtros_prospects_lista = 
      [
        'Unidades' => 'unidades',
        'Agrupamentos' => 'agrupamentos',
        'Status' => ['callable' => 'resultadosStatusInteracoesProspects', 'varName' => 'status_interacoes_prospects'],
      ];
    private $filtros_interacoes = 
      [
        'Unidades' => 'unidades',
        'Agrupamentos' => 'agrupamentos',
        'Status' => ['callable' => 'resultadosStatusInteracoes', 'varName' => 'status_interacoes'],
        'Colaboradores' => ['callable' => 'resultadosColaboradores', 'varName' => 'responsaveis'],
      ];
    private function prospectConfig()
      {
        return $this->config($this->prospects_aux);
      }
    private function interacaoConfig()
      {
        return $this->config($this->interacoes_aux);
      }
    private function filtrosConfig()
      {
        $src = (strtolower($this->request->getParam('action')) === 'index') ? $this->filtros_prospects_lista : $this->filtros_prospects;
        return $this->config($src);
      }
    private function filtrosInteracoes()
      {
        return $this->config($this->filtros_interacoes);
      }
    private function emailVisitaConfig()
      {
        return $this->config($this->prospects_aux_email);
      }
    public function novo()
      {
        $this->set('config', $this->prospectConfig());
        $this->set($this->filtrosConfig());
        $user = $this->Auth->user();
        if(!$user)
          {
            $this->viewBuilder()->disableAutoLayout();
            $this->render('novo_externo');
          }
        $this->set('titulo', "Novo prospects | Aldeia Montessori");
      }
    public function visualizar()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $prospectsTable = TableRegistry::get('Prospects');
            $prospect = $prospectsTable->get($data['id']);
            $this->set('prospect', $prospect);
          }
      }
    public function editar($id)
      {
        $prospectsTable = TableRegistry::get('Prospects');
        $prospect = $prospectsTable->get($id);
        $this->set('prospect', $prospect);
        $this->set('titulo', "Editar prospect #" . $prospect->id . " | Aldeia Montessori");
      }
    public function formularioAluno()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $this->set($data);
            $this->viewBuilder()->disableAutoLayout();
          }
      }
    public function inserirProspect()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $user = $this->Auth->user();
            $prospectsTable    = TableRegistry::get('Prospects');
            $pessoasTable      = TableRegistry::get('Pessoas');
            $parentesTable     = TableRegistry::get('Parentes');
            $enderecosTable    = TableRegistry::get('Enderecos');
            $interacoesTable   = TableRegistry::get('Interacoes');
            $parentes          = [];
            $enderecos         = [];
            $interacoes        = [];
            if(isset($data['id']))
              {
                $prospect = $prospectsTable->get($data['id']);
                $prospect = $prospectsTable->patchEntity($prospect, $data, ['associated' => []]);
              }
            else
              {
                $prospect = $prospectsTable->newEntity($data, ['associated' => []]);
              }
            if(!count($prospect->getErrors()))
              {
                if(!isset($prospect['pessoa-prospect']['id']))
                  {
                    $pessoa = $pessoasTable->newEntity($data['pessoa-prospect']);
                  }
                else
                  {
                    $pessoa = $pessoasTable->get($data['pessoa-prospect']['id']);
                    $pessoa =  $pessoasTable->patchEntity($pessoa, $data['pessoa-prospect'], ['associated' => []]);
                  }
                $pessoasTable->save($pessoa, ['associated' => []]);
                $prospect->pessoa_id = $pessoa->id;
                if(is_array($data['acompanhamentos_sistematicos']))
                  {
                    $prospect->acompanhamentos_sistematicos = json_encode($data['acompanhamentos_sistematicos']);
                  }
                $prospectsTable->save($prospect, ['associated' => []]);
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
                    $parente['aluno_id']  = $prospect->id;
                    $parente['pessoa_id'] = $parentePessoa->id;
                    if(!isset($parente['id']))
                      {
                        $parenteEntity = $parentesTable->newEntity($parente, ['associated' => []]);
                      }
                    else
                      {
                        $parenteEntity = $parentesTable->get($parente['id']);
                        $parenteEntity = $parentesTable->patchEntity($parenteEntity, $parente, ['associated' => []]);
                      }
                    array_push($parentes, $parenteEntity);
                  }
                foreach($data['enderecos'] as $endereco)
                  {
                    $endereco['aluno_id'] = $prospect->id;
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
                if(@$data['interacoes'])
                  {
                    foreach($data['interacoes'] as $interacao)
                      {
                        $interacao['aluno_id'] = $prospect->id;
                        $interacao['usuario']  = $user['id'];
                        if($interacao['arquivo']->getError() === 0)
                          {
                            $titulo = $interacao['arquivo']->getClientFilename();
                            $pedacos = explode('.', $titulo);
                            $ext = array_pop($pedacos);
                            $nome_arquivo_server = date('Y_m_d_H_i_s') . '_' . md5($interacao['titulo']) . '.' . $ext;
                            $interacao['caminho_arquivo'] = WWW_ROOT . 'uploads_interacoes/' . $nome_arquivo_server;
                            $interacao['titulo_arquivo'] = $titulo;
                            move_uploaded_file($interacao['arquivo']->getStream()->getMetadata('uri'), $interacao['caminho_arquivo']);
                          }
                        elseif(isset($interacao['remover-arquivo']))
                          {
                            $interacao['caminho_arquivo'] = null;
                            $interacao['titulo_arquivo'] = null;
                          }
                        if(!isset($interacao['id']))
                          {
                            $interacaoEntity = $interacoesTable->newEntity($interacao, ['associated' => []]);
                            $interacaoEntity->responsavel = $interacao['responsavel'];
                          }
                        else
                          {
                            $interacaoEntity = $interacoesTable->get($interacao['id']);
                            $interacaoEntity = $interacoesTable->patchEntity($interacaoEntity, $interacao, ['associated' => []]);
                          }
                        array_push($interacoes, $interacaoEntity);
                      }
                  }
                $parentesTable->saveMany($parentes, ['associated' => []]);
                $enderecosTable->saveMany($enderecos, ['associated' => []]);
                if(count($interacoes))
                  {
                    $interacoesTable->saveMany($interacoes, ['associated' => []]);
                  }
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
                                case 'interacoes':
                                  $removed = $interacoesTable->get($removed_id);
                                  $interacoesTable->delete($removed);
                                  break;
                              }
                          }
                      }
                  }
                $response = json_encode(['success' => true]);
              }
            else
              {
                $errors = $prospect->getErrors();
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
    public function inserirProspectExterno()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 
                http_build_query(
                  [
                    'secret' => '6Lflg7YUAAAAAEHiKx3CIw-WbFIiqcJvS0W828Se',
                    'response' => $data['g-recaptcha-response'] 
                  ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resposta = curl_exec($ch);
            curl_close ($ch);
            $resposta = json_decode($resposta);
            if($resposta->success)
              {
                $user = $this->Auth->user();
                $prospectsTable    = TableRegistry::get('Prospects');
                $pessoasTable      = TableRegistry::get('Pessoas');
                $parentesTable     = TableRegistry::get('Parentes');
                $interacoesTable   = TableRegistry::get('Interacoes');
                $parentes          = [];

                $data['data_primeiro_atendimento'] = date('Y-m-d');
                $prospect = $prospectsTable->newEntity($data, ['validate' => 'externo']);
                if(!count($prospect->getErrors()))
                  {
                    $duplicate = $pessoasTable->find('all', ['conditions' => ['nome' => $data['pessoa-prospect']['nome']]])->count();
                    if(!$duplicate)
                      {
                        $pessoa = $pessoasTable->newEntity($data['pessoa-prospect'], ['associated' => []]);
                        $pessoasTable->save($pessoa, ['associated' => []]);
                        $prospect->pessoa_id = $pessoa->id;
                        $prospectsTable->save($prospect, ['associated' => []]);
                        $parente = $data['parentes'][0];
                        $parentePessoa = $pessoasTable->newEntity($parente['pessoa-parente'], ['associated' => []]);
                        $parentePessoa->telefones = json_encode($parente['pessoa-parente']['telefones']); 
                        $pessoasTable->save($parentePessoa, ['associated' => []]);
                        $parente['aluno_id']  = $prospect->id;
                        $parente['pessoa_id'] = $parentePessoa->id;
                        $parenteEntity = $parentesTable->newEntity($parente, ['associated' => []]);
                        $parentesTable->save($parenteEntity, ['associated' => []]);
                        $interacao = 
                          [
                            'tipo' => $this->tipo_visita,
                            'data' => $data['interacao']['data'],
                            'hora' => $data['interacao']['hora'],
                            'titulo' => 'Visita agendada pelo site',
                            'aluno_id' => $prospect->id,
                            'informacao' => 'Interação gerada automaticamente por um agendamento de visita pelo formulário externo|Timestamp: ' . date('d/m/Y H:i:s')
                          ];
                        $interacao = $interacoesTable->newEntity($interacao, ['associated' => []]);
                        $interacoesTable->save($interacao, ['associated' => []]);
                        $emailData = compact('pessoa', 'prospect', 'parenteEntity', 'parentePessoa', 'interacao');
                        $this->enviarEmailProspectExterno($emailData);
                        $this->enviarEmailProspectExternoCliente($emailData);
                        $response = json_encode(['success' => true]);
                      }
                    else
                      {
                        $data = ['nome_parente' => $data['parentes'][0]['pessoa-parente']['nome'], 'nome_aluno' => $data['pessoa-prospect']['nome']];
                        $this->enviarEmailProspectExternoDuplicado($data);
                        sleep(1);
                        $response = json_encode(['success' => true]);
                      }
                  }
                else
                  {
                    $errors = $prospect->getErrors();
                    $response = json_encode(
                      [
                        'success' => false,
                        'errors'  => $errors
                      ]);
                  }
              }
            else
              {
                $response = json_encode(['success' => false, 'errors' => 'grecaptcha']);
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody($response);
            return $this->response;
          }
      }
    public function sessaoListaProspect()
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
            $filtered_prospect = 
              (
                (!empty($data['prospect_filter']))
                ||
                (!empty($data['unidade_filter']))
                ||
                (!empty($data['atendimento_filter']))
              );
            $prospects_categoria = 0;
            switch ($scope)
              {
                case 0:
                  $unidadesTable = TableRegistry::get('Unidades');
                  $conds = [];
                  if(!empty($data['unidade_filter']))
                    {
                      array_push($conds, 'id = ' . $data['unidade_filter']);
                    }
                  if(!empty($data['agrupamento_filter']))
                    {
                      array_push($conds, 'agrupamentos LIKE \'%"' . $data['agrupamento_filter'] . '"%\'');
                    }
                  $unidades = $unidadesTable->find('list', ['keyField' => 'id', 'valueField' => 'nome', 'conditions' => $conds])->order(['nome ASC'])->toArray();
                  foreach($unidades as $k => $nome)
                    {
                      $prospects = 0;
                      $prospectsTable = TableRegistry::get('Prospects');
                      $conds = 
                        [
                        ];
                      if(!empty($data['prospect_filter']))
                        {
                          array_push($conds, 'Pessoas.nome LIKE "%' .  $data['prospect_filter'] . '%"');
                        }
                      if(!empty($data['atendimento_filter']))
                        {
                          $dates = explode(" - ", $data['atendimento_filter']);
                          foreach($dates as &$date)
                            {
                              $date = implode('-', array_reverse(explode('/', $date)));
                            }
                          array_push($conds, "Prospects.data_primeiro_atendimento >= CAST(\"" . $dates[0] . "\" AS DATE) AND Prospects.data_primeiro_atendimento <= CAST(\"" . $dates[1] . "\" AS DATE)");
                        }
                      $conds = count($conds) ? $conds : false;
                      $options = ['conditions' => $conds, 'unidade' => $k];
                      if(!empty($data['status_filter']))
                        {
                          $options['interacoes'] = $data['status_filter'];
                        }
                      $prospects = $prospectsTable->find('comAgrupamento', $options)->contain(['Pessoas'])->count();
                      if($prospects > 0)
                        {
                          $block = 
                              [
                                'scope' => 1,
                                'key'   => $k,
                                'parent_key' => $key,
                                'parent_scope' => $scope,
                                'nome' => $nome,
                                'unique' => uniqid(),
                                'parent_id' => $id
                              ];
                            array_push($blocks, $block);
                            $prospects_categoria += $prospects;
                        }
                    }
                  if((empty($data['agrupamento_filter']))||($data['agrupamento_filter'] == -1))
                    {
                      $prospects = 0;
                      $conds = [];
                      if(!empty($data['prospect_filter']))
                        {
                          array_push($conds, 'Pessoas.nome LIKE "%' .  $data['prospect_filter'] . '%"');
                        }
                      if(!empty($data['unidade_filter']))
                        {
                          array_push($conds, 'Prospects.unidade = ' .  $data['unidade_filter']);
                        }
                      if(!empty($data['atendimento_filter']))
                        {
                         $dates = explode(" - ", $data['atendimento_filter']);
                         foreach($dates as &$date)
                            {
                              $date = implode('-', array_reverse(explode('/', $date)));
                            }
                          array_push($conds, "Prospects.data_primeiro_atendimento >= CAST(\"" . $dates[0] . "\" AS DATE) AND Prospects.data_primeiro_atendimento <= CAST(\"" . $dates[1] . "\" AS DATE)");
                        }
                      $conds = (!count($conds)) ? false : $conds;
                      $options = ['conditions' => $conds];
                      if(!empty($data['status_filter']))
                        {
                          $options['interacoes'] = $data['status_filter'];
                        }
                      $prospectsTable = TableRegistry::get('Prospects');
                      $prospects = $prospectsTable->find('semAgrupamento', $options)->contain(['Pessoas', 'Interacoes'])->count();
                      if(($prospects > 0))
                        {
                          $extra_block = 
                            [
                              'scope' => 1,
                              'key'   => 'fora-de-agrupamento',
                              'parent_key' => $key,
                              'parent_scope' => $scope,
                              'nome' => 'Fora de agrupamento',
                              'unique' => uniqid(),
                              'parent_id' => $id
                            ];
                          array_push($blocks, $extra_block);
                          $prospects_categoria += $prospects;
                        }
                    }
                break;
                case 1:
                  if($key != 'fora-de-agrupamento')
                    {
                      $agrupamentosTable = TableRegistry::get('Agrupamentos');
                      $unidadesTable     = TableRegistry::get('Unidades');
                      $unidade           = $unidadesTable->get($key);
                      $conds             = ['id IN(' . implode(', ', $unidade->agrupamentos_array) . ')'];
                      if(!empty($data['agrupamento_filter']))
                        {
                          array_push($conds, 'id = ' . $data['agrupamento_filter']);
                        }
                      $agrupamentos = $agrupamentosTable->find('list', ['keyField' => 'id', 'valueField' => 'nome', 'conditions' => $conds])->order(['idade_inicial ASC'])->toArray();
                      foreach($agrupamentos as $k => $nome)
                        {
                          $prospects = 0;
                          
                          $prospectsTable = TableRegistry::get('Prospects');
                          $conds = [];
                          if(!empty($data['prospect_filter']))
                            {
                              array_push($conds, 'Pessoas.nome LIKE "%' .  $data['prospect_filter'] . '%"');
                            }
                          if(!empty($data['unidade_filter']))
                            {
                              array_push($conds, 'Prospects.unidade = ' .  $data['unidade_filter']);
                            }
                          if(!empty($data['atendimento_filter']))
                            {
                              $dates = explode(" - ", $data['atendimento_filter']);
                              foreach($dates as &$date)
                                {
                                  $date = implode('-', array_reverse(explode('/', $date)));
                                }
                              array_push($conds, "Prospects.data_primeiro_atendimento >= CAST(\"" . $dates[0] . "\" AS DATE) AND Prospects.data_primeiro_atendimento <= CAST(\"" . $dates[1] . "\" AS DATE)");
                            }
                          $conds = (!count($conds)) ? false : $conds;
                          $prospects = $prospectsTable->find('agrupamento', ['agrupamento' => $k, 'unidade' => $key, 'conditions' => $conds])->contain(['Pessoas'])->count();
                          if(($prospects > 0))
                            {
                              $block = 
                                [
                                  'scope' => 2,
                                  'key'   => $k,
                                  'parent_key' => $key,
                                  'parent_scope' => $scope,
                                  'nome' => $nome,
                                  'unique' => uniqid(),
                                  'parent_id' => $id
                                ];
                              array_push($blocks, $block);
                              $prospects_categoria += $prospects;
                            }
                        }
                    }
                  else
                    {
                      $conds = [];
                      if(!empty($data['prospect_filter']))
                        {
                          array_push($conds, 'Pessoas.nome LIKE "%' .  $data['prospect_filter'] . '%"');
                        }
                      if(!empty($data['unidade_filter']))
                        {
                          array_push($conds, 'Prospects.unidade = ' .  $data['unidade_filter']);
                        }
                      if(!empty($data['atendimento_filter']))
                        {
                          $dates = explode(" - ", $data['atendimento_filter']);
                          foreach($dates as &$date)
                            {
                              $date = implode('-', array_reverse(explode('/', $date)));
                            }
                          array_push($conds, "Prospects.data_primeiro_atendimento >= CAST(\"" . $dates[0] . "\" AS DATE) AND Prospects.data_primeiro_atendimento <= CAST(\"" . $dates[1] . "\" AS DATE)");
                        }
                      $conds = (!count($conds)) ? false : $conds;
                      $options = ['conditions' => $conds];
                      if(!empty($data['status_filter']))
                        {
                          $options['interacoes'] = $data['status_filter'];
                        }
                      $prospectsTable = TableRegistry::get('Prospects');
                      $prospects = $prospectsTable->find('semAgrupamento', $options)->contain(['Pessoas', 'Interacoes'])->toArray();
                      foreach($prospects as $prospect)
                        {
                          $block = 
                            [
                              'scope' => 4,
                              'key'   => $prospect->id,
                              'parent_key' => 'fora-de-agrupamento',
                              'parent_scope' => $scope,
                              'nome' => $prospect->pessoa->nome,
                              'unique' => uniqid(),
                              'parent_id' => $id
                            ];
                          array_push($blocks, $block);
                        }
                      $prospects_categoria += count($prospects);
                    }
                break;
                case 2:
                  $prospectsTable = TableRegistry::get('Prospects');
                  $conds = [];
                  if(!empty($data['prospect_filter']))
                    {
                      array_push($conds, 'Pessoas.nome LIKE "%' .  $data['prospect_filter'] . '%"');
                    }
                  if(!empty($data['unidade_filter']))
                    {
                      array_push($conds, 'Prospects.unidade = ' .  $data['unidade_filter']);
                    }
                  if(!empty($data['atendimento_filter']))
                    {
                      $dates = explode(" - ", $data['atendimento_filter']);
                      foreach($dates as &$date)
                        {
                          $date = implode('-', array_reverse(explode('/', $date)));
                        }
                      array_push($conds, "Prospects.data_primeiro_atendimento >= CAST(\"" . $dates[0] . "\" AS DATE) AND Prospects.data_primeiro_atendimento <= CAST(\"" . $dates[1] . "\" AS DATE)");
                    }
                  $conds = (!count($conds)) ? false : $conds;
                  $prospects = $prospectsTable->find('agrupamento', ['agrupamento' => $key, 'unidade' => $parent_key, 'conditions' => $conds])->contain(['Pessoas'])->toArray();
                  foreach($prospects as $prospect)
                    {
                      $block = 
                        [
                          'scope' => 3,
                          'key'   => $prospect->id,
                          'parent_key' => $key,
                          'parent_scope' => $scope,
                          'nome' => $prospect->pessoa->nome,
                          'unique' => uniqid(),
                          'parent_id' => $id
                        ];
                      array_push($blocks, $block);
                    }
                  $prospects_categoria += count($prospects);
                break;
                case 3:
                case 4:
                  $prospectsTable = TableRegistry::get('Prospects');
                  $prospect = $prospectsTable->find('completo', ['conditions' => ['Prospects.id' => $key]])->first();
                  $new_scope = ($scope == 4) ? 5 : 6;
                  $blocks = 'form';
                  $block =
                    [
                      'scope' => $new_scope,
                      'key'   => $prospect->id,
                      'parent_key' => $key,
                      'parent_scope' => $scope,
                      'unique' => uniqid(),
                      'parent_id' => $id,
                      'prospect' => $prospect
                    ];
                  $this->set($block);
                  $this->set('config', $this->prospectConfig());
                break;
              }
            $this->set('blocks', $blocks);
            $this->set('prospects_categoria', $prospects_categoria);
          }
      }
    public function interacoes()
      {
        $interacoesTable = TableRegistry::get('Interacoes');
        $interacoes = $interacoesTable->find('all')->contain(['Prospects' => ['Pessoas', 'Parentes' => ['Pessoas']], 'Responsaveis' => ['Pessoas']])->order(['data DESC', 'hora DESC'])->toArray();
        $this->set('interacoes', $interacoes);
        $this->set('titulo', 'Todas Interações | Aldeia Montessori');
        $this->set($this->filtrosInteracoes());
      }
    public function sessaoListaInteracoes()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();            
            $data       = $this->request->getData();
            $key        = (int)$data['key'];
            $parent_key = (int)$data['parent_key'];
            $id         = $data['id'];
            $interacoesTable = TableRegistry::get('Interacoes');
            $interacao = $interacoesTable->find('all', ['conditions' => ['Interacoes.id' => $key]])->contain(['Prospects' => ['Pessoas', 'Parentes' => ['Pessoas']]])->first();
            $new_scope = 1;
            $block =
              [
                'key'   => $interacao->id,
                'parent_key' => $key,
                'unique' => uniqid(),
                'parent_id' => $id,
                'interacao' => $interacao
              ];
            $this->set($block);
            $this->set('config', $this->interacaoConfig());
          }
      }  
    public function inserirInteracao()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $user = $this->Auth->user();
            
            if($data['arquivo']->getError() === 0)
              {
                $titulo = $data['arquivo']->getClientFilename();
                $pedacos = explode('.', $titulo);
                $ext = array_pop($pedacos);
                $nome_arquivo_server = date('Y_m_d_H_i_s') . '_' . md5($data['titulo']) . '.' . $ext;
                $data['caminho_arquivo'] = WWW_ROOT . 'uploads_interacoes/' . $nome_arquivo_server;
                $data['titulo_arquivo'] = $titulo;
                move_uploaded_file($data['arquivo']->getStream()->getMetadata('uri'), $data['caminho_arquivo']);
              }
            elseif(isset($data['remover-arquivo']))
              {
                $data['caminho_arquivo'] = null;
                $data['titulo_arquivo'] = null;
              }
            $interacoesTable   = TableRegistry::get('Interacoes');
            $interacao  = $interacoesTable->get($data['id']);
            $interacao = $interacoesTable->patchEntity($interacao, $data);
            $interacao->responsavel = $data['responsavel'];
            $errors = $interacao->getErrors();
            if(!count($errors))
              {
                $interacoesTable->save($interacao, ['associated' => []]);
                $response = 
                  [
                    'success' => true
                  ];
              }
            else
              {
                $response = 
                  [
                    'success' => false,
                    'errors' => $errors
                  ];
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode($response));
            return $this->response;
          }
      }
    public function visualizarArquivo($id)
      {
        $interacoesTable = TableRegistry::get('Interacoes');
        $interacao = $interacoesTable->get($id);
        if($interacao->caminho_arquivo)
          {
            $mime = mime_content_type($interacao->caminho_arquivo);
            $this->response = $this->response->withType($mime);
            $this->response = $this->response->withFile($interacao->caminho_arquivo, ['name' => $interacao->titulo_arquivo]);
            return $this->response;
          }
        else
          {
            throw new NotFoundException("Essa interação não possui arquivos");
          }
       
      }
    public function baixarArquivo($id)
      {
         $interacoesTable = TableRegistry::get('Interacoes');
         $interacao = $interacoesTable->get($id);
         if($interacao->caminho_arquivo)
           {
             $mime = mime_content_type($interacao->caminho_arquivo);
             $this->response = $this->response->withType($mime);
             $this->response = $this->response->withFile($interacao->caminho_arquivo, ['name' => $interacao->titulo_arquivo, 'download' => true]);
             return $this->response;
           }
         else
           {
             throw new NotFoundException("Essa interação não possui arquivos");
           } 
      }
    public function testeEmail()
      {
        $this->autoRender = false;
        $email = new Email('default');
        $email->viewBuilder()->setTemplate(false);
        $email->viewBuilder()->disableAutoLayout();
        $email->addBcc('vinicius@aigen.com.br')
          ->setEmailFormat('text')
          ->setSubject("Testando a parada")
          ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
        $email->addto('vinicius@aigen.com.br');
        $email->addto('rafael@aigen.com.br');
        $email->addto('vc__abreu@live.com');
        /*
        foreach($colaboradores as $colaborador)
          {
           $email->addTo($colaborador->pessoa->email, $colaborador->pessoa->nome);
          }*/
        var_dump($email->send());
         
      }
    private function enviarEmailProspectExterno($data)
      {
        extract($data);
        extract($this->prospectConfig());
        $colaboradoresTable = TableRegistry::get('Colaboradores');
        $colaboradores = $colaboradoresTable->find('all')->contain(['Pessoas'])->toArray();
        
        $email = new Email('default');
        $email->viewBuilder()->setTemplate('prospect_externo_inserido');
        $email->addBcc('vinicius@aigen.com.br')
          ->setEmailFormat('html')
          ->setSubject("Visita agendada por " . $parentePessoa->nome . " pelo formulário externo")
          ->setViewVars(['data' => $data, 'config' => $this->emailVisitaConfig()])
          ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
        foreach($colaboradores as $colaborador)
          {
            $email->addTo($colaborador->pessoa->email, $colaborador->pessoa->nome);
          }
        (bool) $email->send();
        
      }
    private function enviarEmailProspectExternoDuplicado($data)
      {
        
        $colaboradoresTable = TableRegistry::get('Colaboradores');
        $colaboradores = $colaboradoresTable->find('all')->contain(['Pessoas'])->toArray();
        $email = new Email('default');
        $email->viewBuilder()->setTemplate('prospect_externo_duplicado');
        $email->addBcc('vinicius@aigen.com.br')
          ->setEmailFormat('html')
          ->setSubject("Tentativa de cadastro de aluno duplicado por " . $data['nome_parente'] . " pelo formulário externo")
          ->setViewVars(['data' => $data, 'now' => date('d/m/Y H:i:s')])
          ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
        foreach($colaboradores as $colaborador)
          {
           $email->addTo($colaborador->pessoa->email, $colaborador->pessoa->nome);
          }
        (bool) $email->send();
      }
    private function enviarEmailProspectExternoCliente($data)
      {
        extract($data);
        $email = new Email('default');
        $email->viewBuilder()->setTemplate('prospect_externo_cliente');
        $email->addBcc('vinicius@aigen.com.br')
          ->addBcc('fabiorighetti@gmail.com')
          ->setEmailFormat('html')
          ->setSubject("Sua visita foi agendada com sucesso!")
          ->setViewVars(['data' => $data, 'now' => date('d/m/Y H:i:s')])
          ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
        $email->addTo($parentePessoa->email, $parentePessoa->nome);
      
        (bool) $email->send();
      }
  }
?>