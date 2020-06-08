<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Parente extends Entity {

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
  protected function _getAtribuicoesArray()
    {
      $atribuicoes_array = json_decode($this->_fields['atribuicoes'], true);
      return ($atribuicoes_array) ? $atribuicoes_array : [];
    }
}