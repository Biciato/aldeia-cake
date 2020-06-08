<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
class ProspectsTable extends Table
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
        $this->hasMany('Enderecos', ['foreignKey' => 'aluno_id', 'bindingKey' => 'id', 'dependent' => false]);
        $this->hasMany('Parentes', ['foreignKey' => 'aluno_id', 'bindingKey' => 'id', 'dependent' => false]);
        $this->hasMany('Interacoes', ['foreignKey' => 'aluno_id', 'bindingKey' => 'id', 'dependent' => false, 'order' => ['Interacoes.data DESC']]);
       }
    public function findCompleto(Query $query, array $options)
      {
        return $query->contain(
          [
            'Pessoas',
            'Enderecos',
            'Parentes' => ['Pessoas'],
            'Interacoes' => ['Responsaveis' => ['Pessoas']],
          ]);        
      }
    public function findComPessoa(Query $query, array $options)
      {
        return $query->contain(['Pessoa']);
      }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->notEmptyString('permanencia', 'Selecione uma permanência')
        ->notEmptyString('turno', 'Selecione um turno')
        ->notEmptyString('horario', 'Selecione um horário')
        ->notEmptyString('unidade', 'Selecione uma unidade')
        ->requirePresence('enderecos', true, 'Insira ao menos um endereço')
        ->requirePresence('parentes', true, 'Insira ao menos um parente')
        ->notEmptyString('necessidades_especiais', 'Selecione uma das opções')
        ->allowEmptyString('data_primeiro_atendimento', 'Insira uma data válida', function($context)
        {
          if(empty($context['data']['data_primeiro_atendimento']))
            {
              return true;
            }
          else
           {
              return call_user_func([$this, 'validateDate'], $context['data']['data_primeiro_atendimento'], $context);
           }
        });

        $validatorPessoa = new Validator();
        $validatorPessoa
        ->notEmptyString('nome', 'Insira o nome do aluno')
        ->notEmptyString('sexo', 'Selecione o sexo do aluno')
        ->allowEmptyString('data_nascimento', 'Insira a data de nascimento do aluno', function($context)
        {
          if((bool)$context['data']['ja_nascido'])
            {
              if(!$context['data']['data_nascimento'])
                {
                  return false;
                }
              return true;
            }
          return true;
        })
        ->add('data_nascimento', 'custom', 
          [
            'rule' => function($date, $context)
              {
                if((bool)$context['data']['ja_nascido'])
                  {
                    return call_user_func([$this, 'validateDate'], $date, $context);
                  }
                return true;
              },
            'message' => 'Insira uma data válida'
          ]);
        $validator->addNested('pessoa-prospect', $validatorPessoa);

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

        $validatorPessoaParente = new Validator();
        $validatorPessoaParente
        ->notEmptyString('nome', "Insira o nome desse parente")
        ->notEmptyString('cpf', "Insira o CPF desse parente")
        ->notEmptyString('ocupacao', "Insira a ocupação desse parente")
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

        $validatorInteracoes = new Validator();
        $validatorInteracoes
        ->notEmptyString('tipo', "Selecione o tipo da interação")
        ->notEmptyString('titulo', "Insira o título da interação")
        ->notEmptyString('responsavel', "Selecione o colaborador responsável por essa interação")
        ->notEmptyString('data', "Insira a data para a interação")
        ->notEmptyString('hora', "Insira a hora para a interação")
        ->add('hora', 'custom', 
          [
            'rule' => [$this, 'validateTime'],
            'message' => 'Insira um horário válido'
          ]);
        $validator->addNestedMany('interacoes', $validatorInteracoes);

        return $validator;
      }
    public function validationExterno(Validator $validator)
      {
        $validator
        ->notEmptyString('unidade', 'Selecione uma unidade');

        $validatorPessoa = new Validator();
        $validatorPessoa
        ->notEmptyString('nome', 'Insira o nome do aluno')
        ->allowEmptyString('data_nascimento')
        ->add('data_nascimento', 'custom', 
          [
            'rule' => function($date, $context)
              {
                if((bool)$date)
                  {
                    return call_user_func([$this, 'validateDate'], $date, $context);
                  }
                return true;
              },
            'message' => 'Insira uma data válida'
          ]);
        $validator->addNested('pessoa-prospect', $validatorPessoa);

        $validatorParentes = new Validator();
        $validatorParentes
        ->notEmptyString('parentesco', "Selecione o seu parentesco com o aluno");
        $validator->addNestedMany('parentes', $validatorParentes);

        $validatorPessoaParente = new Validator();
        $validatorPessoaParente
        ->notEmptyString('nome', "Insira o seu nome")
        ->notEmptyString('email', "Insira o seu email")
        ->add('email', 'validFormat', [
                'rule' => 'email',
                'message' => 'Insira um email válido'
            ])
        ->add('telefones', 'custom', 
          [
            'rule' => function($tels, $context)
              {
                return !empty($tels[0]);
              },
            'message' => 'Insira um telefone'
          ]);
        $validatorParentes->addNested('pessoa-parente', $validatorPessoaParente);

        $validatorInteracoes = new Validator();
        $validatorInteracoes
        ->notEmptyString('data', "Insira a data para a visita")
        ->notEmptyString('hora', "Insira a hora para a visita")
        ->add('data', 'custom', 
          [
            'rule' => [$this, 'validateExternalDate'],
            'message' => 'Insira uma data de segunda à sexta para daqui no mínimo 1 dia'
          ])
        ->add('hora', 'custom', 
          [
            'rule' => [$this, 'validateExternalTime'],
            'message' => 'Insira um horário entre 08 e 18 horas para a visita'
          ]);
        $validator->addNested('interacao', $validatorInteracoes);

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
     public function validateExternalTime($time, $context)
      {
        if(!call_user_func([$this, 'validateTime'], $time, $context))
          {
            return false;
          }
        $exploded = explode(":", $time);
        if(((int)$exploded[0] > 18)||((int)$exploded[0] < 8)||(((int)$exploded[0] == 18)&&((int)$exploded[1] > 0)))
          {
            return false;
          }
        return true;
      }
    public function validateExternalDate($date, $context)
      {
        if(!call_user_func([$this, 'validateDate'], $date, $context))
          {
            return false;
          }
        $dts = (object)
          [
            'now' => new \DateTime(date('Y-m-d') . " 00:00:00"),
            'date' => new \DateTime(implode('-', array_reverse(explode('/', $date)))  . " 00:00:00")
          ];
        $dts->now->modify('+1 days');
        return (($dts->date >= $dts->now)&&(!in_array($dts->date->format('w'), ['0', '6']))); 
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
    public function findAgrupamento(Query $query, array $options)
      {
        $agrupamento       = $options['agrupamento'];
        $unidade           = $options['unidade'];
        $unidadesTable     = TableRegistry::get('Unidades');
        $agrupamentosTable = TableRegistry::get('Agrupamentos');
        $agrupamento       = $agrupamentosTable->get($agrupamento);
        $unidade           = $unidadesTable->get($unidade);
        $unidadeCond       = 'unidade = ' . $unidade->id;
        if($unidade->extende)
          {
            $unidadeCond = '(' . $unidadeCond . ' OR unidade = ' . $unidade->extende . ')';
          }
        $dateTime0         = new \DateTime(); 
        $dateTime1         = new \DateTime();
        $dateTime0->modify("-" . $agrupamento->idade_inicial . " months");
        $dateTime1->modify("-" . $agrupamento->idade_final . " months");
        return $query->where(['(data_nascimento < \'' . $dateTime0->format('Y-m-d') . '\' AND data_nascimento > \'' . $dateTime1->format('Y-m-d') . '\')', $unidadeCond]);
      }
    public function findSemAgrupamento(Query $query, array $options)
      {
        $conds = [];
        $unidadesTable     = TableRegistry::get('Unidades');
        $agrupamentosTable = TableRegistry::get('Agrupamentos');
        $unidades          = $unidadesTable->find('all')->toArray();
        foreach($unidades as $unidade)
          {
            $agrupamentos = $agrupamentosTable->find('all', ['conditions' => ['id IN(' . implode(', ', $unidade->agrupamentos_array) . ')']])->order(['idade_inicial ASC'])->toArray();
            foreach($agrupamentos as $agrupamento)
              {
                $unidadeCond       = 'unidade = ' . $unidade->id;
                if($unidade->extende)
                  {
                    $unidadeCond = '(' . $unidadeCond . ' OR unidade = ' . $unidade->extende . ')';
                  }
                $dateTime0         = new \DateTime(); 
                $dateTime1         = new \DateTime();
                $dateTime0->modify("-" . $agrupamento->idade_inicial . " months");
                $dateTime1->modify("-" . $agrupamento->idade_final . " months");
                $conds[] = ['!((data_nascimento < \'' . $dateTime0->format('Y-m-d') . '\' AND data_nascimento > \'' . $dateTime1->format('Y-m-d') . '\')' . 'AND ' . $unidadeCond . ')']; 
              }
          }
        $options['agrupamento_conds'] = ['OR' => ['AND' => $conds, 'data_nascimento IS NULL']];
        return ((isset($options['interacoes']))&&($this->_associations->has('interacoes'))) ? call_user_func([$this, 'findStatusInteracoes'], $query, $options) : $query->where($options['agrupamento_conds']);
      }
     public function findComAgrupamento(Query $query, array $options)
      {
        $conds = [];
        $unidadesTable     = TableRegistry::get('Unidades');
        $agrupamentosTable = TableRegistry::get('Agrupamentos');
        $unidade           = $unidadesTable->get($options['unidade']);
        $agrupamentos = $agrupamentosTable->find('all', ['conditions' => ['id IN(' . implode(', ', $unidade->agrupamentos_array) . ')']])->order(['idade_inicial ASC'])->toArray();
        foreach($agrupamentos as $agrupamento)
          {
            $unidadeCond       = 'unidade = ' . $unidade->id;
            if($unidade->extende)
              {
                $unidadeCond = '(' . $unidadeCond . ' OR unidade = ' . $unidade->extende . ')';
              }
           $dateTime0         = new \DateTime(); 
           $dateTime1         = new \DateTime();
           $dateTime0->modify("-" . $agrupamento->idade_inicial . " months");
           $dateTime1->modify("-" . $agrupamento->idade_final . " months");
           $conds[] = ['((data_nascimento < \'' . $dateTime0->format('Y-m-d') . '\' AND data_nascimento > \'' . $dateTime1->format('Y-m-d') . '\')' . 'AND ' . $unidadeCond . ')']; 
          }
        $options['agrupamento_conds'] = ['OR' => ['OR' => $conds]];
        return ((isset($options['interacoes']))&&($this->_associations->has('interacoes'))) ? call_user_func([$this, 'findStatusInteracoes'], $query, $options) : $query->where($options['agrupamento_conds']);
      }
    public function findStatusInteracoes(Query $query, array $options)
      {
        $conds = null;
        $result = $query->where($conds);
        $interacoesAssociation = $this->_associations->get('interacoes');
        $status = $options['interacoes'];
        switch ($status) 
          {
            case '1':
              $conds = "(SELECT COUNT(InteracoesSub.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub WHERE InteracoesSub." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . ") = 0";
              break;
            case '2':
              $conds = "((SELECT COUNT(InteracoesSub0.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub0 WHERE InteracoesSub0." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . " AND InteracoesSub0.concluida = FALSE) = 0 AND (SELECT COUNT(InteracoesSub1.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub1 WHERE InteracoesSub1." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . ")) > 0";
              break;
            case '3':
              $now = (object)
                [
                  'date' => date('Y-m-d'),
                  'time' => date('H:i')
                ];
              $conds = "(SELECT COUNT(InteracoesSub0.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub0 WHERE InteracoesSub0." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . " AND InteracoesSub0.concluida = FALSE AND (InteracoesSub0.data < '" . $now->date . "' OR (InteracoesSub0.data = '" . $now->date . "' AND InteracoesSub0.hora < '" . $now->time . ":00'))) = 0 AND (SELECT COUNT(InteracoesSub1.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub1 WHERE InteracoesSub1." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . ") > 0 AND (SELECT COUNT(InteracoesSub2.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub2 WHERE InteracoesSub2." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . " AND InteracoesSub2.concluida = FALSE) > 0";
              break;
            case '4':
              $now = (object)
                [
                  'date' => date('Y-m-d'),
                  'time' => date('H:i')
                ];
              $conds = "(SELECT COUNT(InteracoesSub0.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub0 WHERE InteracoesSub0." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . " AND InteracoesSub0.concluida = FALSE AND (InteracoesSub0.data < '" . $now->date . "' OR (InteracoesSub0.data = '" . $now->date . "' AND InteracoesSub0.hora < '" . $now->time . ":00'))) > 0 AND (SELECT COUNT(InteracoesSub1.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub1 WHERE InteracoesSub1." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . ") > 0";
              break;
          }
        return $query->where([$options['agrupamento_conds'], $conds]);
      }
  }
  /*
                dt = new \DateTime();
                      $data = implode('-', array_reverse(explode('/', $this->_properties['data'])));
                      $hora = explode(' ', $this->_properties['hora'])[1];
                      $dt_interacao = new \DateTime($data . " " . $hora);
                      $status = null;
                      if($this->_properties['concluida'])
                        {
                          $status = 1;
                        }
                      elseif($dt_interacao < $dt)
                        {
                          $status = 2;
                        }
                      elseif($dt_interacao > $dt)
                        {
                          $status = 3;
                        }

                  CONDS PARA ATRASADAS = (DATA_INTERAÇÃO < DATA_DE_HOJE) OU ((DATA_INTERACAO = DATA_DE_HOJE)E(HORA_INTERACAO < HORA_DE_AGORA))
                  (SELECT COUNT(InteracoesSub0.id) FROM " . $interacoesAssociation->getTable() . " AS InteracoesSub0 WHERE InteracoesSub0." . $interacoesAssociation->getForeignKey() . " = " . $this->getRegistryAlias() . "." . $interacoesAssociation->getBindingKey() . " AND InteracoesSub0.concluida = FALSE AND (InteracoesSub0.data < '" . $now->date . "' OR (InteracoesSub0.data = '" . $now->date . "' AND InteracoesSub0.hora < '" . $now->time . "'))

              */
?>