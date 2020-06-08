<?php
namespace App\Model\Table;

use Cake\ORM\Table;


class RegistrosNotasFiscaisTable extends Table
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
        $this->setTable('registros_notas_fiscais');
        $this->setEntityClass('App\Model\Entity\RegistroNotasFiscais');
        $this->belongsTo('Unidades', [
            'foreignKey' => 'unidade',
            'bindingKey' => 'id',
            'joinType' => 'INNER',
            'propertyName' => 'Unidade'
        ]);
      }
  
  }
