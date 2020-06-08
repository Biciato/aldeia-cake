<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class EnderecosTable extends Table
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
  }
?>