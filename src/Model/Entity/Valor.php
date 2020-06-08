<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Valor extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _setDataInicio($data)
    {
      if($data)
        {
          $data = implode('-', array_reverse(explode('/', $data)));
        }
      return $data;
    }
  protected function _setDataFinal($data)
    {
      if($data)
        {
          $data = implode('-', array_reverse(explode('/', $data)));
        }
      return $data;
    }
  protected function _getValorFormatado()
    {
      if($this->_fields['valor'])
        {
          return number_format(((int)$this->_fields['valor']/100), 2, ",", ".");
        }
      return '0,00';
    }
}