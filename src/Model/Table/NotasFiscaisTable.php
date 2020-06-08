<?php
namespace App\Model\Table;

use Cake\ORM\Table;


class NotasFiscaisTable extends Table
  {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
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
        $this->setTable('notas_fiscais');
        $this->setEntityClass('App\Model\Entity\NotaFiscal');
        $this->belongsTo('RPS', [
            'foreignKey' => 'rps_id',
            'joinType' => 'INNER'
        ]);
      }
  
  }
