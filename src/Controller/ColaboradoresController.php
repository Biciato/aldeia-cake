<?php 
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;
use Cake\Mailer\Email;
use Cake\Log\Log;


class ColaboradoresController extends AppController
  {
  	public function initialize(): void
      {
      	parent::initialize();
       	$user = $this->Auth->user();
        $this->set('logged', (bool)$user);
      }
    public function index()
      {
        $this->set('titulo', "Lista de colaboradores | Aldeia Montessori");
        $this->set($this->colaboradoresConfig());
        $this->set($this->filtrosConfig());
        $colaboradoresTable = TableRegistry::get('Colaboradores');
        $colaboradores = $colaboradoresTable->find('comPessoa')->toArray();
        $this->set('colaboradores', $colaboradores);
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
    private $colaboradores_aux = 
      [
        'Unidades' => 'unidades',
        'FuncoesColaboradores' => 'funcoes',
        'Estados' => 'estados',
        'Nacionalidades' => 'nacionalidades',
        'Cores' => 'cores',
        'EstadosCivis' => 'estados_civis',
        'BooleanSelectBox' => ['callable' => 'resultadosBooleanSelectbox', 'varName' => 'boolean_selectbox'],
        'Modulos' => ['callable' => 'resultadosModulos', 'varName' => 'modulos']
      ];
    private $colaboradores_aux_email = 
      [
        'Unidades' => 'unidades',
        'Parentescos' => 'parentescos',
        'MeiosConhecimento' => 'meios_conhecimento',
      ];
    private $filtros_colaboradores = 
      [
      ];
    private $filtros_colaboradores_lista = 
      [
      ];
    
    private function colaboradoresConfig()
      {
        return $this->config($this->colaboradores_aux);
      }
    private function filtrosConfig()
      {
        $src = (strtolower($this->request->getParam('action')) === 'index') ? $this->filtros_colaboradores_lista : $this->filtros_colaboradores;
        return $this->config($src);
      }
    private function emailVisitaConfig()
      {
        return $this->config($this->colaboradores_aux_email);
      }
    public function novo()
      {
        $this->set('config', $this->colaboradoresConfig());
        $this->set($this->filtrosConfig());
        $this->set('titulo', "Novo colaborador | Aldeia Montessori");
      }
    public function inserirColaborador()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $user = $this->Auth->user();
            $colaboradoresTable      = TableRegistry::get('Colaboradores');
            $pessoasTable            = TableRegistry::get('Pessoas');
            $enderecosTable          = TableRegistry::get('Enderecos');
            $loginTable              = TableRegistry::get('Login');
            $enderecos               = [];
            $data['salario_base']    = str_replace([',', '.', ' '], '', $data['salario_base']);
            $data['vale_transporte'] = str_replace([',', '.', ' '], '', $data['vale_transporte']);
            $data['dados_filhos']    = isset($data['dados_filhos']) ? json_encode($data['dados_filhos']) : "[]";
            if(@$data['avatar']['error'] === 0)
              {
                $titulo = $data['avatar']['name'];
                $pedacos = explode('.', $titulo);
                $ext = array_pop($pedacos);
                $nome_arquivo_server = date('Y_m_d_H_i_s') . '_' . md5($titulo) . '.' . $ext;
                $data['pessoa-colaborador']['caminho_arquivo_avatar'] = WWW_ROOT . 'img/avatares/' . $nome_arquivo_server;
                $data['pessoa-colaborador']['titulo_arquivo_avatar'] = $titulo;
              }
            elseif(isset($data['remover-avatar']))
              {
                $data['pessoa-colaborador']['caminho_arquivo_avatar'] = null;
                $data['pessoa-colaborador']['titulo_arquivo_avatar'] = null;
              }
            if(@is_array($data['pessoa-colaborador']['telefones']))
              {
                $data['pessoa-colaborador']['telefones'] = json_encode($data['pessoa-colaborador']['telefones']);
              }
            else
              {
                $data['pessoa-colaborador']['telefones'] = '[]';
              }
            if(isset($data['id']))
              {
                $colaborador = $colaboradoresTable->get($data['id']);
                $colaborador = $colaboradoresTable->patchEntity($colaborador, $data, ['associated' => []]);
              }
            else
              {
                $colaborador = $colaboradoresTable->newEntity($data, ['associated' => []]);
              }
            if(!count($colaborador->getErrors()))
              {
                if(!isset($colaborador['pessoa-colaborador']['id']))
                  {
                    $pessoa = $pessoasTable->newEntity($data['pessoa-colaborador']);
                  }
                else
                  {
                    $pessoa = $pessoasTable->get($data['pessoa-colaborador']['id']);
                    $pessoa = $pessoasTable->patchEntity($pessoa, $data['pessoa-colaborador'], ['associated' => []]);
                  }
                if(@$data['avatar']['error'] === 0)
                  {
                    move_uploaded_file($data['avatar']['tmp_name'], $data['pessoa-colaborador']['caminho_arquivo_avatar']);
                  }
                $pessoasTable->save($pessoa, ['associated' => []]);
                $colaborador->pessoa_id = $pessoa->id;
                $colaboradoresTable->save($colaborador, ['associated' => []]);
                foreach($data['enderecos'] as $endereco)
                  {
                    $endereco['pessoa_id'] = $pessoa->id;
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
                $enderecosTable->saveMany($enderecos, ['associated' => []]);
                if($data['login'])
                  {
                    $data['login']['modulos_acesso']  = json_encode($data['login']['modulos_acesso']); 
                    $data['login']['unidades_acesso'] = json_encode($data['login']['unidades_acesso']); 
                    if(!isset($data['login']['id']))
                      {
                        $login = $loginTable->newEntity($data['login'],  ['associated' => []]);
                        $login->pessoa_id = $pessoa->id;
                      }
                    else
                      {
                        if(!$data['login']['senha'])
                          {
                            unset($data['login']['senha']);
                            unset($data['login']['repetir_senha']);
                          }
                        $login = $loginTable->get($data['login']['id']);
                        $login = $loginTable->patchEntity($login, $data['login'], ['associated' => []]);
                      }
                    $loginTable->save($login);
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
                                
                                case 'enderecos':
                                  $removed = $enderecosTable->get($removed_id);
                                  $enderecosTable->delete($removed);
                                  break;
                                
                              }
                          }
                      }
                  }
                $response = json_encode(['success' => true]);
              }
            else
              {
                $errors = $colaborador->getErrors();
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
    public function visualizarAvatar($id)
      {
        $pessoasTable = TableRegistry::get('Pessoas');
        $pessoa = $pessoasTable->get($id);
        if($pessoa->caminho_arquivo_avatar)
          {
            $mime = mime_content_type($pessoa->caminho_arquivo_avatar);
            $this->response = $this->response->withType($mime);
            $this->response = $this->response->withFile($pessoa->caminho_arquivo_avatar, ['name' => $pessoa->titulo_arquivo_avatar]);
            return $this->response;
          }
        else
          {
            throw new NotFoundException("Essa pessoa não possui avatar");
          }
      }
    public function baixarAvatar($id)
      {
         $pessoasTable = TableRegistry::get('Pessoas');
         $pessoa = $pessoasTable->get($id);
         if($pessoa->caminho_arquivo_avatar)
           {
             $mime = mime_content_type($pessoa->caminho_arquivo_avatar);
             $this->response = $this->response->withType($mime);
             $this->response = $this->response->withFile($pessoa->caminho_arquivo_avatar, ['name' => $pessoa->titulo_arquivo_avatar, 'download' => true]);
             return $this->response;
           }
         else
           {
             throw new NotFoundException("Essa pessoa não possui avatar");
           } 
      }
    public function sessaoListaColaboradores()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();            
            $data       = $this->request->getData();
            $key        = (int)$data['key'];
            $parent_key = (int)$data['parent_key'];
            $id         = $data['id'];
            $colaboradoresTable = TableRegistry::get('Colaboradores');
            $colaborador = $colaboradoresTable->find('all', ['conditions' => ['Colaboradores.id' => $key]])->contain(['Pessoas' => ['Login', 'Enderecos']])->first();
            $new_scope = 1;
            $block =
              [
                'key'   => $colaborador->id,
                'parent_key' => $key,
                'unique' => uniqid(),
                'parent_id' => $id,
                'colaborador' => $colaborador
              ];
            $this->set($block);
            $this->set('config', $this->colaboradoresConfig());
          }
      }
  }
?>