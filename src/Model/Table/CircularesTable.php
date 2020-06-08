<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class CircularesTable extends Table
  {
  	public function initialize(array $config): void
       {
           $this->addBehavior('Timestamp', [
               'events' => [
                   'Model.beforeSave' => [
                       'data_criacao' => 'new',
                       'data_modificacao'   => 'always',
                   ],                   
               ]
           ]);
         $this->setEntityClass('App\Model\Entity\Circular');
         $this->belongsTo('LotesCirculares', ['foreignKey' => 'lote', 'bindingKey' => 'id', 'dependent' => false, 'propertyName' => 'Lote']);
         $this->hasOne('Pessoas', ['foreignKey' => 'id', 'bindingKey' => 'pessoa', 'dependent' => false, 'propertyName' => 'destinatario']);
         $this->hasOne('Alunos', ['foreignKey' => 'id', 'bindingKey' => 'aluno', 'dependent' => false, 'propertyName' => 'Aluno']);
        }
  }
?>