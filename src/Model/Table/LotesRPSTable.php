<?php
namespace App\Model\Table;

use Cake\ORM\Table;


class LotesRPSTable extends Table
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
         $this->setTable('lotes_rps');
         $this->setEntityClass('App\Model\Entity\LoteRPS');
         $this->hasMany('RPS', ['foreignKey' => 'lote_id', 'bindingKey' => 'id', 'dependent' => false, 'propertyName' => 'rps']);
         $this->belongsTo('Unidades', ['foreignKey' => 'unidade', 'bindingKey' => 'id', 'propertyName' => 'Unidade']);
        }
  }
?>