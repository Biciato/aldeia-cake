<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Nivel extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
}