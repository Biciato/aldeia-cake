<?php
namespace App\Controller;

use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Controller\Controller;

class PessoaApiController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function getPessoa($id): Response {
        $pessoasTable = TableRegistry::getTableLocator()->get('Pessoas');
        $pessoa = $pessoasTable->find()->where(['id =' => $id])->first();
        if (!$pessoa) {
            $status = 400;
            $message = 'Usuário não encontrado';
        } else {
            $pessoa->sexo = $pessoa->sexo == 0 ? 'Masculino' : 'Feminino';
        }

        return $this->response
                ->withType('application/json')
                ->withStatus($status ?? 200)
                ->withStringBody(json_encode([
                    'data' => $pessoa,
                    'message' => $message ?? ''
                ]));
    }

    public function updatePessoa($id) {
        if ($this->request->is(['put'])) {
            $pessoasTable = TableRegistry::getTableLocator()->get('Pessoas');
            $pessoa = $pessoasTable->find()->where(['id =' => $id])->first();
            if (!$pessoa) {
                $status = 400;
                $message = 'Pessoa não encontrada';
            } else {
                $data = $this->request->getData();
                $pessoa->nome = isset($data['nome']) ? $data['nome'] : $pessoa->nome;
                $pessoa->apelido = isset($data['apelido']) ? $data['apelido'] : $pessoa->apelido;
                $pessoa->sexo = isset($data['sexo']) ? $data['sexo'] : $pessoa->sexo;
                $pessoa->data_nascimento = isset($data['data_nascimento']) ? $data['data_nascimento'] : $pessoa->data_nascimento;
                $pessoa->empresa = isset($data['empresa']) ? $data['empresa'] : $pessoa->empresa;
                $pessoa->ocupacao = isset($data['ocupacao']) ? $data['ocupacao'] : $pessoa->ocupacao;
                $pessoa->email = isset($data['email']) ? $data['email'] : $pessoa->email;
                $pessoa->email_secundario = isset($data['email_secundario']) ? $data['email_secundario'] : $pessoa->email_secundario;
                $pessoa->cpf = isset($data['cpf']) ? $data['cpf'] : $pessoa->cpf;
                $pessoa->rg = isset($data['rg']) ? $data['rg'] : $pessoa->rg;
                $pessoa->data_expedicao_rg = isset($data['data_expedicao_rg']) ? $data['data_expedicao_rg'] : $pessoa->data_expedicao_rg;
                $pessoa->orgao_expeditor = isset($data['orgao_expeditor']) ? $data['orgao_expeditor'] : $pessoa->orgao_expeditor;
                $pessoa->telefones = isset($data['telefones']) ? $data['telefones'] : $pessoa->telefones;
                $pessoa->nacionalidade = isset($data['nacionalidade']) ? $data['nacionalidade'] : $pessoa->nacionalidade;
                $pessoa->naturalidade = isset($data['naturalidade']) ? $data['naturalidade'] : $pessoa->naturalidade;

                if (!$pessoasTable->save($pessoa)) {
                    $status = 400;
                    $message = 'Erro em atualizar pessoa';
                }
            }
        }

        return $this->response
                ->withType('application/json')
                ->withStatus($status ?? 200)
                ->withStringBody(json_encode([
                    'data' => $pessoa,
                    'message' => $message ?? ''
                ]));
    }
}
