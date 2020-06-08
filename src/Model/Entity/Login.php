<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Log\Log;
use Authentication\IdentityInterface;


/**
 * Login Entity.
 */
class Login extends Entity implements IdentityInterface {

  protected $_accessible =
    [
        '*' => true,
    ];

  protected $_virtual =
    [
      'modulos_acesso_array',
      'unidades_acesso_array'
    ];

    /**
     * Authentication\IdentityInterface method
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * Authentication\IdentityInterface method
     */
    public function getOriginalData()
    {
        return $this;
    }

  protected function _setSenha($value)
    {
      return $this->doHash($value);
    }
  protected function doHash($s)
    {
      return (new DefaultPasswordHasher)->hash($s);
    }
  protected function _getModulosAcessoArray()
    {
      $array = json_decode($this->_fields['modulos_acesso'], true);
      return ($array) ? $array : [];
    }
  protected function _getUnidadesAcessoArray()
    {
      $array = json_decode($this->_fields['unidades_acesso'], true);
      return ($array) ? $array : [];
    }
  protected function _getPrimeiroModulo()
    {
      $array = json_decode($this->_fields['modulos_acesso'], true);
      return ($array) ? array_shift($array) : 'dashboard/index';
    }
  protected function _getLandingPage()
    {
      if(!$this->_fields['landing_page'])
        {
          $array = json_decode($this->_fields['modulos_acesso'], true);
          return ($array) ? array_shift($array) : 'dashboard/index';
        }
      return $this->_fields['landing_page'];
    }
}
