<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;

class LoginAPIController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function recuperarSenha()
    {
        $email = $this->request->getData('email');

        $user = TableRegistry::getTableLocator()
            ->get('Login')
            ->find()
            ->where(['Pessoas.email' => $email], [], true)
            ->contain(['Pessoas'])
            ->first();

        if (!$user) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'status' => 404,
                    'message' => 'Este E-mail não existe em nossos cadastros',
                ]));
        }

        $codigo = rand(100000, 999999);

        $tokensTable = TableRegistry::get('TokensRecuperacao');
        $now = new \DateTime();
        $now->modify('+24 hours');
        $token = $tokensTable->newEntity([
            'usuario' => $user->id,
            'token' => $codigo,
            'validade' => $now->format('Y-m-d H:i:s'),
        ]);
        if (!$tokensTable->save($token)) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'status' => 500,
                    'message' => 'Houve um erro! Tente novamente mais tarde',
                ]));
        } else {
            if ($this->sendToken($email, $codigo)) {
                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'status' => 200,
                    ]));
            } else {
                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'status' => 500,
                        'message' => 'Houve um erro! Tente novamente mais tarde',
                    ]));
            }
        }
    }

    private function sendToken($to, $codigo)
    {
        $email = new Mailer('default');
        $email->viewBuilder()->setTemplate('recuperacao_de_senha_api');
        $email->setBcc('vinicius@aigen.com.br')
            ->setTo($to)
            ->setEmailFormat('html')
            ->setSubject('Recuperação de sua senha')
            ->setViewVars(['codigo' => $codigo])
            ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);
        return (bool) $email->deliver();
    }

    public function updatePassword($id)
    {
        if ($this->request->is(['post'])) {
            $status = 200;
            $message = 'OK';
            $loginTable = TableRegistry::getTableLocator()->get('Login');
            $login = $loginTable->find()->where(['id =' => $id])->first();
            if (!$login) {
                $status = 400;
                $message = 'Login não encontrado';
            } else {
                $data = $this->request->getData();
                $passwordIsValid = password_verify($data['password'], $login->senha);
                if (!$passwordIsValid) {
                    $status = 400;
                    $message = 'Senha atual não confere';
                } else {
                    $login->senha = password_hash($data['new_password'], PASSWORD_DEFAULT);

                    if (!$loginTable->save($login)) {
                        $status = 400;
                        $message = 'Erro em atualizar senha';
                    }
                }
            }
            return $this->response
                        ->withType('application/json')
                        ->withStatus($status)
                        ->withStringBody(json_encode([
                            'message' => $message,
                        ]));
        }
    }
}
