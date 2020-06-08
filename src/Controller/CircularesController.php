<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;
use Cake\Routing\Router;

class CircularesController extends AppController
  {
    public function initialize(): void
      {
        parent::initialize();
        $user = $this->Auth->user();
        $this->set('logged', (bool)$user);
      }
    public function nova()
      {
        $unidadesTable = TableRegistry::get('Unidades');
        $unidades = $unidadesTable->find('all', 
          [
            'conditions' =>
              [
                'ativo' => true
              ]
          ])->toArray();
        $this->set('unidades', $unidades);
        $this->set('titulo', 'Nova circular | Aldeia Montessori');
      }
    public function buscarTurmas()
      {
        $this->viewBuilder()->disableAutoLayout();
        $unidadesTable = TableRegistry::get('Unidades');
        $turmasTable   = TableRegistry::get('Turmas');
        $alunosTable   = TableRegistry::get('Alunos');
        $data          = $this->request->getData();
        $unidades = $unidadesTable->find('all', 
          [
            'conditions' =>
              [
                'Unidades.id IN(' . implode(', ', $data['unidades']) . ')'
              ]
          ])->toArray();
        foreach($unidades as $unidade)
          {
            $turmas_servico = [];
            $turmas = $turmasTable->find('all', 
              [
                'conditions' =>
                  [
                    'Turmas.unidade' => $unidade->id,
                    'Turmas.ano_letivo' => date('Y')
                  ]
              ])->contain(['ServicosAux'])->toArray();
            foreach($turmas as $turma)
              {
                if(!isset($turmasServico[$turma->Servico->id]))
                  {
                    $servico_nome = ($turma->Servico->nome == 'Hotelaria') ? 'Sistema Creche' : $turma->Servico->nome; 
                    $turmas_servico[$turma->Servico->id] =
                      [
                        'servico' =>
                          [
                            'id' => $turma->Servico->id,
                            'nome' => $servico_nome
                          ],
                        'turmas' =>
                          [

                          ]
                      ];
                    }
                $alunosTable = TableRegistry::get('Alunos');
                $alunos = $alunosTable->find('all', 
                  [
                    'conditions' =>
                      [
                        'Alunos.turmas LIKE "%\"' . $turma->servico . '\":\"' . $turma->id . '\"%"'
                      ]
                  ])->contain(['Pessoas'])->toArray();
                $turma->alunos = $alunos;
                array_push($turmas_servico[$turma->Servico->id]['turmas'], $turma);
              }
            $unidade->turmas_servico = $turmas_servico;
          }
        $this->set('unidades', $unidades);
      }
    public function enviar()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $this->response = $this->response->withType('application/json');
            $arquivo = $data['arquivo'];
            $data['arquivo'] = '';
            $lotesCircularesTable = TableRegistry::get('LotesCirculares');
            $lote = $lotesCircularesTable->newEntity($data);
            if(count($lote->getErrors()) > 0)
              {
                $this->response = $this->response->withStringBody(json_encode(
                  [
                    'success' => false,
                    'errors' => $lote->getErrors()
                  ]));
                return $this->response;
              }
            else
              {
                $success = true;
                $errors = [];
                if(($data['tipo_circular'] == 'arquivo_pdf')&&($data['arquivo']->getError() != 0))
                  {
                    $success = false;
                    $errors['arquivo']['upload'] = "Insira um arquivo PDF para ser enviado na circular";
                  }
                else
                  {
                    $alunos = [];
                    $turmas = [];
                    $alunos_turmas = [];
                    foreach($data['alunos'] as $ids)
                      {
                        $exp = explode('|', $ids);
                        if(!in_array($exp[0], $alunos))
                          {
                            array_push($alunos, $exp[0]);
                          }
                        if(!in_array($exp[1], $turmas))
                          {
                            array_push($turmas, $exp[1]);
                          }
                        if(!in_array($exp[0], array_keys($alunos_turmas)))
                          {
                            $alunos_turmas[$exp[0]] = $exp[1];
                          }
                      }
                    $lote->alunos = json_encode($alunos);
                    $lote->turmas = json_encode($turmas);
                    if((($data['tipo_circular'] == 'arquivo_pdf')&&($data['arquivo']->getError() == 0)))
                      {
                        $titulo_arquivo = $data['arquivo']->getClientFilename();
                        move_uploaded_file($data['arquivo']->getStream()->getMetadata('uri'), WWW_ROOT . 'circulares/' . $titulo_arquivo);
                        $lote->arquivo = $titulo_arquivo;
                      }
                    elseif($data['tipo_circular'] == 'texto_html')
                      {
                        $lote->texto = $data['conteudo_html'];
                      }
                    $lotesCircularesTable->save($lote);
                    $alunosTable     = TableRegistry::get('Alunos');
                    $circularesTable = TableRegistry::get('Circulares');
                    foreach($alunos as $aluno_id)
                      {
                        $aluno = $alunosTable->find('all', 
                          [
                            'conditions' =>
                              [
                                'Alunos.id' => $aluno_id
                              ]
                          ])->contain(['Parentes' => ['Pessoas']])->first();
                        foreach($aluno->parentes as $parente)
                          {
                            if(in_array('2', $parente->atribuicoes_array))
                              {
                                $circular = $circularesTable->newEntity(
                                  [
                                    'pessoa' => $parente->pessoa->id,
                                  ]);
                                $circular->lote = $lote->id;
                                $circular->aluno = $aluno_id;
                                $circular->turma = $alunos_turmas[$aluno_id];
                                $circularesTable->save($circular);
                              }
                          }                        
                      }
                    $this->enviarCirculares($lote);
                  }
                $this->response = $this->response->withStringBody(json_encode(
                  [
                    'success' => $success,
                    'errors' => $errors
                  ]));
                return $this->response;
              }
          }
      }
    public function reenviar()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $lotesCircularesTable = TableRegistry::get('LotesCirculares');
            $lote = $lotesCircularesTable->get($data['id']);
            $this->enviarCirculares($lote, true);
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(
              [
                'success' => true
              ]));
            return $this->response;
          }
      }
    private function enviarCirculares($lote, $reenvio = false)
      {
        $circularesTable = TableRegistry::get('Circulares');
        $conds = 
          [
            'lote' => $lote->id
          ];
        if($reenvio)
          {
            $conds['lido'] = false;
          }
        $circulares      = $circularesTable->find('all', 
          [
            'conditions' => $conds
          ])->contain(['Pessoas'])->toArray();
        foreach($circulares as $circular)
          {
            $conteudo = ($lote->tipo_circular == 'arquivo_pdf') ? 'Uma nova circular foi enviada. Confira <a href="' . Router::url('/', true) . 'circulares/abrir-circular/' . $circular->id . '/"> clicando aqui.</a>' : $lote->texto; 
            $email = new Email('default');
            $email->viewBuilder()->setTemplate('circular');
            $email->addBcc('vinicius@aigen.com.br')
              ->setEmailFormat('html')
              ->setSubject($lote->titulo)
              ->setViewVars(['conteudo' => $conteudo])
              ->setFrom(['sistema@aldeiamontessori.com.br' => 'Aldeia Montessori']);

            $email->addTo([$circular->destinatario->email => $circular->destinatario->nome]);
            $email->send();
          }
      }
    public function abrirCircular($circular_id)
      {
        $circularesTable = TableRegistry::get('Circulares');
        $circular = $circularesTable->find('all', 
          [
            'conditions' =>
              [
                'Circulares.id' => $circular_id
              ]
          ])->contain(['LotesCirculares'])->first();
        $circular->lido = 1;
        $circular->lido_em = date('Y-m-d H-i-s');
        $circularesTable->save($circular);
        $arquivo = WWW_ROOT . 'circulares/' . $circular->Lote->arquivo;
        $this->response = $this->response->withFile($arquivo);
        return $this->response;
      }
    public function enviadas()
      {
        $lotesCircularesTable = TableRegistry::get('LotesCirculares');
        $lotesCirculares      = $lotesCircularesTable->find('all', 
          [
            'order' =>
              [
                'data_criacao DESC' 
              ]
          ])->contain(['Circulares'])->toArray();
        $this->set('lotes', $lotesCirculares);
        $this->set('titulo', 'Circulares enviadas | Aldeia Montessori');
      }
  }