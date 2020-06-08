<?php
namespace App\Model\Table;

use Cake\ORM\Table;


class RPSTable extends Table
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
         $this->setTable('rps');
         $this->setEntityClass('App\Model\Entity\RPS');
         $this->belongsTo('LotesRPS', ['foreignKey' => 'lote_id', 'bindingKey' => 'id', 'joinType' => 'LEFT', 'propertyName' => 'lote']);
         $this->hasOne('NotasFiscais', ['foreignKey' => 'rps_id', 'bindingKey' => 'id', 'joinType' => 'LEFT', 'propertyName' => 'nota_fiscal']);
         $this->hasOne('Alunos', ['foreignKey' => 'id', 'bindingKey' => 'aluno', 'joinType' => 'LEFT', 'propertyName' => 'Aluno']);
       }
  }
?>