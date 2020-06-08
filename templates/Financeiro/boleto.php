<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UFT-8'>
	</head>
	<body onload="document.getElementById('form').submit();">
		<form style="display:none;" action="/boleto/imprimir.php" id='form' enctype='application/x-www-form-urlencoded' accept-charset='UTF-8' method="post">

			<!-- CAMPOS VAZIOS -->
			<input type='text' name="data[Boleto][demonstrativo1]" value=''>
			<input type='text' name="data[Boleto][demonstrativo2]" value=''>
			<input type='text' name="data[Boleto][demonstrativo3]" value=''>
			<input type='text' name="data[Boleto][quantidade]"     value=''>
			<input type='text' name="data[Boleto][valor_unitario]" value=''>
			<input type='text' name="data[Boleto][endereco]"       value=''>
			<input type='text' name="data[Boleto][cidade_uf]"      value=''>

			<!-- CAMPOS COM VALORES -->



			<input type='text' name="data[Boleto][valor_boleto]"              value='<?php echo number_format(($valor_boleto/100), 2, ',', ''); ?>'>
			<input type='text' name="data[Boleto][cobrado]"                   value='<?php echo number_format(($cobrado/100), 2, ',', ''); ?>'>
			<input type='text' name="data[Boleto][multa]"                     value='<?php echo number_format(($multa/100), 2, ',', ''); ?>'>
			<input type='text' name="data[Boleto][descontoCondedidoExibicao]" value='<?php echo number_format(($desconto_concedido_exibicao/100), 2, ',', ''); ?>'>
			<input type='text' name="data[Boleto][instrucoes5]"               value='<?php echo $instrucoes_valor; ?>'>
			<input type='text' name="data[Boleto][instrucoes1]"               value='JUROS DE MORA DE 1.0% MENSAL (R$ <?php echo number_format(($reais_juros_dia/100), 2, ',', ''); ?> AO DIA).'>

			<?php
				$sacador = '';

				if(isset($responsavel->pessoa->nome))
				{
					$sacador = $responsavel->pessoa->nome.' CPF: '.$responsavel->pessoa->cpf;
				}
			?>

			<!-- OUTROS CAMPOS -->
			<input type='text' name="data[Boleto][nosso_numero]" value='<?php echo $boleto->numero_interno; ?>'>
			<input type='text' name="data[Boleto][numero_documento]" value='<?php echo $boleto->numero_documento; ?>'>
			<input type='text' name="data[Boleto][data_vencimento]" value='<?php echo $boleto->data_vencimento_formatada; ?>'>
			<input type='text' name="data[Boleto][data_documento]" value='<?php echo date('d/m/Y'); ?>'>
			<input type='text' name="data[Boleto][data_processamento]" value='<?php echo date('d/m/Y'); ?>'>
			<input type='text' name="data[Boleto][sacado]" value='<?php echo $sacador; ?>'>
			<input type='text' name="data[Boleto][endereco1]" value='<?php echo $endereco->logradouro.' '.$endereco->numero.' '.$endereco->complemento; ?>'>
			<input type='text' name="data[Boleto][endereco2]" value='<?php echo $endereco->bairro.' - '.$endereco->cep.' - '.$endereco->cidade.' - '.$endereco->estado; ?>'>
			<input type='text' name="data[Boleto][instrucoes2]" value='MULTA DE 2% APÓS O VENCIMENTO.'>
			<input type='text' name="data[Boleto][instrucoes3]" value='NÃO RECEBER APÓS 30 DIAS DO VENCIMENTO.'>
			<input type='text' name="data[Boleto][instrucoes4]" value='O PAGAMENTO DESTE NÃO QUITA DÉBITOS ANTERIORES.'>
			<input type='text' name="data[Boleto][aceite]" value='<?php echo 'N'; ?>'>
			<input type='text' name="data[Boleto][especie]" value='<?php echo 'R$'; ?>'>
			<input type='text' name="data[Boleto][especie_doc]" value='<?php echo 'DS'; ?>'>
			<input type='text' name="data[Boleto][codigo_cliente]" value='<?php echo $unidade->codigo_beneficiario; ?>'>
			<input type='text' name="data[Boleto][ponto_venda]" value='<?php echo $unidade->agencia; ?>'>
			<input type='text' name="data[Boleto][carteira]" value='101'>
			<input type='text' name="data[Boleto][carteira_descricao]" value='COBRANCA SIMPLES - RCR'>
			<input type='text' name="data[Boleto][identificacao]" value='<?php echo $unidade->razao_social; ?>'>
			<input type='text' name="data[Boleto][cpf_cnpj]" value='<?php echo $unidade->cnpj; ?>'>
			<input type='text' name="data[Boleto][cedente]" value='<?php echo $unidade->razao_social; ?>'>
			<input type='text' name="data[Boleto][sacador/avalista]" value='<?php echo $sacador; ?>'>
			<input type='text' name="data[Boleto][enderecoAldeia]" value='<?php echo $unidade->endereco; ?>'>
			
		</form>
	</body>
</html>
