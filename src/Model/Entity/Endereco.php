<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Endereco extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];

  protected function _getDataCriacaoFormatada()
    {
    	$DateTime = new \DateTime($this->_fields['data_criacao']);
    	return $DateTime->format('d/m/Y H:i');
    }
}