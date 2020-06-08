<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Servicos
			</h3>
		</div>
		<div class="kt-portlet__head-label">
			<a href="<?php echo $this->Url->build(['controller' => 'servicos', 'action' => 'novo']); ?>" class="btn btn-success">
				Adicionar novo
			</a>
		</div>
	</div>
	<div class="kt-portlet__body">
		<?php foreach($servicos as $servico)
          {
          	?>
          	<div class="row accordion scope-0" data-scope="0" data-path="<?php echo json_encode([$servico->id]); ?>" data-key="<?php echo $servico->id; ?>" data-parent-key="" data-parent-scope="" id="servico_aux_<?php echo $servico->id; ?>">
          		<div class="col-sm-12">
          			<h4><?php echo $servico->nome; ?></h4>
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
<?php $this->append('script'); 
	echo $this->Html->script('vanilla-masker');
    echo $this->Html->script('datepicker-pt-br');
	echo $this->Html->script('lista-servicos');
$this->end(); ?>