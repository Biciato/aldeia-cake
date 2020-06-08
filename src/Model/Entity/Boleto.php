<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\I18n\FrozenTime;

class Boleto extends Entity {

  protected $_accessible = 
    [
        '*' => true,
    ];
  private function formataValor($_valor)
    {
		$valor = null;
    	if($_valor)
    	  {
    	  	$valor = number_format(($_valor/100), 2, ",", ".");
    	  }
    	return $valor;
	}
  protected function _getValorFormatado()
    {
		return $this->formataValor($this->_fields['valor_com_desconto']);
	}
  protected function _getValorLiquidoRecebidoFormatado()
    {
    	return $this->formataValor($this->_fields['valor_liquido_recebido']);
    }
  protected function _getDataVencimentoFormatada()
    {
    	$data = null;
    	if($this->_fields['data_vencimento'])
    	  {
			if(is_string($this->_fields['data_vencimento']))
			  {
				  $this->_fields['data_vencimento'] = new FrozenTime($this->_fields['data_vencimento']);
			  }
    	  	$data = $this->_fields['data_vencimento']->format('d/m/Y');
    	  }
    	return $data;
	}
    protected function _getJurosReais()
     {
		$valor_juros = round(((int)$this->_fields['valor_sem_desconto'] / 100) / 30);
        if($valor_juros < 1)
          {
            $valor_juros = 1;
		  }
		return $valor_juros;
	 }
	protected function _getValorAtualizado()
	  {
		$juros_reais = call_user_func([$this, '_getJurosReais']);
		$dias_atrasados = call_user_func([$this, '_getDiasAtrasados']);
		return ((int)$this->_fields['valor_sem_desconto'] + ($juros_reais*(int)$dias_atrasados));
	  }
	protected function _getDiasAtrasados()
	  {
		$dias = false;
		if($this->_fields['data_vencimento'])
    	  {
			if(is_string($this->_fields['data_vencimento']))
			  {
				  $this->_fields['data_vencimento'] = new FrozenTime($this->_fields['data_vencimento']);
			  }
			$hoje = new \DateTime();
			$vencimento = new \DateTime($this->_fields['data_vencimento']->format('Y-m-d'));
			if(($hoje > $vencimento)&&(!$this->_fields['data_liquidacao']))
			  {
				$interval = $vencimento->diff($hoje);
				$dias = $interval->format('%a');
			  }
		  }
		return $dias;
	  }
  
}