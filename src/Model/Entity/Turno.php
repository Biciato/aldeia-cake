<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Turno extends Entity {
 
  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _getPermanenciasEntities()
    {
    	$permanenciasTable = TableRegistry::get('Permanencias');
    	$permanencias = $permanenciasTable->find('all', ['order' => 'ordenacao ASC'])->toArray();
    	$arr = [];
    	foreach($permanencias as $permanencia)
    	  {
    	  	if(in_array($this->_fields['id'], $permanencia->turnos_array))
    	  	  {
	    	  	$arr[$permanencia->id] = $permanencia;
    	  	  } 
    	  }
    	return $arr;
    }
}