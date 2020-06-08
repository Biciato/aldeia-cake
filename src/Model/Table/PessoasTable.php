<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class PessoasTable extends Table
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
          $this->hasOne('Login', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'joinType' => 'LEFT']);
          $this->hasMany('Enderecos', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'joinType' => 'LEFT']);
          $this->hasMany('Boletos', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'joinType' => 'LEFT']);
          $this->hasMany('Cobrancas', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'joinType' => 'LEFT', 'order' => ['id DESC']]);
          $this->hasMany('BoletosVencidos', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'joinType' => 'INNER', 'conditions' => ['BoletosVencidos.data_vencimento <= "' . date('Y-m-d') . '"', 'BoletosVencidos.data_liquidacao IS NULL'], 'propertyName' => 'boletos_vencidos', 'className' => 'Boletos', 'order' => ['BoletosVencidos.data_vencimento ASC']]);
          $this->hasOne('Alunos', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'joinType' => 'LEFT']);
          $this->hasOne('Colaboradores', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'joinType' => 'LEFT']);
       }
  }
?>