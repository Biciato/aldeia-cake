<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Servico extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
 protected function _getValorAtual()
   {
 	  $valoresTable = TableRegistry::get("Valores");
 	  $valor_atual = $valoresTable->find('all', 
  		[
		  'conditions' =>
		    [
		  	  'servico' => $this->_fields['id'],
		  	  'AND' => 
		  	    [
		  	      'data_inicio <= "' . date('Y-m-d') . '"',
		  	  	  'data_final >= "' . date('Y-m-d') . '"' 
		  	    ]
		    ]
  		]
 	  )->first();
 	  if(!$valor_atual)
 	    {
    	  $valor_atual = $valoresTable->find('all', 
	  		[
	  		  'conditions' =>
	  		    [
	  		  	  'servico' => $this->_fields['id']
	  		    ],
	  		  'order' =>
	  		    [
	  		  	  'data_final DESC'
	  		    ]
	  		]
    		)->first();
 	   	  if(!$valor_atual)
 	   	    {

 	   	    }
 	    }
 	  return $valor_atual;
   }
}