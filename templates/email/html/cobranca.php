<?php 
	$pt_months = 
	  [
		'', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
	  ];
	$total = 0;
	foreach($aluno->pessoa->boletos_vencidos as $boleto)
	  {
		  $total += $boleto->valor_atualizado;
	  }
	/*<!img src="https://<?php echo HTTP_HOST . '/checar?usuario=<?php echo $aluno->responsavel->pessoa->id; ?>&cobranca=<?php echo $cobranca->id; ?>' ?>" />
*/
?>
<p>
	<img border="0" width="1" height="1" alt="" src="<?php echo $this->Url->build('/', true); ?>checar?email=<?php echo $cobranca->id; ?>&responsavel=<?php echo $aluno->responsavel->pessoa->id; ?>">
	<h2>Por favor, verifique.</h2></br>
    <h3>Boletos em aberto.</h3>
	Rio de Janeiro, <?php echo date('d'); ?> de <?php echo $pt_months[(int)date('m')]; ?> de <?php echo date('Y'); ?>.<br/>
	Prezado(a) <?php echo $aluno->responsavel->pessoa->nome; ?>, responsável por <?php echo mb_strtoupper($aluno->pessoa->nome); ?>.<br/>
	Solicitamos sua especial atenção para verificar os pagamentos das cotas relacionadas<br/>
	abaixo, totalizando <?php echo $aluno->pessoa->boletos_vencidos[0]->dias_atrasados; ?> dias em atraso no valor de R$ <?php echo $this->Grana->formatar($total); ?>, pois<br/>
	as mesmas encontram-se em aberto nos nossos controles.<br/>
	<table width="70%">
	  <tr>
	  	<td>Data de Vencimento</td>
	  	<td>Valor</td>
	  	<td>Dias em Atraso</td>
	  	<td>Valor Atualizado</td>
		<td></td>
	  </tr>
	  <?php 
	 	foreach($aluno->pessoa->boletos_vencidos as $boleto)
		 {
		   ?>
			   <tr>
					 <td><?php echo $boleto->data_vencimento_formatada; ?></td>
					 <td><?php echo $this->Grana->formatar($boleto->valor_sem_desconto); ?></td>
					 <td><?php echo $boleto->dias_atrasados; ?></td>
					 <td><?php echo $this->Grana->formatar($boleto->valor_atualizado); ?></td>
					 <td>
					   <a target="_blank" href="/financeiro/boleto/<?php echo $boleto->id; ?>">
						   Imprimir
					   </a>
				   </td>
				</tr>
		   <?php
		 } 
	  ?>
	</table>
	Por favor, caso o pagamento já tiver sido efetuado queira desconsiderar este aviso. Neste<br/>
	caso solicitamos sua colaboração para que nos seja encaminhado uma cópia do recibo de<br/>
	quitação. Desta forma poderemos analisar o ocorrido.<br/>
	Contarmos com a sua parceira é sempre razão de prazer e orgulho!<br/>
	Em caso de dúvida procure-me.<br/>
	Fabio Righetti<br/>
	fabio@aldeiamontessori.com.br - (21) 3899 7044 - (21) 99243 9126<br/>
	<a href="https://www.aldeiamontessori.com.br/">https://www.aldeiamontessori.com.br/</a><br/>
	<a href="https://www.facebook.com/aldeiamontessori">https://www.facebook.com/aldeiamontessori</a><br/>
</p>