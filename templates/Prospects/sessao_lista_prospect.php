<?php 
	if($blocks !== "form")
	  {
	  	if(count($blocks))
	  	  {
	  	  	foreach($blocks as $block_data)
	  	  	  {
	  	  	  	extract($block_data);
	  	  	  	?>
	  	  	  	<div style="display: none;" class="row accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>">
	  	  	  		<div class="col-sm-12">
	  	  	  			<h4><?php echo $nome; ?></h4>
	  	  	  		</div>
	  	  	  	</div>
	  	  	  	<div style="display: none;"  class="kt-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
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
	  	<div style="display: none;" class="row accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>" data-unique="<?php echo $unique; ?>">
	  		<?php echo $this->Element('prospect_form', ['update' => $prospect, 'origem' => $prospect->origem, 'config' => $config, 'unique' => $unique]); ?>
	  	</div>
	  	<div style="display: none;"  class="kt-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
	  	<?php
	  	
	  }
	if($prospects_categoria > 0)
	  {
	  	?>
	  	<div class="hidden-prospect-message kt-hidden"><?php echo $prospects_categoria; ?> prospects encontrados nessa categoria</div>
	  	<?php
	  }
?>