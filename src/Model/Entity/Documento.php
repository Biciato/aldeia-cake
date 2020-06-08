<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;


class Documento extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _getPrimeirosCapitulos()
    {
      $capitulosTable = TableRegistry::get('Capitulos');
      $capitulos = $capitulosTable->find('all', ['conditions' => ['documento' => $this->_fields['id'], 'pai IS NULL'], 'order' => 'ordenacao ASC, nome ASC'])->toArray();
      return $capitulos;
    }
}