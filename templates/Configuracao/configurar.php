<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				<?php echo $config['label']; ?> 
			</h3>
		</div>
	</div>
    <div class="kt-portlet__body">
    	<?php echo $this->Form->create(null, ['id' => 'aux_form', 'class' => 'kt-form']);
    	$aux_counter = count($auxiliares_cadastrados);
    	if((bool)$aux_counter)
    	  {
    	  	?>
    	  	<div class="row aux-separator-row">
    	  		<div class="col-sm-12">
    	  			<?php foreach($auxiliares_cadastrados as $k => $aux)
    	  			  {
    	  				if($config['form'] === 'default')
    	  			  	  {
    	  			  		?>
                                <div class="auxiliar-individual col-sm-12" data-id="<?php echo $aux->id; ?>" id="auxiliar-individual-<?php echo $k; ?>">
        	  			  			<div class="form-group row aux-fields-<?php echo $k; ?>">
        	  			  			   <div class="col-sm-11">
        	  			  			        <label <?php echo ($aux->ativo) ? "" : "class=\"text-danger\""; ?> for="<?php echo $auxiliar; ?>[<?php echo $k; ?>][nome]">Nome</label>
        	  			  			        <input class="form-control" type="text" name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][nome]" value="<?php echo $aux->nome; ?>">
        	  			  			        <div class="form-text"></div>
        	  			  			    	<input type="hidden" name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][id]" value="<?php echo $aux->id; ?>">
        	  			  			    	<input type="hidden" name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][ativo]" value="0">
        	  			  			    </div>
        	  			  			    <div class="col-sm-1">
        	  			  			    	<label>Ativo?</label>
        	  			  	  	    		<input name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][ativo]" data-switch="true" type="checkbox" <?php echo ($aux->ativo) ? 'checked="checked"' : ''; ?> value="1" data-on-text="Sim" data-off-text="Não" data-on-color="success" data-off-color="danger">
        	  			  			    </div>
        	  			  			</div>
                                </div>
    	  			  		<?php
    	  			  	  }
    	  			  	else
    	  			  	  {
    	  			  	  	$options = [];
                            ?>
                            <div class="auxiliar-individual col-sm-12" data-id="<?php echo $aux->id; ?>" id="auxiliar-individual-<?php echo $k; ?>">
                                <?php
        	  			  	  	foreach($config['form']['rows'] as $row)
        	  			  	  	  {
        	  			  	  	  	?>
        	  			  	  	  	<div class="row form-group aux-fields-<?php echo $k; ?>">
        	  			  	  	  	<?php
        	  			  	  	  	foreach($row as $name => $settings)
        	  			  	  	  	  {
        	  			  	  	  	  	?>
        	  			  	  	  	  	<div class="col-sm-<?php echo $settings['col']; ?>">
        	  			  	  	  	  		<label <?php echo ($aux->ativo) ? "" : "class=\"text-danger\""; ?> for="<?php echo $auxiliar; ?>[<?php echo $k; ?>][<?php echo $name; ?>]"><?php echo $settings['label']; ?></label>
        	  			  	  	  	  	<?php
        	  			  	  	  	  	if(($settings['type'] === 'text')||($settings['type'] === 'number'))
        	  			  	  	  	  	  {
                                            $aux_val = (@$settings['field-val']) ? $aux->{$settings['field-val']} : $aux->{$name};
        	  			  	  	  	  	  	?>
        	  			  	  	  	  	  	<input type="<?php echo $settings['type']; ?>" name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][<?php echo $name; ?>]" class="form-control" value="<?php echo $aux_val; ?>" <?php echo (@$settings['mask']) ? "data-mask=\"" . $settings['mask'] . "\"" : ""; ?>>
        	  			  	  	  	  	  	<div class="form-text"></div>
        	  			  	  	  	  	  	<?php
        	  			  	  	  	  	  }
        	  			  	  	  	  	elseif($settings['type'] === 'select')
        	  			  	  	  	  	  {
        	  			  	  	  	  	  	?>    	  			  			  
        	  			  	  	  	  	  	<select name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][<?php echo $name; ?>]" class="form-control">
        	  			  	  	  	  	  		<option value="0">Selecione...</option>
        	  			  	  	  	  	  		<?php
        	  			  	  	  	  	  		  $selected = $aux->{$name};
        	  			  	  	  	  	  		  $options[$name] = call_user_func($settings['options_src']);
        	  			  	  	  	  	  		  foreach($options[$name] as $opt_val => $opt_label)
        	  			  	  	  	  	  		    {
        	  			  	  	  	  	  		    	?>
        	  			  	  	  	  	  		    	<option <?php echo ($opt_val == $selected) ? "selected=\"selected\"" : ""; ?> value="<?php echo $opt_val; ?>"><?php echo $opt_label; ?></option>
        	  			  	  	  	  	  		    	<?php
        	  			  	  	  	  	  		    }	
        	  			  	  	  	  	  		?>
        	  			  	  	  	  	  	</select>
        	  			  	  	  	  	  	<div class="form-text"></div>
        	  			  	  	  	  	  	<?php
        	  			  	  	  	  	  }
        	  			  	  	  	  	elseif($settings['type'] === 'checkbox')
        	  			  	  	  	  	  {
        	  			  	  	  	  	  	?>
    										<div class="kt-checkbox-inline" data-key="<?php echo $k; ?>" data-name="<?php echo $name; ?>">
    											<input type="hidden" name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][<?php echo $name; ?>]">
    											<?php
    												$selecteds = $aux->{$name . '_array'};
    												$options[$name] = call_user_func($settings['options_src']);
    												foreach($options[$name] as $opt_val => $opt_label)
    												  {
    												  	?>
    												  	<label class="kt-checkbox kt-checkbox--bold <?php echo (in_array($opt_val, $selecteds)) ? "kt-checkbox--brand" : ""; ?>">
    												  		<input name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][<?php echo $name; ?>][]" type="checkbox" value="<?php echo $opt_val; ?>" <?php echo (in_array($opt_val, $selecteds)) ? "checked=\"checked\"" : ""; ?>> <?php echo $opt_label; ?>
    												  		<span></span>
    												  	</label>
    												  	<?php
    												  }	
    											?>
    										</div>
    										<span class="form-text"></span>
        	  			  	  	  	  	  	<?php
        	  			  	  	  	  	  }
        	  			  	  	  	  	elseif($settings['type'] === 'status-switch') 
        	  			  	  	  	  	  {
        	  			  	  	  	  	  	?>
        	  			  	  	  	  	  	<input type="hidden" name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][ativo]" value="0">
        	  			  	  	    		<input name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][ativo]" data-switch="true" type="checkbox" <?php echo ($aux->ativo) ? 'checked="checked"' : ''; ?> value="1" data-on-text="Sim" data-off-text="Não" data-on-color="success" data-off-color="danger">
        	  			  	  	  	  	  	<?php 
        	  			  	  	  	  	  }
        	  			  	  	  	  	?>
        	  			  	  	  	  	</div>
        	  			  	  	  	  	<?php
        	  			  	  	  	  }
        	  			  	  	  	?>
        	  			  	  	  	</div>
        	  			  	  	  	<?php
        	  			  	  	  }
        	  			  	  	?>
        	  			  	  	<input type="hidden" name="<?php echo $auxiliar; ?>[<?php echo $k; ?>][id]" value="<?php echo $aux->id; ?>">
    	  			  	  	</div>
                            <?php
    	  			  	  }
    	  			  }
    	  			?>
    	  		</div>
    	  	</div>
    	  	<?php
    	  } ?>
    	<div class="row form-group" id="add-auxiliar-box">
    	   <div class="col-sm-12">
    	     <a href="javascript:void(0)" class="btn btn-success" id="adicionar-auxiliares">Adicionar novo</a>
    	   </div>
    	</div>
    	<input type="hidden" name="tableClass" value="<?php echo $config['tableClass']; ?>">
    	<input type="hidden" name="auxiliar" value="<?php echo $auxiliar; ?>">
    	<?php echo $this->Form->end(); ?>
	</div>
	<div style="text-align:right" class="kt-footer">
    <button class="btn btn-success" style="visibility: hidden;">A</button>
    <button class="btn btn-success" id="inserir-auxiliares">Salvar</button>
  </div>
