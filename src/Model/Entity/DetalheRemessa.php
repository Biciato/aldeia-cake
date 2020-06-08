<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class DetalheRemessa extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];
  public function proximoDigito()
    {
        $properties = $this->_fields;
        $remessasTable = TableRegistry::get('Remessas');
        $ultima = $remessasTable->find('all', 
          [
              'conditions' =>
                [
                    'remessa_id' => $properties['remessa_id']
                ],
               'order' => 
                 [
                     'numero_sequencial DESC'
                 ]
          ])->first();
        if($ultima) 
          {
             $numero = 1;
          }
        else
          {
              $numero = ((int)$ultima->numero_sequencial + 1);
          }
        return $numero;
    }
}