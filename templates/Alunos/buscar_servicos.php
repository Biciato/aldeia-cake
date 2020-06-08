<?php
	if(count($servicos))
	  {
	  	?>
	  		<div class="row" style="display:none;" data-parent-id="servicos-<?php echo $key; ?>">
	  		<input type="hidden" name="servicos" value="[]">
	  			<div class="kt-checkbox-list servicos-checkbox-list">
	  				<?php foreach ($servicos as $servico)
	  				  {
	  					?>
  						<label class="kt-checkbox kt-checkbox--disabled">
  					        <input checked="checked" type="checkbox" name="servicos[]" value="<?php echo $servico->id; ?>"  disabled="disabled"> <?php echo $servico->ServicoAux->nome; ?> <span></span>
  					    </label>
	  					<?php
	  				  }
	  				?>
	  			</div>
	  		</div>
	  	<?php
	  } 
	else
	  {
	  	echo "sem-resultados";
	  }
?>