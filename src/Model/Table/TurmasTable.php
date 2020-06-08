<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class TurmasTable extends Table
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
           $this->hasOne('ServicosAux', ['bindingKey' => 'servico', 'foreignKey' => 'id', 'propertyName' => 'Servico']);
           $this->belongsTo('Unidades', ['bindingKey' => 'id', 'foreignKey' => 'unidade', 'propertyName' => 'Unidade']);
       }
       public function validationDefault(Validator $validator): \Cake\Validation\Validator
       {
         $validator
         ->notEmptyString('nome', 'Insira um nome ou número para essa turma')
         ->notEmptyString('unidade', 'Selecione a unidade dessa turma')
         ->notEmptyString('servico', 'Selecione o serviço dessa turma')
         ->notEmptyString('quantidade_vagas', 'Insira a quantidade de vagas dessa turma')
         ->notEmptyString('horario_inicial', 'Insira o horário de início')
         ->add('horario_inicial', 'custom', 
          [
            'rule' => function($horario, $context)
              {
                if($horario)
                  {
                    $pedacos = explode(':', $horario);
                    if(
                        (
                          ((int)$pedacos[0] > 23)||
                          ((int)$pedacos[1] > 59)
                        )
                      )
                      {
                        return false;
                      }
                  }
                else
                  {
                    return false;
                  }
                return true;
              },
            'message' => 'Insira um horário válido'
          ])
         ->notEmptyString('horario_final', 'Insira o horário final')
         ->add('horario_final', 'custom', 
          [
            'rule' => function($horario, $context)
              {
                if($horario)
                  {
                    $pedacos = explode(':', $horario);
                    if(
                        (
                          ((int)$pedacos[0] > 23)||
                          ((int)$pedacos[1] > 59)
                        )
                      )
                      {
                        return false;
                      }
                  }
                else
                  {
                    return false;
                  }
                return true;
              },
            'message' => 'Insira um horário válido'
          ])
         ->notEmptyString('dias_semana', 'Selecione os dias da semana')
         ->notEmptyString('colaboradores', 'Insira ao menos um colaborador')
         ->add('colaboradores', 'custom', 
          [
            'rule' => function($colaboradores, $context)
              {
                if(is_array($colaboradores))
                  {
                    foreach($colaboradores as $colaborador)
                      {
                        if(!$colaborador)
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
            'message' => 'Insira um colaborador'
          ]);

         return $validator;
       }
  }
?>