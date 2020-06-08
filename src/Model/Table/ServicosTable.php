<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Query;

class ServicosTable extends Table
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
          $this->hasOne('ServicosAux', ['foreignKey' => 'id', 'bindingKey' => 'servico', 'dependent' => false, 'propertyName' => 'ServicoAux']);
          $this->hasOne('Horarios', ['foreignKey' => 'id', 'bindingKey' => 'horario', 'dependent' => false, 'propertyName' => 'HorarioEntity']);

       }
    public function findLote(Query $query, array $options)
      {
        $lote = $options['lote_config'];
        $conds = [];
        foreach($lote as $campo => $valor)
          {
            if($valor !== "TODAS_OPCOES")
              {
                $conds[$campo] = $valor;
              }
          }
        return $query->where($conds);
      }
    public function findCompleto(Query $query, array $options)
      {
        return $query->contain(['ServicosAux']);
      }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
      {
        $validator
        ->notEmptyString('unidade', 'Selecione a unidade')
        ->notEmptyString('servico', 'Selecione o serviço')
        ->notEmptyString('curso', 'Selecione o curso')
        ->notEmptyString('agrupamento', 'Selecione o agrupamento')
        ->notEmptyString('nivel', 'Selecione o nível')
        ->notEmptyString('turno', 'Selecione o turno')
        ->notEmptyString('permanencia', 'Selecione a permanência')
        ->notEmptyString('horario', 'Selecione o horário');
        
        $validatorValor = new Validator();
        $validatorValor
        ->notEmptyString('valor', 'Insira o valor do serviço')
        ->notEmptyString('data_inicio', 'Insira a data de início para o valor desse serviço')
        ->add('data_inicio', 'valid', 
          [
            'rule' => function($date, $context)
              {
                return call_user_func([$this, 'validateDate'], $date, $context);
              },
            'message' => 'Insira uma data válida'
          ])
        ->notEmptyString('data_final', 'Insira a data final para o valor desse serviço')
        ->add('data_final', 'valid', 
          [
            'rule' => function($date, $context)
              {
                return call_user_func([$this, 'validateDate'], $date, $context);
              },
            'message' => 'Insira uma data válida'
          ])
        ->add('data_inicio', 'valid_daterange', 
          [
            'rule' => function($date, $context)
              {
                return call_user_func([$this, 'validateDateRange'], $date, $context);
              },
            'message' => 'A data inicial deve ser menor que a data final'
          ]);
        $validator->addNested('valor', $validatorValor);
        return $validator;
      }
    public function validateDateRange($data_inicial, $context)
      {
        $data_final = $context['data']['data_final'];
        if(($data_inicial)&&($data_final))
          {
            $dt0 = new \DateTime(implode('-', array_reverse(explode('/', $data_inicial))));
            $dt1 = new \DateTime(implode('-', array_reverse(explode('/', $data_final))));
            return ($dt0 < $dt1);
          }
        return false;
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