</div>
<?php $this->append('script');
  echo $this->Html->script('vanilla-masker'); ?>
<script type="text/javascript">
	var aux_keys = <?php echo $aux_counter; ?>;
	var form_template = 
	<?php if($config['form'] === 'default')
	  {
	  	?>
	  		'<div class="form-group row aux-fields-__COUNTER__">' +
	  		   '<div class="col-sm-12">' +
	  		      '<label for="<?php echo $auxiliar; ?>[__COUNTER__][nome]">Nome</label>' + 
	  		      '<input class="form-control" type="text" name="<?php echo $auxiliar; ?>[__COUNTER__][nome]" value="">' +
	  		      '<div class="form-text"></div>' +
	  		   '</div>' +
	  		'</div>' +
	  		'<div class="row form-group aux-fields-__COUNTER__">' +
	  			'<div class="col-sm-12">' +
	  			    '<a class="btn btn-danger remover-auxiliar" style="color:white" href="javascript:void(0)" data-key="[__COUNTER__]">Remover auxiliar</a>' + 
	  			'</div>' + 
	  		'</div>';
	  	<?php
	  }
	else
	  {
		foreach($config['form']['rows'] as $row_key => $row)
    	  {
    	  	$is_switch_row = ($row_key == $config['form']['status-switch-row']);
    	  	?>
	  	  	'<div class="row form-group aux-fields-__COUNTER__">' + 
    	  	<?php
    	  	$keys = array_keys($row);
    	  	array_pop($keys);
    	  	$last_field = array_pop($keys);
    	  	foreach($row as $name => $settings)
    	  	  {
                        if(isset($settings['options_src'])&&(!isset($options[$name])))
                          {
                            $options[$name] = call_user_func($settings['options_src']);
                          }
    	  			    if($settings['type'] != 'status-switch')
    	  			      {
    	  			  	    ?>
    	  					'<div class="col-sm-<?php echo (($name == $last_field)&&($is_switch_row)) ? ($settings['col'] + 1) : $settings['col']; ?>">' + 
    	  			  		'<label  for="<?php echo $auxiliar; ?>[__COUNTER__][<?php echo $name; ?>]"><?php echo $settings['label']; ?></label>' + 
    	  			        <?php
    	  			      }
    	  				if(($settings['type'] === 'text')||($settings['type'] === 'number'))
    	  			  	  {
    	  			  	   	?>
    	  			  	   	'<input <?php echo (@$settings['mask']) ? "data-mask=\"" . $settings['mask'] . "\"" : ""; ?> type="<?php echo $settings['type']; ?>" name="<?php echo $auxiliar; ?>[__COUNTER__][<?php echo $name; ?>]" class="form-control">' +
    	  			  	   	'<div class="form-text"></div>' +
    	  			  	   	<?php
    	  			  	  }
    	  			  	elseif($settings['type'] === 'select')
    	  			  	  {
    	  			  	  	?>    	  			  			  
    	  			  	  	'<select name="<?php echo $auxiliar; ?>[__COUNTER__][<?php echo $name; ?>]" class="form-control">' +
    	  			  	  		'<option value="0">Selecione...</option>' + 
    	  			  	  		<?php
    	  			  	  		  foreach($options[$name] as $opt_val => $opt_label)
    	  			  	  		    {
    	  			  	  		    	?>
    	  			  	  		    	'<option value="<?php echo $opt_val; ?>"><?php echo $opt_label; ?></option>' +
    	  			  	  		    	<?php
    	  			  	  		    }	
    	  			  	  		?>
    	  			  	  	'</select>' + 
    	  			  	  	'<div class="form-text"></div>' +
    	  			  	  	<?php
    	  			  	  }
    	  			  	elseif($settings['type'] === 'checkbox')
    	  			  	  {
    	  			  	   	?>
							'<div class="kt-checkbox-inline" data-key="__COUNTER__" data-name="<?php echo $name; ?>">' +
							'<input type="hidden" name="<?php echo $auxiliar; ?>[__COUNTER__][<?php echo $name; ?>]">' + 
							<?php
								foreach($options[$name] as $opt_val => $opt_label)
								  {
								  	?>
								  	'<label class="kt-checkbox kt-checkbox--bold">' +
    							  		'<input name="<?php echo $auxiliar; ?>[__COUNTER__][<?php echo $name; ?>][]" type="checkbox" value="<?php echo $opt_val; ?>"> <?php echo $opt_label; ?>' +
								  		'<span></span>' + 
								  	'</label>' +
								  	<?php
								  }	
							?>
							'</div>' +
							'<span class="form-text"></span>' +
    	  	 	  	  	  	<?php
    	  			      }
    	  			?>
    	  		'</div>' +
    	  		<?php
    	  	  }
    	  	?>
    	  	'</div>' +
    	  	<?php
    	  }
    	?>
    	'<div class="row form-group aux-fields-__COUNTER__">' +
	  		'<div class="col-sm-12">' +
	  		    '<a class="btn btn-danger remover-auxiliar" style="color:white" href="javascript:void(0)" data-key="[__COUNTER__]">Remover auxiliar</a>' + 
	  		'</div>' + 
	  	'</div>';
    	<?php
	  }
	?>
</script>
<?php
    echo $this->Html->script('https://code.jquery.com/ui/1.10.4/jquery-ui.js');
    echo $this->Html->script('configurar-auxiliares');
 $this->end(); ?>