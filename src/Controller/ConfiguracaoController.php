<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;


class ConfiguracaoController extends AppController
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
        $this->set('titulo', 'Configurações | Aldeia Montessori');
      }
    public function configurar($auxiliar)
      {
        if(in_array($auxiliar, array_keys($this->mapa_auxiliares)))
          {
            $config = $this->mapa_auxiliares[$auxiliar];
            $auxTable = TableRegistry::get($config['tableClass']);
            $auxiliares = $auxTable->find('all', ['order' => 'ordenacao ASC, id ASC'])->toArray();
            $this->set('titulo', 'Configurar ' . $config['label'] . " | Aldeia Montessori");
            $this->set('config', $config);
            $this->set('auxiliar', $auxiliar);
            $this->set('auxiliares_cadastrados', $auxiliares);
          }
        else
          {
            $this->response = $this->response->withStatus(404);
            throw new NotFoundException("Auxiliar não encontrado!");
            return $this->response;
          }
      }
    private function ordem_novos($ordem_existentes)
      {
        if(!isset($this->ordem_auxiliares))
          {
            $this->ordem_auxiliares = count($ordem_existentes);
          }
        $this->ordem_auxiliares++;
        return $this->ordem_auxiliares;
      }
    public function inserirAuxiliares()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $auxTable = TableRegistry::get($data['tableClass']);
            $entities = 
              [
              ];
            $errors = [];
            $ordem_existentes = explode('-', $data['ordem']);
            $ordem_existentes = array_flip($ordem_existentes);
            foreach($data[$data['auxiliar']] as $k => $aux)
              {
                if(isset($aux['id']))
                  {
                    foreach($aux as $_k => $_aux)
                      {
                        if(is_array($_aux))
                          {
                            $aux[$_k] = json_encode($_aux);
                          }
                      }
                    $aux['ordenacao'] = $ordem_existentes[$aux['id']];
                    $edited   = $auxTable->get($aux['id']);
                    $auxiliar = $auxTable->patchEntity($edited, $aux);
                  }
                else
                  {
                    $aux['ordenacao'] = $this->ordem_novos($ordem_existentes);
                    $auxiliar = $auxTable->newEntity($aux);
                  }
                $_errors = $auxiliar->getErrors();
                if(count($_errors))
                  {
                    $errors[$k] = $_errors;
                  }
                else
                  {
                    array_push($entities, $auxiliar);
                  }
              }
            $success = false;
            if(!count($errors))
              {
                if($auxTable->saveMany($entities))
                  {
                    $success = true;
                  }
              } 
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(
              json_encode([
                'success' => $success,
                'errors' => $errors
              ]));
            return $this->response;
          }
      }
}
