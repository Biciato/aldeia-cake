<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
class AlunosTable extends Table
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
        $this->belongsTo('Pessoas', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'dependent' => false]);
        $this->hasMany('Enderecos', ['foreignKey' => 'pessoa_id', 'bindingKey' => 'id', 'dependent' => false]);
        $this->hasMany('Parentes', ['foreignKey' => 'aluno_id', 'bindingKey' => 'id', 'dependent' => false, 'conditions' => ['tipo' => 1]]);
        $this->hasOne('Cursos', ['foreignKey' => 'id', 'bindingKey' => 'curso', 'dependent' => false, 'propertyName' => 'Curso']);
        $this->hasOne('Turnos', ['foreignKey' => 'id', 'bindingKey' => 'turno', 'dependent' => false, 'propertyName' => 'Turno']);
        $this->hasOne('Permanencias', ['foreignKey' => 'id', 'bindingKey' => 'permanencia', 'dependent' => false, 'propertyName' => 'Permanencia']);
        $this->hasOne('Agrupamentos', ['foreignKey' => 'id', 'bindingKey' => 'agrupamento', 'dependent' => false, 'propertyName' => 'Agrupamento']);
        $this->hasOne('Horarios', ['foreignKey' => 'id', 'bindingKey' => 'horario', 'dependent' => false, 'propertyName' => 'Horario']);
        $this->hasOne('Niveis', ['foreignKey' => 'id', 'bindingKey' => 'nivel', 'dependent' => false, 'propertyName' => 'Nivel']);
       }
    public function findCompleto(Query $query, array $options)
      {
        return $query->contain(
          [
            'Pessoas',
            'Enderecos',
            'Parentes' => ['Pessoas']
          ]);        
      }
    public function findComPessoa(Query $query, array $options)
      {
        return $query->contain(['Pessoa']);
      }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->notEmptyString('data_inicio', 'Insira uma data de início')
        ->notEmptyString('data_matricula', 'Insira uma data de matrícula')
        ->notEmptyString('ano_letivo', 'Selecione o ano letivo para a matrícula do aluno')
        ->notEmptyString('unidade', 'Selecione a unidade do aluno')
        ->notEmptyString('matricula', 'Selecione o número de matrícula do aluno')
        ->notEmptyString('curso', 'Selecione a curso do aluno')
        ->notEmptyString('agrupamento', 'Selecione o agrupamento do aluno')
        ->notEmptyString('nivel', 'Selecione o nivel do aluno')
        ->notEmptyString('turno', 'Selecione o turno do aluno')
        ->notEmptyString('permanencia', 'Selecione a permanencia do aluno')
        ->notEmptyString('horario', 'Selecione o horario do aluno')
        ->requirePresence('turmas', true, 'Selecione a turma do aluno')
        ->add('turmas', 'custom', 
          [
            'rule' => function($turmas, $context)
              {
                if(is_array($turmas))
                  {
                    foreach($turmas as $turma)
                      {
                        if(!$turma)
                          {
                            return false;
                          }
                      }
                  }
                else
                  {
                    return false;
                  }
                return true;
              },
            'message' => 'Selecione a(s) turma(s) do aluno'
          ])
        ->notEmptyString('dia_vencimento', "Insira o dia de vencimento dos boletos")
        ->requirePresence('responsavel_legal', true, 'Selecione ao menos um responsável legal')
        ->requirePresence('enderecos', true, 'Insira ao menos um endereço')
        ->requirePresence('parentes', true, 'Insira ao menos um parente');
        
        $validatorPessoa = new Validator();
        $validatorPessoa
        ->notEmptyString('nome', 'Insira o nome do aluno')
        ->notEmptyString('sexo', 'Selecione o sexo do aluno')
        ->notEmptyString('data_nascimento', 'Insira a data de nascimento do aluno')
        ->allowEmptyString('cpf',  'Insira um CPF válido',function($context)
              {
                if(!empty($context['data']['cpf']))
                  {
                    return call_user_func([$this, 'validateCPF'], $context['data']['cpf'], $context);
                  }
                return true;                  
              }
          )
        ->add('data_nascimento', 'custom', 
          [
            'rule' => function($date, $context)
              {
                return call_user_func([$this, 'validateDate'], $date, $context);
              },
            'message' => 'Insira uma data válida'
          ]);
        $validator->addNested('pessoa-aluno', $validatorPessoa);

        $validatorEnderecos = new Validator();
        $validatorEnderecos
        ->notEmptyString('cep', 'Insira o CEP do endereço')
        ->notEmptyString('logradouro', 'Insira o logradouro do endereço')
        ->notEmptyString('bairro', 'Insira o bairro do endereço')
        ->notEmptyString('cidade', 'Insira a cidade do endereço')
        ->notEmptyString('estado', 'Insira o estado do endereço')
        ->notEmptyString('numero', 'Insira o número do endereço');
        $validator->addNestedMany('enderecos', $validatorEnderecos);

        $validatorParentes = new Validator();
        $validatorParentes
        ->notEmptyString('parentesco', "Selecione o parentesco desse parente")
        ->notEmptyString('notificacoes', "Selecione uma opção (se o parente recebe notificações ou não)");

        $validatorEnderecosParentes = new Validator();
        $validatorEnderecosParentes
        ->allowEmptyString('cep',  'Insira o CEP do endereço',function($context)
              {
                if($context['data']['mesmo_endereco'] == 0)
                  {
                    return !empty($context['data']['cep']);
                  }
                return true;                  
              }
          )
        ->allowEmptyString('logradouro',  
           'Insira o logradouro do endereço',
           function($context)
              {
                if($context['data']['mesmo_endereco'] == 0)
                  {
                    return !empty($context['data']['logradouro']);
                  }
                return true;                  
              }
          )
        ->allowEmptyString('bairro',  'Insira o CEP do endereço',function($context)
              {
                if($context['data']['mesmo_endereco'] == 0)
                  {
                    return !empty($context['data']['bairro']);
                  }
                return true;                  
              }
          )
        ->allowEmptyString('cidade',  'Insira a cidade do endereço', function($context)
              {
                if($context['data']['mesmo_endereco'] == 0)
                  {
                    return !empty($context['data']['cidade']);
                  }
                return true;                  
              }
          )
         ->allowEmptyString('estado',  'Insira o estado do endereço',function($context)
              {
                if($context['data']['mesmo_endereco'] == 0)
                  {
                    return !empty($context['data']['estado']);
                  }
                return true;                  
              }
           
          )
         ->allowEmptyString('numero',  'Insira o número do endereço',function($context)
              {
                if($context['data']['mesmo_endereco'] == 0)
                  {
                    return !empty($context['data']['numero']);
                  }
                return true;                  
              }
          );
        $validatorParentes->addNested('endereco', $validatorEnderecosParentes);


        $validatorPessoaParente = new Validator();
        $validatorPessoaParente
        ->notEmptyString('nome', "Insira o nome desse parente")
        ->notEmptyString('cpf', "Insira o CPF desse parente")
        ->notEmptyString('email', "Insira o email desse parente")
        ->add('email', 'validFormat', [
                'rule' => 'email',
                'message' => 'Insira um email válido'
            ])
        ->add('cpf', 'custom', 
          [
            'rule' => [$this, 'validateCPF'],
            'message' => 'Insira um CPF válido'
          ])
        ->notEmptyString('telefones', "Insira ao menos um telefone para esse parente");
        $validatorParentes->addNested('pessoa-parente', $validatorPessoaParente);
        $validator->addNestedMany('parentes', $validatorParentes);

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
    public function validateBirthDate($birth_date, $context)
      {
        if((bool)$context['data']['ja_nascido'])
          {
            if(!$birth_date)
              {
                return false;
              }
            return true;
          }
        return true;
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
  }
?>