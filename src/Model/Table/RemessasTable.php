<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class RemessasTable extends Table
  {
  	public function initialize(array $config): void
     {
        $this->addBehavior('Timestamp', 
          [
            'events' => 
              [
                'Model.beforeSave' => 
                  [
                    'data_criacao' => 'new',
                    'data_modificacao'   => 'always',
                  ],                   
               ]
           ]);
          $this->hasOne('Unidades', ['foreignKey' => 'id', 'bindingKey' => 'unidade_id']);
          $this->hasMany('Boletos', ['foreignKey' => 'boleto_id', 'bindingKey' => 'id', 'joinType' => 'LEFT']);
       }
  }
?>