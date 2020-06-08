<?php 
namespace App\Controller;

use Cake\ORM\TableRegistry;

class DocumentosController extends AppController
  {
  	public function initialize(): void
      {
      	parent::initialize();
       	$user = $this->Auth->user();
       	if(!$user)
       	  {
       	  	$this->redirect(
       	  		[
       	  			'controller' => 'login',
       	  			'action'     => 'index'
       	  		]);
       	  }
      }
    public function index()
      {
      	$documentosTable = TableRegistry::get('Documentos');
   	  	$documentos  = $documentosTable->find('all')->order(['ordenacao ASC'])->toArray();
   	  	$this->set('documentos', $documentos);
        $this->set('titulo', "Documentos | Aldeia Montessori");
      }
    public function ver($tipo, $id)
      {
        $capitulosTable = TableRegistry::get('Capitulos');
        $documentosTable = TableRegistry::get('Documentos');
        if($tipo === 'documento')
          {
            $documento = $documentosTable->get($id);
            $capitulos = $capitulosTable->find('all', ['conditions' => 
              [
                'documento' => $id,
                'pai IS NULL'
              ]])->contain(['Documentos'])->order(['Capitulos.ordenacao ASC', 'Capitulos.nome'])->toArray();
            $this->set('capitulos', $capitulos);
            $titulo = $documento->nome;
            $this->set('documento', $documento);
          }
        elseif($tipo === 'capitulo')
          {
            $capitulo       = $capitulosTable->find('all', ['conditions' => ['Capitulos.id' => $id]])->contain(['Documentos'])->first();
            $this->set('capitulo', $capitulo);
            $titulo = $capitulo->nome;
          }
        $this->set('tipo', $tipo);
        $this->set('titulo', $titulo . " | Aldeia Montessori");
      }
    public function adicionarDocumento()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $documentosTable = TableRegistry::get('Documentos');
            $data['descricao'] = str_replace('%26', '&', $data['descricao']);
            $documento = $documentosTable->newEntity($data);
            $success = false;
            $errors  = $documento->getErrors();
            if($documentosTable->save($documento))
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'errors' => $errors]));
            return $this->response;
          }
      }
    public function editarDocumento()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $documentosTable = TableRegistry::get('Documentos');
            $data['conteudo'] = str_replace('%26', '&', $data['conteudo']);
            $documento = $documentosTable->get($data['id']);
            $documento = $documentosTable->patchEntity($documento, $data);
            $success = false;
            $errors  = $documento->getErrors();
            if($documentosTable->save($documento))
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'errors' => $errors]));
            return $this->response;
          }
      }
    public function carregarDocumentos()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->setLayout(false);
            $documentosTable = TableRegistry::get('Documentos');
            $documentos  = $documentosTable->find('all')->order(['ordenacao ASC'])->toArray();
            $this->set('documentos', $documentos);
          }
      }
    public function carregarCapitulos()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $this->viewBuilder()->setLayout(false);
            $capitulosTable = TableRegistry::get('Capitulos');
            $conditions     = ($data['dados']['tipo'] === 'documento') ? 
              [
                'pai IS NULL',
                'documento' => $data['dados']['id']
              ] :
              [
                'pai' => $data['dados']['id']
              ];
            $capitulos      = $capitulosTable->find('all', 
              [
               'conditions' => $conditions 
              ])->order(['Capitulos.ordenacao ASC', 'Capitulos.nome'])->toArray();
            $this->set('capitulos', $capitulos);
          }
      }
    public function adicionarCapitulo()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $data['pai'] = ($data['pai']) ? $data['pai'] : null;
            $capitulosTable = TableRegistry::get('Capitulos');
            $ultimo = $capitulosTable->find('all')->order(['ordenacao DESC'])->first();
            $data['conteudo'] = str_replace('%26', '&', $data['conteudo']);
            $capitulo = $capitulosTable->newEntity($data);
            $capitulo->ordenacao = ((int)$ultimo->ordenacao + 1);
            $success = false;
            $errors  = $capitulo->getErrors();
            if($capitulosTable->save($capitulo))
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'errors' => $errors]));
            return $this->response;
          }
      }
    public function editarCapitulo()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $capitulosTable = TableRegistry::get('Capitulos');
            $data['conteudo'] = str_replace('%26', '&', $data['conteudo']);
            $capitulo = $capitulosTable->get($data['id']);
            $capitulo = $capitulosTable->patchEntity($capitulo, $data);
            $success = false;
            $errors  = $capitulo->getErrors();
            if($capitulosTable->save($capitulo))
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
    public function ordenar()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $flip = array_flip($data);
            $capitulosTable = TableRegistry::get('Capitulos');
            $capitulos = $capitulosTable->find('all', ['conditions' => ['id IN(' . implode(', ', $data) . ')']])->toArray();
            foreach ($capitulos as &$capitulo) 
              {
                $capitulo->ordenacao = $flip[$capitulo->id];
              }
            $success = false;
            if($capitulosTable->saveMany($capitulos))
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success]));
            return $this->response;
          }
      }
    public function ordenarDocumentos()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $flip = array_flip($data);
            $documentosTable = TableRegistry::get('Documentos');
            $documentos = $documentosTable->find('all', ['conditions' => ['id IN(' . implode(', ', $data) . ')']])->toArray();
            foreach ($documentos as &$documento) 
              {
                $documento->ordenacao = $flip[$documento->id];
              }
            $success = false;
            if($documentosTable->saveMany($documentos))
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success]));
            return $this->response;
          }
      }
  }
?>