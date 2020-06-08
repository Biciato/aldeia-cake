<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Log\Log;

class Capitulo extends Entity 
  {
    protected $_accessible = 
      [
        '*' => true
      ];
    protected $_virtual =
      [
      ];
    protected $_table = null;
    protected function table()
      {
        if(is_null($this->_table))
          {
            $this->_table = TableRegistry::get('Capitulos');
          }
        return $this->_table;
      }
    protected function _getAnterior()
      {
        $anterior = null;
        if(!@is_null($this->_fields['pai']))
          {
            $table = $this->table();
              $anterior = $table->find('all',['conditions'=> ['id' => $this->_fields['pai']]])->select(['id', 'nome', 'conteudo'])->first();
          }
        return $anterior;
      }
    protected function _getProximos()
      {
        $table = $this->table();
        $proximos = $table->find('all', ['conditions' => 
          [
            'pai' => $this->_fields['id']
          ]])->order(['ordenacao ASC', 'nome ASC'])->select(['id', 'nome', 'conteudo'])->toArray();
        return $proximos;
      }
    protected function _getCaminho()
      {
        $caminho = 
          [
            [
              'id'   => $this->_fields['id'],
              'nome' => $this->_fields['nome']
            ]
          ];
        $table = $this->table();
        $pai   = @$this->_fields['pai'];
        while(!is_null($pai))
          {
            $pai_entity = $table->find('all', ['conditions' => ['id' => $pai]])->select(['id', 'nome', 'pai'])->first();
            array_push($caminho, 
              [
                'id'   => $pai_entity->id,
                'nome' => $pai_entity->nome
              ]);
            $pai = $pai_entity->pai;
          }
        $documentosTable = TableRegistry::get('Documentos');
        $documento       = $documentosTable->get($this->_fields['documento']);
        array_push($caminho, 
          [
            'id' => $documento->id,
            'nome' => $documento->nome
          ]); 
        return array_reverse($caminho);
      }
  }