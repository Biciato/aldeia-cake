<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Curso extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _getAgrupamentosArray()
    {
    	$array = null;
    	if($this->_fields['agrupamentos'])
    	  {
    	  	$array = json_decode($this->_fields['agrupamentos'], true);
    	  }
    	return ($array) ? $array : [];
    }
  public function _getAgrupamentosEntities()
    {
      if($this->_getAgrupamentosArray())
        {
          $agrupamentosTable = TableRegistry::get('Agrupamentos');
          return $agrupamentosTable->find('all', ['conditions' => 
            [
                'id IN (' . implode(', ', $this->_getAgrupamentosArray()) . ')' 
            ]
          ])->toArray();
        } 
      return [];
    }
}