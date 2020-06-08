<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class TiposInteracaoTable extends Table
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
         $this->setEntityClass('App\Model\Entity\TipoInteracao');
         $this->setTable('tipos_interacao');
       }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->add('nome', 'custom', 
          [
            'rule' => [$this, 'uniqueName'],
            'message' => 'Já existe um tipo de interação com esse nome'
          ])
        ->notEmptyString('nome', "Insira um nome para esse tipo de interação");
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