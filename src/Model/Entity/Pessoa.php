<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Pessoa extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];
  protected $_virtual =
    [
      'primeiro_nome'
    ];
  protected function _getDataCriacaoFormatada()
    {
    	$DateTime = new \DateTime($this->_fields['data_criacao']);
    	return $DateTime->format('d/m/Y H:i');
    }
  protected function _setDataNascimento($data)
    {
      return $this->formatDate($data);
    }
  protected function _getDataNascimentoFormatada()
    {
        if(!$this->_fields['data_nascimento'])
          {
            return false;
          }
        $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data_nascimento']))));
        return $dt->format('d/m/Y');
    }
  protected function _getTelefonesArray()
    {
      if(!$this->_fields['telefones'])
        {
          return [];
        }
      $decoded = json_decode($this->_fields['telefones'], true);
      return ($decoded) ? $decoded : [];
    }
  protected function _getPrimeiroNome()
    {
      $explode = explode(" ", $this->_fields['nome']);
      if(is_array($explode))
        {
          return $explode[0];
        }
      return $this->_fields['nome'];
    }
  protected function _setDataExpedicaoRg($data)
    {
      return $this->formatDate($data);
    }
  protected function _setDataExpedicaoCarteiraTrabalho($data)
    {
      return $this->formatDate($data);
    }
  public function formatDate($date)
    {
      if($date)
        {
          $date = implode('-', array_reverse(explode('/', $date)));
        }
      return $date;
    }
}
