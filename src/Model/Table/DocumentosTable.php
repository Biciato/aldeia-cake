<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;


class DocumentosTable extends Table
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
        $this->hasMany('Capitulos', ['foreignKey' => 'id', 'bindingKey' => 'documento', 'propertyName' => 'Capitulos',  'dependent' => false]);
       }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->notEmptyString('nome', 'Insira o título do documento')
        ->notEmptyString('descricao', 'Insira a descrição do documento');
        return $validator;
      }
  }
?>