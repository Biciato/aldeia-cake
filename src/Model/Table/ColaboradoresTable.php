<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
class ColaboradoresTable extends Table
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
        $this->setEntityClass('App\Model\Entity\Colaborador');
        $this->belongsTo('Pessoas', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id']);
       }
    public function findComPessoa(Query $query, array $options)
      {
        return $query->contain(['Pessoas']);
      }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {

        $validator
        ->notEmptyString('funcao', 'Selecione a função do colaborador')
        ->notEmptyString('unidade', 'Selecione a unidade em que o colaborador trabalha')
        ->notEmptyString('assina_contrato', 'Selecione uma das opções')
        ->notEmptyString('contrato_trabalho_ativo', 'Selecione uma das opções')
        ->notEmptyString('visao_do_parente', 'Selecione uma das opções')
        ->notEmptyString('altera_vencimento', 'Selecione uma das opções')
        ->notEmptyString('data_admissao', 'Insira a data de admissão')
        ->allowEmptyString('horario_entrada',  'Insira um horário válido', function($context)
        {
          
              if(!$context['data']['horario_entrada'])
                {
                  return true;
                }
              return call_user_func([$this, 'validateTime'], $context['data']['horario_entrada'], $context);
           
        })
        ->allowEmptyString('horario_saida', 'Insira um horário válido', function($context)
        {
              if(!$context['data']['horario_saida'])
                {
                  return true;
                }
              return call_user_func([$this, 'validateTime'], $context['data']['horario_saida'], $context);
           
        })
        ->allowEmptyString('horario_intervalo',  'Insira um horário válido', function($context)
        {
          
              if(!$context['data']['horario_intervalo'])
                {
                  return true;
                }
              return call_user_func([$this, 'validateTime'], $context['data']['horario_intervalo'], $context);
           
        })
        ->requirePresence('enderecos', true, 'Insira ao menos um endereço');
        
       
        $validatorPessoa = new Validator();
        $validatorPessoa
        ->notEmptyString('nome', 'Insira o nome do colaborador')
        ->notEmptyString('sexo', 'Selecione o sexo do colaborador')
        ->notEmptyString('email', 'Insira um email para o colaborador')
        ->notEmptyString('rg', 'Insira um RG para o colaborador')
        ->notEmptyString('cpf', 'Insira um CPF para o colaborador')
        ->add('email', 'unique', 
          [
            'rule' => function($email, $context)
              {
                $conds = ['Pessoas.email' => $email];
                if(!$context['newRecord'])
                  {
                    $conds[] = 'Pessoas.id != ' . $context['data']['id'];
                  }
                return !(bool)$this->find('all', ['conditions' => $conds])->contain(['Pessoas'])->count();
              },
            'message' => 'Já existe uma pessoa cadastrada com esse email no sistema'
          ])
        ->allowEmptyString('data_nascimento',  'Insira a data de nascimento do colaborador',function($context)
        {
          
              if(!$context['data']['data_nascimento'])
                {
                  return false;
                }
              return true;
           
        })
        ->add('data_nascimento', 'custom', 
          [
            'rule' => function($date, $context)
              {
                return call_user_func([$this, 'validateDate'], $date, $context);
              },
            'message' => 'Insira uma data válida'
          ])
        ->allowEmptyString('avatar', 'Insira uma imagem do tipo PNG, JPG, JPEG ou GIF', 
        function($context)
        {
          if(empty($context['data']['avatar']))
            {
              return true;
            }
          $titulo = $context['data']['avatar']['name'];
          $pedacos = explode('.', $titulo);
          $ext = array_pop($pedacos);
          return in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
        })
        ->add('cpf', 'valid', 
          [
            'rule' => function($cpf, $context)
              {
                return call_user_func([$this, 'validateCPF'], $cpf, $context);
              },
            'message' => 'Insira um CPF válido'
            ])
        ->add('cpf', 'custom', 
          [
            'rule' => function($cpf, $context)
              {
                $conds = ['Pessoas.cpf' => $cpf];
                if(!$context['newRecord'])
                  {
                    $conds[] = 'Pessoas.id != ' . $context['data']['id'];
                  }
                return !(bool)$this->find('all', ['conditions' => $conds])->contain(['Pessoas'])->count();
              },
            'message' => 'Já existe uma pessoa com esse CPF cadastrada no sistema'
          ])
        ->add('rg', 'custom', 
          [
            'rule' => function($rg, $context)
              {
                $conds = ['Pessoas.rg' => $rg];
                if(!$context['newRecord'])
                  {
                    $conds[] = 'Pessoas.id != ' . $context['data']['id'];
                  }
                return !(bool)$this->find('all', ['conditions' => $conds])->contain(['Pessoas'])->count();
              },
            'message' => 'Já existe uma pessoa cadastrada com esse RG no sistema'
          ]);
        $validator->addNested('pessoa-colaborador', $validatorPessoa);

        $validatorEnderecos = new Validator();
        $validatorEnderecos
        ->notEmptyString('cep', 'Insira o CEP do endereço')
        ->notEmptyString('logradouro', 'Insira o logradouro do endereço')
        ->notEmptyString('bairro', 'Insira o bairro do endereço')
        ->notEmptyString('cidade', 'Insira a cidade do endereço')
        ->notEmptyString('estado', 'Insira o estado do endereço')
        ->notEmptyString('numero', 'Insira o número do endereço');
        $validator->addNestedMany('enderecos', $validatorEnderecos);

        $validatorLogin = new Validator();
        $validatorLogin
        ->allowEmptyString('senha',  'Insira uma senha e repita ela no campo indicado', [$this, 'validatePassword'])
        ->add('senha', 'custom', 
          [
            'rule' => function($senha, $context)
              {
                return ($senha == $context['data']['repetir_senha']);
              },
            'message' => 'As senhas não coincidem'
          ]);
        $validator->addNested('login', $validatorLogin);
        

        return $validator;
      }
    public function validateCPF($cpf, $context)
      {
        if(empty($cpf))
          {
            return false;
          }
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        if (strlen($cpf) != 11)
          {
            return false;
          }
        else if ($cpf == '00000000000' || 
          $cpf == '11111111111' || 
          $cpf == '22222222222' || 
          $cpf == '33333333333' || 
          $cpf == '44444444444' || 
          $cpf == '55555555555' || 
          $cpf == '66666666666' || 
          $cpf == '77777777777' || 
          $cpf == '88888888888' || 
          $cpf == '99999999999') 
          {
            return false;
          }
        else
         {   
          for ($t = 9; $t < 11; $t++) 
            {
            for ($d = 0, $c = 0; $c < $t; $c++) 
              {
                $d += $cpf{$c} * (($t + 1) - $c);
              }
              $d = ((10 * $d) % 11) % 10;
              if ($cpf{$c} != $d) 
                {
                  return false;
                }
            }
          return true;
        }
      }
    public function validateDate($date, $context)
      {
        $date_pattern = "99/99/9999";
        if(strlen($date) != strlen($date_pattern))
          {
            return false;
          }
        $exploded = explode("/", $date);
        return checkdate($exploded[1], $exploded[0], $exploded[2]);
      }
    public function validateTime($time, $context)
        {
          if(strpos($time, ":") === false)
            {
              return false;
            }
          $exploded = explode(":", $time);
          if(((int)$exploded[0] > 23)||((int)$exploded[1] > 59))
            {
              return false;
            }
          return true;
        }
      public function validatePassword($context)
        {
          $password = $context['data']['senha'];
          if(!$password)
            {
              if(!$context['newRecord'])
                {
                  return true;
                }
              else
                {
                  return false;
                }
            }
          return $password == $context['data']['repetir_senha'];
        }
    }
?>