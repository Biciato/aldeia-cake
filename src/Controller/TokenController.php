<?php
namespace App\Controller;

use Firebase\JWT\JWT;
use Cake\Http\Response;
use Cake\Controller\AppController;

class TokenController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Verificar e retorna um token de autenticação com dados do usuário
     * @return Response
     */
    public function token(): Response
    {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $privateKey = file_get_contents('config/jwt.key');
            $user = $result->getData();
            $payload = [
                'iss' => 'myapp',
                'sub' => $user->id,
                'exp' => time() + 60,
            ];
            $json = [
                'token' => JWT::encode($payload, $privateKey, 'RS256'),
            ];
        } else {
            $this->response = $this->response->withStatus(401);
            $json = ['error' => 'invalid credentials'];
        }
        $this->set(compact('json'));
        $this->viewBuilder()->setOption('serialize', 'json');

        return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($json));
    }

    public function teste()
    {
        $service = $this->request->getAttribute('authentication');
        $this->set([
            'recipes' => ['mensagem' => 'teste'],
            '_serialize' => ['recipes']
        ]);
        $this->viewBuilder()->setOption('serialize', 'json');

        return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode(['mensagem' => 'teste']));
    }

}
