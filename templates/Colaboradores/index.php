<?php $this->start('search-topbar');
	echo $this->Element('search-topbar', ['placeholder' => 'Pesquisar em colaboradores']);
$this->end(); ?>
<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Colaboradores
			</h3>
		</div>
		<div class="kt-portlet__head-label">
			<a href="<?php echo $this->Url->build(['controller' => 'colaboradores', 'action' => 'novo']); ?>" class="btn btn-success">
				Adicionar novo
			</a>
		</div>
	</div>
	<div class="kt-portlet__body">
		<?php  
		foreach($colaboradores as $colaborador)
		  {
		  	$filter_config = 
		  	  [
		  	  	'nome' => strtolower($colaborador->pessoa->nome),
		  	  	'email' => strtolower($colaborador->pessoa->email),
		  	  	'telefones' => str_replace([" ", "-", "(", ")"], "", implode('/', $colaborador->pessoa->telefones_array))
		  	  ];
		  	?>
		  	<div data-filter='<?php echo json_encode($filter_config); ?>' class="row accordion scope-0"  data-key="<?php echo $colaborador->id; ?>" data-parent-key="" id="colaborador_<?php echo $colaborador->id; ?>">
		  		<div class="col-sm-12">
		  			<h4>
		  				<?php echo $colaborador->pessoa->nome; ?>
		  			</h4>
		  		</div>	
		  	</div>
		  	<div class="kt-separator scope-0 kt-separator--space-sm"></div>
		  	<?php
		  } 
		?>
		<!--<div class="row accordion scope-0" data-scope="0" data-key="1">
			<div class="col-sm-12">
				<h4>Por pesquisa orgânica</h4>
			</div>	
		</div>
		<div class="kt-separator scope-0 kt-separator--space-sm"></div>-->
	</div>
	<?php echo $this->Form->create(null, ['id' => 'token-form']); echo $this->Form->end(); ?>
</div>
<?php 
$this->append('script'); 
	echo $this->Html->script('vanilla-masker');
    echo $this->Html->script('datepicker-pt-br');
    echo $this->Html->script('cropper');
    echo $this->Html->script('jquery-cropper');
	echo $this->Html->script('class-formulario-colaborador'); 
	echo $this->Html->script('lista-colaboradores'); ?>
<?php $this->end();
$this->append('css'); 
	echo $this->Html->css('cropper');
$this->end(); ?>