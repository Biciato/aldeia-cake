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
    private function generateToken($id, $email)
    {
        $key = 'aldeiasecret';

        //Header Token
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        //Payload - Content
        $payload = [
            'exp' => Time::now()->getTimestamp(),
            'uid' => $id,
            'email' => $email,
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
        return $header . '.' . $payload . '.' . $sign;
    }

    /**
     * Verificar e retorna um token de autenticação com dados do usuário
     * @return Response
     */
    public function token(): Response
    {
        $data = $this->request->getData();
        $loginTable = TableRegistry::getTableLocator()->get('Login');
        $pessoasTable = TableRegistry::getTableLocator()->get('Pessoas');
        $parentesTable = TableRegistry::getTableLocator()->get('Parentes');
        $colaboradoresTable = TableRegistry::getTableLocator()->get('Colaboradores');
        $funcoesColaboradoresTable = TableRegistry::getTableLocator()->get('FuncoesColaboradores');
        $message = 'success';
        $status = 200;
        $pessoa = $pessoasTable->find()->where(['email' => $data['username']])->first();
        if (isset($pessoa)) {
            $user = $loginTable->find()->where(['Pessoas.email' => $pessoa->email], [], true)->contain(['Pessoas'])->first();
            if (!password_verify($data['password'], $user->senha)) {
                $status = 400;
                $message = 'Senha inválida';
            } else {
                $token = $this->generateToken($user->id, $data['username']);
            }
        } else {
            $status = 400;
            $message = 'Usuário não encontrado';
        }

        if (isset($token)) {
            if ($parentesTable->find()->where(['pessoa_id' => $pessoa->id])->first() !== null) {
                $role = 'Parente';
            } else {
                if ($colaboradoresTable->find()->where(['pessoa_id' => $pessoa->id])->first() !== null) {
                    $role = $funcoesColaboradoresTable->find()->where(['id' => $colaborador->funcao_id])->first();
                } else {
                    $role = 'Admin';
                }
            }
            $user->api_token = $token;
            $loginTable->save($user);
            $credentials = [
                'token' => $token,
                'id' => $user->id,
                'id_pessoa' => $pessoa->id,
                'role' => $role,
                'name' => $pessoa['nome']
            ];
        }

        return $this->response
                ->withType('application/json')
                ->withStatus($status)
                ->withStringBody(json_encode([
                    'credentials' => $credentials ?? null,
                    'message' => $message
                ]));
    }
}
