<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;

class OcorrenciasController extends AppController
  {
    public function initialize(): void
      {
        parent::initialize();
        $user = $this->Auth->user();
        $this->set('logged', (bool)$user);
      }
    public function index()
      {
        $this->set('titulo', 'Ocorrências | Aldeia Montessori');
        $pessoasTable = TableRegistry::get('Pessoas');
        $pessoas = $pessoasTable->find('all')->contain(['Alunos', 'Colaboradores'])->where(['OR' => ['Alunos.id IS NOT NULL', 'Colaboradores.id IS NOT NULL']])->order(['Pessoas.nome ASC'])->toArray();
        $lista_pessoas = [];
        foreach($pessoas as $pessoa)
          {
            $nome = $pessoa->nome;
            if($pessoa->aluno)
              {
                foreach($pessoa->aluno->turmas_entities as $turma)
                  {
                    if($turma->servico == $this->escolaridade)
                      {
                        $nome .= " T" . str_pad($turma->nome, 3, "0", STR_PAD_LEFT);
                      }
                      if($turma->servico == $this->hotelaria)
                      {
                        $nome .= " SC" . str_pad($turma->nome, 3, "0", STR_PAD_LEFT);
                      }
                  }
              }
            array_push($lista_pessoas, $nome);
          }
        $tagsTable = TableRegistry::get('Tags');
        $tags = $tagsTable->find('list', ['keyField' => 'id', 'valueField' => 'nome'])->toArray();
        $lista_tags = [];
        foreach($tags as $tag)
          {
            array_push($lista_tags, $tag);
          }
        $ocorrenciasTable = TableRegistry::get('Ocorrencias');
        $ocorrencias = $ocorrenciasTable->find('all')->where(['comentario_de IS NULL'])->order(['Ocorrencias.data_criacao DESC'])->contain(['Pessoas', 'Comentarios' => ['Pessoas']])->limit(6)->toArray();
        $unidadesTable = TableRegistry::get('Unidades');
        $unidades = $unidadesTable->find('all', 
          [
            'conditions' =>
              [
                'ativo' => 1
              ]
          ])->order(['ordenacao ASC'])->toArray();
        $this->set('unidades', $unidades);
        $this->set('ocorrencias', $ocorrencias);
        $this->set('lista_tags', $lista_tags);
        $this->set('lista_pessoas', $lista_pessoas);
        $this->set('pessoas', $pessoas);
        $this->set('tags', $tags);
      }
    public function inserir()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $success = true;
            $mensagem = null;
            $arquivo = $data['arquivo'];
            $data['arquivo'] = "";
            $this->response = $this->response->withType('application/json');
            $ocorrenciasTable = TableRegistry::get('Ocorrencias');
            $ocorrencia = $ocorrenciasTable->newEntity($data);
            $user = $this->Auth->user();
            $ocorrencia->registrado_por = $user['id'];
            if((empty($data['texto']))||($data['tipo'] == ''))
              {
                $success = false;
                $mensagem = "Insira o texto e selecione o tipo da ocorrência";
                $this->response = $this->response->withStringBody(json_encode(['mensagem' => $mensagem, 'success' => $success]));
                return $this->response;
              }
            if(($arquivo->getError() == 0))
              {
                $titulo_arquivo = $arquivo->getClientFilename();
                move_uploaded_file($arquivo->getStream()->getMetadata('uri'), WWW_ROOT . 'ocorrencias/' . $titulo_arquivo);
                $ocorrencia->arquivo = $titulo_arquivo;
              }
            $_mencoes = [];
            $_tags    = [];
            $pessoasTable = TableRegistry::get('Pessoas');
            $pessoas = $pessoasTable->find('all', ['conditions' => ['nome !=' => '']])->contain(['Alunos', 'Colaboradores'])->where(['OR' => ['Alunos.id IS NOT NULL', 'Colaboradores.id IS NOT NULL']])->order(['Pessoas.nome ASC'])->toArray();
            $tagsTable = TableRegistry::get('Tags');
            $tags = $tagsTable->find('all', ['keyField' => 'id', 'valueField' => 'nome'])->toArray();
            foreach($pessoas as $pessoa)
              {
                if(strpos($data['texto'], '@' . $pessoa->nome) !== false)
                  {
                    array_push($_mencoes, (string)$pessoa->id);
                  }
              }
            foreach($tags as $tag)
              {
                if(strpos($data['texto'], '#' . $tag->nome) !== false)
                  {
                    array_push($_tags, (string)$tag->id);
                  }
              }
            $ocorrencia->tags = json_encode($_tags);
            $ocorrencia->mencoes = json_encode($_mencoes);
            if(!$ocorrenciasTable->save($ocorrencia))
              {
                $success = false;
                $mensagem = "Erro ao salvar a ocorrência";
              }
            $this->response = $this->response->withStringBody(json_encode(['success' => $success, 'mensagem' => $mensagem]));
            return $this->response;
          }
      }
    public function marcarLido()
      {
        if($this->request->is('POST'))
          {
            $data = $this->request->getData();
            $ocorrenciasTable = TableRegistry::get('Ocorrencias');
            $ocorrencia = $ocorrenciasTable->get($data['id']);
            $visto_por = (json_decode($ocorrencia->visto_por, true)) ? json_decode($ocorrencia->visto_por, true) : [];
            $user = $this->Auth->user();
            if(!in_array($user['id'], $visto_por))
              {
                array_push($visto_por, $user['id']);
              } 
            $ocorrencia->visto_por = json_encode($visto_por);
            $success = false;
            if($ocorrenciasTable->save($ocorrencia))
              {
                $success = true;
              }
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode(['success' => $success]));
            return $this->response;
          }
      }
    public function buscaFormComentarios()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            $this->set('id', $data['id']);
          }
      }
    public function pesquisaAvancada()
      {
        if($this->request->is('POST'))
          {
            $this->viewBuilder()->disableAutoLayout();
            $data = $this->request->getData();
            /*{ ["unidades"]=> array(2) { [0]=> string(1) "1" [1]=> string(1) "2" } ["pessoas"]=> array(1) { [0]=> string(3) "295" } ["data_inicial"]=> string(10) "02/04/2020" ["data_final"]=> string(10) "14/04/2020" ["tags"]=> array(1) { [0]=> string(1) "1" } } */
            $conds = [];
            $or =
            [
              
            ];
            if(@count($data['pessoas']) > 0)
              {
                $or[] = 'registrado_por IN (' . implode(', ' , $data['pessoas']) . ')';
                foreach($data['pessoas'] as $pessoa_id)
                  {
                    $or[] = 'mencoes LIKE "%' . $pessoa_id . '%"';
                  }
              }
            if(count($or) > 0)
              {
                $conds['OR'] = $or;
              }
            if(@count($data['tags']) > 0)
              {
                foreach($data['tags'] as $tag)
                  {
                    $or[] = 'tags LIKE "%' . $tag . '%"';
                  }
              }
            if($data['data_inicial'] != "")
              {
                $conds[] = 'Ocorrencias.data_criacao >= "' .  implode('-', array_reverse(explode('/', $data['data_inicial']))) . '"';
              }
            if($data['data_final'] != "")
              {
                $conds[] = 'Ocorrencias.data_criacao <= "' .  implode('-', array_reverse(explode('/', $data['data_final']))) . '"';
              }
            if(count($conds) == 0)
              {
                $conds = false;
              }
            $ocorrenciasTable = TableRegistry::get('Ocorrencias');
            $ocorrencias = $ocorrenciasTable->find('all', 
              [
                'conditions' => $conds
              ])->contain(['Pessoas' => ['Alunos', 'Colaboradores']])->toArray();
            $this->set('ocorrencias', $ocorrencias);
            $this->set('user', $this->Auth->user());
          }
      }
  }