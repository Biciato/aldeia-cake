<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

class BoletosTable extends Table
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
        $this->hasOne('Unidades', ['foreignKey' => 'id', 'bindingKey' => 'unidade_id'])->setDependent(false);
        $this->hasOne('Pessoas', ['foreignKey' => 'id', 'bindingKey' => 'pessoa_id'])->setDependent(false);
       }

    public function findNossoNumero(Query $query, array $options)
      {
        $ultimo =  $this->find('all', ['order' => ['numero_interno DESC']]);
        return $ultimo; 
      }
  }
?>