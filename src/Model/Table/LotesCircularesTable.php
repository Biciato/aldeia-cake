<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class LotesCircularesTable extends Table
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
        $this->setTable('lotes_circulares');
        $this->setEntityClass('App\Model\Entity\LoteCirculares');
        $this->hasMany('Circulares', ['foreignKey' => 'lote', 'bindingKey' => 'id', 'dependent' => false, 'propertyName' => 'circulares']);
      }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->notEmptyString('titulo', "Insira o título da circular")
        ->requirePresence('tipo_circular', true, "Selecione o tipo da circular para ser enviada")
        ->notEmptyString('unidades', "Selecione ao menos uma unidade para enviar a circular")
        ->requirePresence('alunos', true, "Selecione ao menos um aluno para enviar a circular");
        return $validator;
      }
  }
?>