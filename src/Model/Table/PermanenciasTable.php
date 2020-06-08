<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class PermanenciasTable extends Table
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
         ->add('nome', 'custom', 
          [
            'rule' => [$this, 'uniqueName'],
            'message' => 'Já existe uma permanência cadastrada com esse nome'
          ])
        ->notEmptyString('nome', "Insira um nome para essa permanência");
        return $validator;
      }
    public function uniqueName($name, $context)
      {
        $conds = ['nome' => $name];
        if(!$context['newRecord'])
          {
            $conds['id !='] = $context['data']['id'];
          }
        return !(bool)$this->find('all', ['conditions' => $conds])->count();
      }
  }
?>