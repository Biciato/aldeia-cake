<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

class DetalhesRemessaTable extends Table
  {
  	public function initialize(array $config): void
     {
        $this->addBehavior('Timestamp', 
          [
            'events' => 
              [
                'Model.beforeSave' => 
                  [
                    'data_criacao' => 'new',
                    'data_modificacao'   => 'always',
                  ],                   
               ]
           ]);
           $this->setEntityClass('App\Model\Entity\DetalheRemessa');
       }
    
  }
?>