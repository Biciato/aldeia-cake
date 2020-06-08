<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\ORM\Query;


class LoginTable extends Table
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
          $this->belongsTo('Pessoas', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id']);
       }
  public function validationChangePassword(Validator $validator)
    {
      $validator
      ->notEmptyString('senha_atual', 'Digite a sua senha atual')
      ->add('senha_atual', 'custom',
        [
          'rule' => [$this, 'checkPassword'],
          'message' => 'A senha atual digitada está incorreta'
        ])
      ->notEmptyString('senha', 'Digite a senha nova')
      ->add('senha', 'custom',
        [
          'rule' => [$this, 'comparePasswords'],
          'message' => 'As duas senhas novas não coincidem'
        ]);
      return $validator;
    }
  public function checkUser($bd, $input)
    {
      return (new DefaultPasswordHasher)->check($input, $bd);
    }
  public function comparePasswords($value, $context)
    {
      if($value)
        {
          return($value == $context['data']['repetir_senha']);
        }
    }
  public function findAuthenticate(Query $query, array $options)
    {
        $options = count($options) > 1 ? $options : $_REQUEST;
      return $query->where(['Pessoas.email' => $options['username']], [], true)->contain(['Pessoas']);
    }
  public function checkPassword($value, $context)
    {
      $user = $this->get($context['data']['id']);
      return $this->checkUser($user['senha'], $value);
    }

}
