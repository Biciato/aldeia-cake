<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TokensRecuperacaoTable extends Table
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
        $this->setTable('tokens_recuperacao');
        $this->hasOne('Login', ['foreignKey' => 'id', 'bindingKey' => 'usuario', 'propertyName' => 'User'])->setDependent(false);
       }
}