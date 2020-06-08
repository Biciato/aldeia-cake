<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class OcorrenciasTable extends Table
  {
    protected $_acessible =
      [
        '*' => true
      ];
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
         $this->setEntityClass('App\Model\Entity\Ocorrencia');
         $this->hasOne('Pessoas', ['foreignKey' => 'id', 'bindingKey' => 'registrado_por', 'dependent' => false, 'propertyName' => 'usuario']);
         $this->hasMany('Comentarios', ['foreignKey' => 'comentario_de', 'bindingKey' => 'id', 'dependent' => false, 'propertyName' => 'comentarios', 'className' => 'Ocorrencias']);
        
       }
  }
?>