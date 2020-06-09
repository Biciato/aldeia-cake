<?php
namespace App\Controller;

use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Controller\Controller;

class TokenController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Verificar e retorna um token de autenticação com dados do usuário
     * @return Response
     */
    public function token(): Response
    {
        $data = $this->request->getData();
        $table = TableRegistry::getTableLocator()->get('Login');
        $message = 'success';
        $status = 200;

        $query = $table->query();

        $user = $query->where(['Pessoas.email' => $data['username']], [], true)->contain(['Pessoas'])->first();
        if (!isset($user)) {
            $status = 400;
            $message = 'Usuário não encontrado';
        }

        if (!password_verify($data['password'], $user->senha)) {
            $status = 400;
            $message = 'Senha inválida';
        }
        $key = 'aldeiasecret';

        //Header Token
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        //Payload - Content
        $payload = [
            'exp' => Time::now()->getTimestamp(),
            'uid' => $user->id,
            'email' => $data['username'],
        ];

        //JSON
        $header = json_encode($header);
        $payload = json_encode($payload);

        //Base 64
        $header = base64_encode($header);
        $payload = base64_encode($payload);

        //Sign
        $sign = hash_hmac('sha256', $header . "." . $payload, $key, true);
        $sign = base64_encode($sign);

        //Token
        $token = $header . '.' . $payload . '.' . $sign;
        if ($status === 200) {
            $user->api_token = $token;
            $table->save($user);
        }

        return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'status' => $status,
                    'credentials' => [
                        'token' => isset($status) ? '' : $token,
                        'id' => isset($status) ? '' : $user->id
                    ],
                    'message' => $message
                ]));
    }
}
