<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class NotificacoesTable extends Table
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
         $this->setEntityClass('App\Model\Entity\Notificacao');
         $this->hasOne('TiposNotificacao', ['foreignKey' => 'tipo', 'bindingKey' => 'id', 'dependent' => false]);
       }
  }
?>