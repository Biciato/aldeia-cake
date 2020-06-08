<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class CapitulosTable extends Table
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
        $this->hasMany('Filhos', ['foreignKey' => 'id', 'bindingKey' => 'pai', 'propertyName' => 'Filhos', 'className' => 'Capitulos', 'dependent' => false]);
        $this->belongsTo('Documentos', ['foreignKey' => 'documento', 'bindingKey' => 'id', 'propertyName' => 'Documento']);
       }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->notEmptyString('nome', 'Insira o título do capítulo')
        ->notEmptyString('conteudo', 'Insira o conteúdo do capítulo');
        return $validator;
      }
  }
?>