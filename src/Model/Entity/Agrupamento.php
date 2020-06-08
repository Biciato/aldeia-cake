<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Agrupamento extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _getNiveisArray()
    {
    	$array = null;
    	if($this->_fields['niveis'])
    	  {
    	  	$array = json_decode($this->_fields['niveis'], true);
    	  }
    	return ($array) ? $array : [];
    }
  public function _getNiveisEntities()
    {
      if($this->_getNiveisArray())
        {
          $agrupamentosTable = TableRegistry::get('Niveis');
          return $agrupamentosTable->find('all', ['conditions' => 
            [
                'id IN (' . implode(', ', $this->_getNiveisArray()) . ')' 
            ]
          ])->toArray();
        } 
      return [];
    }
}