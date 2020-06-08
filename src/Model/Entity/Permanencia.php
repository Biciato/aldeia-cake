<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Permanencia extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  protected function _getTurnosArray()
    {
   	  $turnos = json_decode($this->_fields['turnos'], true);
   	  return ($turnos) ? $turnos : [];
    } 
  protected function _getHorariosEntities()
    {
      $arr = [];
      $horariosTable = TableRegistry::get('Horarios');
      $horarios      = $horariosTable->find('all', ['order' => 'ordenacao ASC'])->toArray();
      $tempo         = intval($this->_fields['nome']);
      foreach($horarios as $horario)
        {
          if(((strtotime($horario->horario_saida->format('Y-m-d H:i:s')) - strtotime($horario->horario_entrada->format('Y-m-d H:i:s')))/60/60) == $tempo)
            {
              $arr[$horario->id] = $horario;
            }
        }
      return $arr;
    }
}