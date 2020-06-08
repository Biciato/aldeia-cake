<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class CursosTable extends Table
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
       }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->notEmptyString('nome', "Insira um nome para esse curso");
        return $validator;
      }
  }
?>