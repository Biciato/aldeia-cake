<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Prospect extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];

  protected $_virtual =
    [
    ];

  protected function _getDataCriacaoFormatada()
    {
    	 if(!$this->_fields['data_criacao'])
        {
          return false;
        }
      $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data_criacao']))));
      return $dt->format('d/m/Y H:i');
    }
  protected function _getDataPrimeiroAtendimentoFormatada()
    {
       if(!$this->_fields['data_primeiro_atendimento'])
        {
          return false;
        }
      $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data_primeiro_atendimento']))));
      return $dt->format('d/m/Y');
    }
  protected function _getDataPrimeiroAtendimentoYmd()
    {
       if(!$this->_fields['data_primeiro_atendimento'])
        {
          return false;
        }
      $dt = new \DateTime(implode('-', array_reverse(explode('/', $this->_fields['data_primeiro_atendimento']))));
      return $dt->format('Y-m-d');
    }
  protected function _getAcompanhamentosSistematicosArray()
    {
      $decoded = json_decode($this->_fields['acompanhamentos_sistematicos'], true);
      return ($decoded) ? $decoded : [];
    }
  protected function _setDataNascimento($data)
    {
      if($data)
        {
          $data = implode('-', array_reverse(explode('/', $data)));
        }
      return $data;
    }
  protected function _setDataPrimeiroAtendimento($data)
    {
      if($data)
        {
          $data = implode('-', array_reverse(explode('/', $data)));
        }
      return $data;
    }
  public function _getAgrupamento()
    {
      $pessoasTable = TableRegistry::get('Pessoas');
      $pessoa       = $pessoasTable->get($this->_fields['pessoa_id']);
      if($pessoa->data_nascimento == null)
        {
          return false;
        }
      $unidadesTable     = TableRegistry::get('Unidades');
      $agrupamentosTable = TableRegistry::get('Agrupamentos');
      $unidade           = $unidadesTable->get($this->_fields['unidade']);
      $extensao          = ($unidade->extende) ? $unidadesTable->get($unidade->extende) : false;
      $agrupamentos      = $unidade->agrupamentos_array;
      if($extensao)
        {
          $agrupamentos = array_merge($agrupamentos, $extensao->agrupamentos_array);
        }
      $agrupamentos = $agrupamentosTable->find('all', 
        [
          'conditions' =>
            [
              'id IN(' . implode(', ', $agrupamentos) . ')'
            ]
        ])->toArray();
      foreach($agrupamentos as $agrupamento)
        {
          $dateTime0         = new \DateTime(); 
          $dateTime1         = new \DateTime();
          $dateTime2         = new \DateTime(implode('-', array_reverse(explode("/", $pessoa->data_nascimento))));
          $dateTime0->modify("-" . $agrupamento->idade_inicial . " months");
          $dateTime1->modify("-" . $agrupamento->idade_final . " months");
          if(($dateTime2 < $dateTime0)&&($dateTime2 > $dateTime1))
            {
              return $agrupamento->id;
            }
        }
      return false;
    }
}