<?php
	if(count($servicos))
	  {
	  	?>
	  		<div class="row" style="display:none;" data-parent-id="financeiro-<?php echo $key; ?>">
	  		<input type="hidden" name="financeiro" value="">
	  		<?php $total = 0; 
	  		foreach ($servicos as $servico)
	  		  {
	  		    $desconto   = ($valores_servicos[$servico->id] != "new") ? $valores_servicos[$servico->id] : "";
	  		    $valor_novo = (int)$servico->valor_atual->valor;
	  		    if($desconto)
	  		      {
	  		        $valor_desconto = ceil((($valor_novo*(int)$desconto)/100));
	  		        $valor_novo     = ($valor_novo - $valor_desconto);
	  		      }
	  		    $total   += $valor_novo;
	  		  ?>
	  		  <div class="linha-financeiro">
	  		        <?php echo $servico->ServicoAux->nome; ?>
	  		        <div class="numeros-financeiro">
	  		          <input data-valor="<?php echo $servico->valor_atual->valor; ?>" data-valor-original="<?php echo $servico->valor_atual->valor; ?>" type="text" value="<?php echo $desconto; ?>%" type="tel" name="financeiro[<?php echo $servico->id; ?>]"  class="campo-desconto" />
	  		          <div class="valor-financeiro">
	  		            R$ <?php echo number_format(($valor_novo/100), 2, ",", "."); ?>
	  		          </div>
	  		        </div>
	  		    </div>
	  		  <?php
	  		  }
	  		?>
	  		<div class="linha-financeiro">
	  		      Total
	  		      <div class="numeros-financeiro">
	  		        <div class="valor-financeiro" id="total-financeiro-<?php echo $key; ?>">
	  		          R$ <?php echo number_format(($total/100), 2, ',', '.'); ?>
	  		        </div>
	  		      </div>
	  		  </div>
	  		</div>
	  	<?php
	  } 
	else
	  {
	  	echo "sem-resultados";
	  }
?>