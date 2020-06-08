<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Turma extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _getDiasSemanaArray()
    {
      $array = null;
      if($this->_fields['dias_semana'])
        {
          $array = json_decode($this->_fields['dias_semana'], true);
        }
      return ($array) ? $array : [];
    }
  protected function _getColaboradoresArray()
    {
      $array = null;
      if($this->_fields['colaboradores'])
        {
          $array = json_decode($this->_fields['colaboradores'], true);
        }
      return ($array) ? $array : [];
    }
  protected function _getColaboradoresCompleto()
    {
      $colaboradores_array = $this->_getColaboradoresArray();
      $entities = [];
      $colaboradoresTable = TableRegistry::get('Colaboradores');
      if($colaboradores_array)
        {
          foreach($colaboradores_array as $id)
            {
              $entities[$id] = $colaboradoresTable->find('all', 
                [
                  'conditions' =>
                    [
                      'Colaboradores.id' => $id,
                    ]
                ])->contain(['Pessoas'])->first();
            }
        }
      return $entities;
    }
  
}