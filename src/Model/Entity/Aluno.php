<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Aluno extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];

  protected function _getDataCriacaoFormatada()
    {
    	 if(!$this->_fields['data_criacao'])
        {
          return false;
        }
      $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data_criacao']))));
      return $dt->format('d/m/Y H:i');
    }
  protected function _getDataMatriculaFormatada()
    {
       if(!$this->_fields['data_matricula'])
        {
          return false;
        }
      $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data_matricula']))));
      return $dt->format('d/m/Y H:i');
    }
  protected function _getDataInicioFormatada()
    {
       if(!$this->_fields['data_inicio'])
        {
          return false;
        }
      $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data_inicio']))));
      return $dt->format('d/m/Y H:i');
    }
  protected function _getServicosArray()
    {
      $array = null;
      if($this->_fields['servicos'])
        {
          $array = json_decode($this->_fields['servicos'], true);
        }
      return ($array) ? $array : [];
    }
  protected function _getTurmasArray()
    {
      $array = null;
      if($this->_fields['turmas'])
        {
          $array = json_decode($this->_fields['turmas'], true);
        }
      return ($array) ? $array : [];
    }
  protected function _getFinanceiroArray()
    {
      $array = null;
      if($this->_fields['financeiro'])
        {
          $array = json_decode($this->_fields['financeiro'], true);
        }
      return ($array) ? $array : [];
    }
  protected function _setDataNascimento($data)
    {
      if($data)
        {
          $data = implode('-', array_reverse(explode('/', $data)));
        }
      return $data;
    }
   protected function _setDataMatricula($data)
    {
      if($data)
        {
          $data = implode('-', array_reverse(explode('/', $data)));
        }
      return $data;
    }
  protected function _setDataInicio($data)
    {
      if($data)
        {
          $data = implode('-', array_reverse(explode('/', $data)));
        }
      return $data;
    }
  protected function _getDiaVencimento()
    {
      if(strlen($this->_fields['dia_vencimento']) == 1)
        {
          return '0' . $this->_fields['dia_vencimento'];
        }
      return $this->_fields['dia_vencimento'];
    }
  protected function _getResponsavel()
    {
      $responsavel = null;
      if($this->_fields['responsavel_id'])
        {
          $parentesTable = TableRegistry::get('Parentes');
          $responsavel = $parentesTable->find('all', 
            [
              'conditions' =>
                [
                  'Parentes.id' => $this->_fields['responsavel_id']
                ]
            ])->contain(['Pessoas' => ['Enderecos']])->first();
        }
      return $responsavel;
    }
  public function _getTurmasEntities()
    {
      $turmasTable = TableRegistry::get('Turmas');
      $turmas_array = $this->_getTurmasArray();
      $entities = [];
      foreach($turmas_array as $servico => $turma)
        {
          $entities[$servico] = $turmasTable->find('all',
            [
              'conditions' => 
                [
                  'Turmas.id' => $turma
                ]
            ])->contain(['ServicosAux'])->first();
        }
      return $entities;
    }
}