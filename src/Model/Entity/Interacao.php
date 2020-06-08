<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Interacao extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _setData($data)
    {
      if($data)
        {
          $data = implode('-', array_reverse(explode('/', $data)));
        }
      return $data;
    }
  protected function _getDataFormatada()
  	{
      if(!$this->_fields['data'])
        {
          return false;
        }
      $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data']))));
    	return $dt->format('d/m/Y');
  	}
  protected function _getHoraFormatada()
    {
      $hora = explode(' ', $this->_fields['hora']);
    	$dt = new \DateTime($hora[1]);
    	return $dt->format('H:i');
    }
  protected function _getDataCompleta()
    {
      if(!$this->_fields['data'])
        {
          return false;
        }
      $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data']))) . " " . $this->_fields['hora']);
      return $dt->format('d/m/Y H:i');
    }
  protected function _getStatus()
    {
      $dt = new \DateTime();
      $data = implode('-', array_reverse(explode('/', $this->_fields['data'])));
      $hora = explode(' ', $this->_fields['hora'])[1];
      $dt_interacao = new \DateTime($data . " " . $hora);
      $status = null;
      if($this->_fields['concluida'])
        {
          $status = 1;
        }
      elseif($dt_interacao < $dt)
        {
          $status = 2;
        }
      elseif($dt_interacao >= $dt)
        {
          $status = 3;
        }
      return $status;
    }
  protected function _getInformacaoFormatada()
    {
      return ($this->_fields['informacao']) ? str_replace("|", "<br />", $this->_fields['informacao']) : null;
    }
}