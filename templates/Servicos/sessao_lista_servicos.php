<?php 
	if($blocks !== "form")
	  {
	  	if(count($blocks))
	  	  {
	  	  	foreach($blocks as $block_data)
	  	  	  {
	  	  	  	extract($block_data);
	  	  	  	?>
	  	  	  	<div style="display: none;" data-path="<?php echo json_encode($path); ?>" class="row accordion services-accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>">
	  	  	  		<div class="col-sm-12">
	  	  	  			<h4><?php echo $nome; ?></h4>
	  	  	  		</div>
	  	  	  	</div>
	  	  	  	<div style="display: none;"  class="kt-separator services-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
	  	  	  	<?php
	  	  	  }
	  	  }
	  	else
	  	  {
	  	  	echo "sem-resultados";
	  	  }
	  }
	else
	  {
	  	$unique = uniqid();
	  	?>
	  	<div style="display: none;" class="row accordion services-accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>" data-unique="<?php echo $unique; ?>">
	  		<?php echo $this->Form->create(null, ['class' => 'services_form', 'id' => 'servicos_' . $servico->id, 'data-id' => $servico->id]); ?>
		  		<div class="row form-group">
		  			<input type="hidden" name="servico" value="<?php echo $servico->id; ?>">
		  			<div class="col-sm-4">
		  				<label>
		  					Data de início
		  				</label>
		  				<input class="form-control" autocomplete="off" type="text" name="data_inicio">
		  				<div class="form-text"></div>
		  			</div>
		  			<div class="col-sm-4">
		  				<label>
		  					Data final
		  				</label>
		  				<input class="form-control" autocomplete="off" type="text" name="data_final">
		  				<div class="form-text"></div>
		  			</div>
		  			<div class="col-sm-4">
		  				<label>
		  					Valor
		  				</label>
		  				<input class="form-control" autocomplete="off" type="text" name="valor">
		  				<div class="form-text"></div>
		  			</div>
		  		</div>
		  		<div class="row form-group">
		  			<div class="col-sm-6">
		  				Histórico de valores
		  			</div>
		  			<div class="col-sm-6 text-right">
		  				<button class="btn btn-success inserir-valor" data-key="<?php echo $servico->id; ?>">Salvar</button>
		  			</div>
		  		</div>
		  		<div class="row form-group">
			  		<?php 
			  		foreach($valores as $valor)
				  		{
				  			?>
				  			R$ <?php echo $valor->valor_formatado; ?> para vigorar de <?php echo $valor->data_inicio->format('d/m/Y'); ?> até <?php echo $valor->data_final->format('d/m/Y'); ?> (registrado em <?php echo $valor->data_criacao->format('d/m/Y H:i:s'); ?>)<br/>
				  			<?php
				  		}
			  		 ?>
		  		</div>
	  		<?php echo $this->Form->end(); ?>
	  	</div>
	  	<div style="display: none;"  class="kt-separator services-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
	  	<?php
	  	
	  }
	if($servicos_categoria > 0)
	  {
	  	?>
	  	<div class="hidden-prospect-message kt-hidden"><?php echo $servicos_categoria; ?> servicos encontrados nessa categoria</div>
	  	<?php
	  }
?>