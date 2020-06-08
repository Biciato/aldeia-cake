<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Unidade extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
      'nome_completo'
    ];

  protected function _getAgrupamentosArray()
    {
      return json_decode($this->_fields['agrupamentos'], true);
    }
  protected function _getNomeCompleto()
    {
      return $this->_fields['nome'] . ", " . $this->_fields['descricao'];
    }
}