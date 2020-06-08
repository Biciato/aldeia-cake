<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class LoteCirculares extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
  private function propriedadeArray($propriedade)
    {
      $array = null;
      if($this->_fields[$propriedade])
        {
          $array = json_decode($this->_fields[$propriedade], true);
        }
      return ($array) ? $array : [];
    }
  protected function _getTurmasArray()
    {
      return $this->propriedadeArray('turmas');
    }
  protected function _getAlunosArray()
    {
      return $this->propriedadeArray('alunos');
    }

  protected function _getTurmasEntities()
    {
      $turmasTable = TableRegistry::get('Turmas');
      $turmas = $this->_getTurmasArray();
      return $turmasTable->find("all", 
        [
          'conditions' =>
            [
              'Turmas.id IN(' . implode(', ', $turmas) . ')'
            ]
        ])->contain(['ServicosAux', 'Unidades'])->toArray();
    }
  protected function _getAlunosEntities()
    {
      $alunosTable = TableRegistry::get('Alunos');
      $alunos = $this->_getAlunosArray();
      return $alunosTable->find("all",
        [
          'conditions' => 
            [
              'Alunos.id IN(' . implode(', ', $alunos) . ')'
            ],
          'order' =>
            [
              'Pessoas.nome'
            ]
        ])->contain(['Pessoas', 'Parentes' => ['Pessoas']])->toArray();      
    }
}
?>