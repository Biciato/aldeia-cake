<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class AgrupamentosTable extends Table
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
        ->notEmptyString('nome', "Insira um nome para esse agrupamento")
        ->notEmptyString('idade_inicial', "Insira a idade inicial para esse agrupamento")
        ->notEmptyString('nome', "Insira um nome para esse agrupamento");
        return $validator;
      }
  }
?>