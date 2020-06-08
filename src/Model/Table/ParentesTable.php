<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Validation\Validator;


class ParentesTable extends Table
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
          $this->belongsTo('Pessoas', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id']);
       }
    public function findComPessoa(Query $query, array $options)
      {
        return $query->contain(['Pessoas']);
      }
  }
?>