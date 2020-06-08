<?php 
namespace App\Controller;

use Cake\ORM\TableRegistry;

class IndicacoesController extends AppController
  {
  	public function initialize(): void
      {
      	parent::initialize();
       	$user = $this->Auth->user();
      }
    public function index()
      {
        $this->set('titulo', "Faça uma indicação | Aldeia Montessori");
      }
    public function adicionarIndicacao()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $prospectsTable = TableRegistry::get('Prospects');
            $data['autor_indicacao'] = $this->Auth->user()['id'];
            $prospect       = $prospectsTable->newEntity($data);
            $success        = false;
            $errors         = $prospect->getErrors();
            if($prospectsTable->save($prospect))
              {
                $success    = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'errors' => $errors]));
            return $this->response;
          }
      }
  }
?>