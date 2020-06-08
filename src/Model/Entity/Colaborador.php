<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Colaborador extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  public function formatDate($date)
    {
      if($date)
        {
          $date = implode('-', array_reverse(explode('/', $date)));
        }
      return $date;
    }
  protected function _setDataAdmissao($data)
    {
      return $this->formatDate($data);
    }
  protected function _setDataDemissao($data)
    {
    	return $this->formatDate($data);
    }
  protected function _setSalarioBase($salario)
    {
    	if($salario)
    	  {
    	  	return str_replace([',', '.', ' '], '', $salario);
    	  }
    	return $salario;
    }
  protected function _setValeTransporte($vale)
    {
    	if($vale)
    	  {
    	  	return str_replace([',', '.', ' '], '', $vale);
    	  }
    	return $vale;
    }
}
