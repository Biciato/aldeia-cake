<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\ORM\Rule\IsUnique;
use Cake\ORM\TableRegistry;

class HorariosTable extends Table
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
        ->notEmptyString('horario_entrada', "Insira um horário de entrada")
         ->add('horario_entrada', 'custom', 
          [
            'rule' => [$this, 'validTime'],
            'message' => 'Insira um horário válido'
          ])
        ->notEmptyString('horario_saida', "Insira um horário de saída")
        ->add('horario_saida', 'custom', 
          [
            'rule' => [$this, 'validTime'],
            'message' => 'Insira um horário válido'
          ]);
        return $validator;
      }
    public function validTime($time, $context)
      {
        if(strpos($time, ":") === false)
          {
            return false;
          }
        $exploded = explode(":", $time);
        if(((int)$exploded[0] > 23)||((int)$exploded[1] > 59))
          {
            return false;
          }
        return true;
      }
  }
?>