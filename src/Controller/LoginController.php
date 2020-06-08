<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;
use Cake\Event\Event;

class LoginController extends AppController
  {
    const TOKEN_HOUR_LIVE = (HOUR * 2);
    private $google_client_id = '309408464657-t6r6lebujdgfifcfp67lik5pnt2d7i4q.apps.googleusercontent.com';
    public function initialize(): void
      {
        parent::initialize();
        $this->Auth->allow(['index', 'login', 'info', 'createLogin', 'recover', 'confirmToken', 'googleLogin', 'facebookLogin']);
        $this->viewBuilder()->setLayout('login');
      }
    public function index($logout = false)
      {
        if($this->Auth->user())
          {
            return $this->redirect('/dashboard');
          }
        if($this->request->getSession()->check('password_changed'))
          {
            $this->set('password_changed', $this->request->getSession()->consume('password_changed'));
          }
        $this->set('logount', $logout);
        $this->viewBuilder()->disableAutoLayout();
      }
    public function info()
      {
        $this->autoRender = false;
        echo phpinfo();
        exit();
      }
    public function login()
      {
        if($this->request->is('POST'))
          {
            $this->response = $this->response->withType('application/json');
            $user = $this->Auth->identify();
            if($user)
              {
                $this->Auth->setUser($user);
                $responseBody = ['success' => true, 'landing_page' => $user['landing_page']];
              }
            else
              {
               $responseBody = ['success' => false, 'reason' => 'Credenciais incorretas'];
              }
            $this->response = $this->response->withStringBody(json_encode($responseBody));
            return $this->response;
          }
      }
    public function googleLogin()
      {
        if($this->request->is('POST'))
          {
            $this->response = $this->response->withType('application/json');
            $success        = false;
            $reason         = null;
            $data           = $this->request->getData();
            $client         = new \Google_Client(['client_id' => $this->google_client_id]);
            $verification   = $client->verifyIdToken($data['token']);
            if($verification)
              {
                $userid     = $verification['sub'];
                $loginTable = TableRegistry::get('Login');
                $login      = $loginTable->find('all',
                  [
                    'conditions' =>
                      [
                        'Pessoas.email' => $data['email']
                      ]
                  ])->contain(['Pessoas'])->first();
                if($login)
                  {
                    unset($login->senha);
                    $this->Auth->setUser($login);
                    $success = true;
                  }
                else
                  {
                    $reason = "Email não cadastrado";
                  }
              }
            else
              {
                $reason = "Erro ao autenticar o usuário";
              }
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'reason' => $reason , 'landing_page' => @$login['landing_page']]));
            return $this->response;
          }
      }
    public function facebookLogin()
      {
        if($this->request->is('POST'))
          {
            $this->response = $this->response->withType('application/json');
            $success        = false;
            $reason         = null;
            $data           = $this->request->getData();
            $loginTable     = TableRegistry::get('Login');
            $login          = $loginTable->find('all',
              [
                'conditions' =>
                  [
                    'Pessoas.email' => $data['email']
                  ]
              ])->contain(['Pessoas'])->first();
            if($login)
              {
                unset($login->senha);
                $this->Auth->setUser($login);
                $success = true;
              }
            else
              {
                $reason = "Email não cadastrado";
              }
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'reason' => $reason, 'landing_page' => @$login['landing_page']]));
            return $this->response;
          }
      }
    private function sendToken($user, $token)
      {
        $email = new Email('default');
        $email->viewBuilder()->setTemplate('token_recuperacao');
        $email->viewBuilder()->setHelpers(['Url']);
        $email->addBcc('vinicius@aigen.com.br')
          ->setTo($user->pessoa->email)
          ->setEmailFormat('html')
          ->setSubject('Recuperação de sua senha')
          ->setViewVars(['user' => $user, 'token' => $token])
          ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
        return (bool) $email->send();
      }
    public function logout()
      {
        $this->autoRender = false;
        if($this->Auth->user())
          {
            $this->request->getSession()->destroy();
            $this->Auth->logout();
          }
        return $this->redirect("/logout");
      }
    private function generateRandomString($length = 10)
      {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
      }
    public function recover()
      {
        if($this->request->is('POST'))
          {
            $loginTable = TableRegistry::get('Login');
            $user       = $loginTable->find('all',
              [
                'conditions' =>
                  [
                    'Pessoas.email' => $this->request->getData()['email']
                  ]
              ])->contain(['Pessoas'])->first();
            $response =
              [
                'success' => false,
                'reason'  => null
              ];
            if($user)
              {
                $dt = new \DateTime();
                $dt->modify('+10 minutes');
                $tokensTable = TableRegistry::get('TokensRecuperacao');
                $token = $tokensTable->find('all',
                  [
                    'conditions' =>
                      [
                        'usuario' => $user->id,
                        'data_criacao >= DATE("' . $dt->format('Y-m-d H:i:s') . '")',
                        'ativo'   => 1,
                      ]
                  ])->first();
                if(!$token)
                  {
                    $token = $tokensTable->newEntity(
                      [
                        'usuario' => $user->id,
                        'token'    => $this->generateRandomString(100)
                      ]);
                    $tokensTable->save($token);
                    if($this->sendToken($user, $token))
                      {
                        $response['success'] = true;
                      }
                    else
                      {
                        $response['reason'] = "Erro ao enviar o email";
                      }
                  }
                else
                  {
                    $response['reason'] = "Uma solicitação já foi enviada recentemente. Verifique seu email.";
                  }

              }
            else
              {
                $response['reason'] = "Usuário não encontrado";
              }
            $this->respnse = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode($response));
            return $this->response;
          }
      }
    public function recuperarSenha()
      {
        if($this->Auth->user())
          {
            return $this->redirect('/dashboard');
          }
      }
    public function recoverPassword()
      {
        if($this->request->is('POST'))
          {
            $this->respnse = $this->response->withType('application/json');
            $loginTable = TableRegistry::get('Login');
            $login = $loginTable->find('all',
              [
                'conditions' =>
                  [
                    'email' => $this->request->getData()['email']
                  ]
              ]);
            $success = true;
            $message = null;
            if(!$login->count())
              {
                $success = false;
                $message = "Email não cadastrado no sistema";
              }
            else
              {
                $user = $login->first();
                $tokensTable = TableRegistry::get('TokensRecuperacao');
                $now = new \DateTime();
                $now->modify('+24 hours');
                $existing_token = $tokensTable->find('all',
                  [
                    'conditions' =>
                      [
                        'usuario'  => $user->id,
                        'validade > DATE(\'' .  $now->format('Y-m-d H:i:s')  . '\')',
                        'ativo' => true
                      ]
                  ]);
                if(!$existing_token->count())
                  {
                    $token = sha1(rand(0, 9999));
                    $_token =
                      [
                        'usuario'  => $user->id,
                        'validade' => $now->format('Y-m-d H:i:s'),
                        'token'    => $token
                      ];
                    $token = $tokensTable->newEntity($_token);
                    if(!$tokensTable->save($token))
                      {
                        $success = false;
                        $message = "Houve um erro! Tente novamente mais tarde";
                      }
                    else
                      {
                        $this->sendEmailPasswordChange($token->id);
                      }
                  }
                else
                  {
                    $success = false;
                    $message = "Já existe uma requisição realizada pra esse email nas últimas 24 horas";
                  }
              }
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'message' => $message]));
            return $this->response;
          }
      }
    protected function sendEmailPasswordChange($token_id)
      {
        $email = new Email('default');
        $tokensTable = TableRegistry::get('TokensRecuperacao');
        $token = $tokensTable->find('all',  ['conditions' => ['TokensRecuperacao.id' => $token_id]])->contain(['Login' => ['Pessoas']])->first();
        $email->addTo($token->User->pessoa->email, $token->User->pessoa->nome);
        $email->viewBuilder()->setTemplate('recuperacao_de_senha');
        $email->bcc('vinicius@aigen.com.br')
          ->bcc('rafael@aigen.com.br')
          ->emailFormat('html')
          ->subject('Solicitação para alterar sua senha')
          ->viewVars(['token' => $token])
          ->helpers(['Url'])
          ->from(['sistema@grupocr2.com.br' => 'SISTEMA CR2 PAPERBOX']);
        $email->send();
      }
    public function confirmToken($id, $token)
      {
        $this->autoRender = false;
        $expires = new \DateTime();
        $expires->modify('+1 day');
        $tokensTable = TableRegistry::get('TokensRecuperacao');
        $token = $tokensTable->find('all',
          [
            'conditions' =>
              [
                'token' => $token,
                'TokensRecuperacao.ativo' => 1,
                'validade <= "' . 'DATE(\'' .  $expires->format('Y-m-d H:i:s')  . '\')' . '"'
              ]
          ])->contain(['Login' => ['Pessoas']]);
        $password_changed = false;
        if($token->count())
          {
            $new_pw = substr(md5(rand()), 0, 7);
            $token = $token->first();
            $loginTable = TableRegistry::get('Login');
            $login = $loginTable->patchEntity($token->User, ['senha' => $new_pw], ['validate' => false]);
            if($loginTable->save($login))
              {
                $token->ativo = false;
                $tokensTable->save($token);
                $email = new Email('default');
                $email->viewBuilder()->setTemplate('nova_senha');
                $email->addTo($token->User->pessoa->email, $token->User->pessoa->nome);
                $email->addBcc('vinicius@aigen.com.br')
                  ->setEmailFormat('html')
                  ->setSubject('Sua nova senha')
                  ->setViewVars(['new_pw' => $new_pw])
                  ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
                $email->send();
                $password_changed = true;
              }
          }
          $session = $this->request->getSession();
          $session->write('password_changed', $password_changed);
          return $this->redirect(['Controller' => 'login', 'action' => 'index']);
      }
    public function checar()
      {
        $this->autoRender = false;
        $params = $this->request->getQueryParams();
        $cobrancasTable = TableRegistry::get('Cobrancas');
        $cobranca = $cobrancasTable->get($params['email']);
        $cobranca->lida = true;
        $cobranca->lida_em = date('Y-m-d H-i-s');
        $cobranca->lida_por = $params['responsavel'];
        $cobrancasTable->save($cobranca);
        exit();
      }
  }
