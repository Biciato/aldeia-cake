<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Cake\ORM\TableRegistry;

class CheckTokenMiddleware implements MiddlewareInterface
{
    protected $excepts = ['token', 'recuperar-senha'];

    public function process(ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $response = $handler->handle($request);
        $path = str_replace('/api', '', $request->getPath());
        if (in_array($path, $this->excepts) === false) {
            $authHeader = $request->getHeader('Authorization');

            if (count($authHeader) < 1) {
                return $response->withType('application/json')->withStringBody(json_encode(['mensagem' => 'missing Authorization Header']));
            }
            $user = TableRegistry::getTableLocator()->get('Login')->find()->where(['api_token' => $authHeader[0]])->first();

            if (!isset($user)) {
                return $response->withType('application/json')->withStringBody(json_encode(['mensagem' => 'Invalid Token']));
            }
        }
        return $response;
    }
}
