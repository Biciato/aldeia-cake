<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;


class DashboardController extends AppController
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
        $this->set('titulo', 'Dashboard | Aldeia Montessori');
      }
    public function alterarSenha()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $user = $this->Auth->user();
            $loginTable = TableRegistry::get('Login');
            $login = $loginTable->get($user['id']);
            $response = [];
            $changed = $loginTable->patchEntity($login, $data, ['validate' => 'changePassword']);
            $success = false;
            $errors = $changed->getErrors();
            if(count($errors))
              {
                $response['errors'] = $errors;
              }
            elseif($loginTable->save($changed))
              {
                $success = true;
              }
            $response['success'] = $success;
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode($response));
            return $this->response;
          }
      }
    public function lerNotificacoes()
      {
        if($this->request->is('POST'))
          {
            $user = $this->Auth->user();
            $notificacoesTable = TableRegistry::get('Notificacoes');
            $naoLidas = $notificacoesTable->find('all')->where(
              [
                'usuario' => $user['id'],
                'lida IS NULL'
              ])->toArray();
            foreach($naoLidas as &$nl)
              {
                if($nl->multiplos_usuarios)
                  {

                  }
                else
                  {
                    $nl->lida = true;
                  }
              }
            $notificacoesTable->saveMany($naoLidas);
            $this->response = $this->response->withStringBody(json_encode(['success' => true]));
            return $this->response;
          }
      }
}
