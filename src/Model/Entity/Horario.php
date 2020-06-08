<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Horario extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];

  protected function _getHorarioEntradaFormatado()
    {
    	$hora = $this->_fields['horario_entrada'];
      return $hora->format('H:i');
    }

  protected function _getHorarioSaidaFormatado()
    {
    	$hora = $this->_fields['horario_saida'];
      return $hora->format('H:i');
    }
  protected function _getNome()
    {
      return $this->_getHorarioEntradaFormatado() . " - " . $this->_getHorarioSaidaFormatado(); 
    }
}