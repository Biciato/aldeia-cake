<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class InteracoesTable extends Table
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
         $this->setEntityClass('App\Model\Entity\Interacao');
         $this->hasOne('TiposInteracao', ['foreignKey' => 'tipo', 'bindingKey' => 'id', 'dependent' => false]);
         $this->belongsTo('Prospects', ['foreignKey' => 'aluno_id', 'bindingKey' => 'id', 'dependent' => false]);
         $this->hasOne('Responsaveis', ['foreignKey' => 'id', 'bindingKey' => 'responsavel', 'dependent' => false, 'className' => 'Colaboradores', 'propertyName' => 'responsavel']);
       }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->notEmptyString('tipo', "Selecione o tipo da interação")
        ->notEmptyString('titulo', "Insira o título da interação")
        ->notEmptyString('responsavel', "Selecione o colaborador responsável por essa interação")
        ->notEmptyString('data', "Insira a data para a interação")
        ->notEmptyString('hora', "Insira a hora para a interação")
        ->add('hora', 'custom', 
          [
            'rule' => [$this, 'validateTime'],
            'message' => 'Insira um horário válido'
          ]);
        return $validator;
      }
    public function validateTime($time, $context)
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