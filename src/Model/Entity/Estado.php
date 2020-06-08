<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Estado extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];
}