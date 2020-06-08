<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Prospects
			</h3>
		</div>
		<div class="kt-portlet__head-label">
			<a href="<?php echo $this->Url->build(['controller' => 'prospects', 'action' => 'novo']); ?>" class="btn btn-success">
				Adicionar novo
			</a>
		</div>
	</div>
	<div class="kt-portlet__body">
		<?php echo $this->Form->create(null, ['id' => 'filtro_interacoes']); ?>
			<div class="row form-group">
				<div class="col-sm-4">
					<label>Prospect</label>
					<input class="form-control filter_input" type="text" name="prospect">
					<div class="form-text"></div>
				</div>
				<div class="col-sm-4">
					<label>Unidade</label>
					<select name="unidade" class="form-control filter_input">
						<option value="">Todos resultados</option>
						<?php foreach($unidades as $id => $label) 
						  {
						  	?>
						  		<option value="<?php echo $id; ?>"><?php echo $label; ?></option>
						  	<?php
						  }
						?>
					</select>
					<div class="form-text"></div>
				</div>
				<div class="col-sm-4">
					<label>Agrupamento</label>
					<select name="agrupamento" class="form-control filter_input">
						<option value="">Todos resultados</option>
						<?php foreach($agrupamentos as $id => $label) 
						  {
						  	?>
						  		<option value="<?php echo $id; ?>"><?php echo $label; ?></option>
						  	<?php
						  }
						?>
						<option value="-1">Sem agrupamento</option>
					</select>
					<div class="form-text"></div>
				</div>
			</div>
			<div class="row form-group">
				<div class="col-sm-6" id="date_range">
					<label>Primeiro atendimento</label>
					<input class="form-control filter_input" type="text" name="primeiro_atendimento">
					<div class="form-text"></div>
				</div>
				<div class="col-sm-6">
					<label>Status das interações</label>
					<select name="status" class="form-control filter_input">
						<option value="">Todos resultados</option>
						<?php foreach($status_interacoes_prospects as $id => $label) 
						  {
						  	?>
						  		<option value="<?php echo $id; ?>"><?php echo $label; ?></option>
						  	<?php
						  }
						?>
					</select>
					<div class="form-text"></div>
				</div>
			</div>
			<div class="kt-separator kt-separator--space-sm "></div>
		<?php echo $this->Form->end(); ?>
		<div class="row accordion scope-0" data-scope="0" data-key="0" data-parent-key="" data-parent-scope="" id="indicacao">
			<div class="col-sm-12">
				<h4>Por pesquisa orgânica</h4>
			</div>	
		</div>
		<div class="kt-separator scope-0 kt-separator--space-sm"></div>
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
    $dt_this_year = new \DateTime(date('Y') . '-03-31');
    $dt_next_year = new \DateTime(date('Y') . '-03-31');
	?>
	<script type="text/javascript">
	  var options_parentescos = '<option value="">Selecione...</option>' +
	  <?php 
	    foreach($parentescos as $id => $label)
	      {
	        ?>
	        '<option value="<?php echo $id; ?>"><?php echo $label; ?></option>' +
	        <?php
	      }
	  ?>
	  '';
	var options_tipo_interacao = '<option value="">Selecione...</option>' +
        <?php 
          foreach($tipos_interacao as $id => $label)
            {
              ?>
              '<option value="<?php echo $id; ?>"><?php echo $label; ?></option>' +
              <?php
            }
        ?>
        '';
    var options_responsaveis = '<option value="">Selecione...</option>' +
        <?php 
          foreach($responsaveis as $id => $label)
            {
              ?>
              '<option value="<?php echo $id; ?>"><?php echo $label; ?></option>' +
              <?php
            }
        ?>
        '';
	var datas_corte =
	  {
	    esse_ano : function()
	      {
	        var dt = new Date('<?php echo $dt_this_year->format('Y-m-d'); ?>');
	        return dt;
	      },
	    ano_que_vem : function()
	      {
	        var dt = new Date('<?php echo $dt_next_year->format('Y-m-d'); ?>');
	        return dt;
	      }
	  }
	</script>
	<?php echo $this->Html->script('class-formulario-prospect'); 
	echo $this->Html->script('lista-prospects'); ?>
<?php $this->end();
$this->append('css'); 
	echo $this->Html->css('dt');
	echo $this->Html->css('dt-responsive');
	echo $this->Html->css('dt-btns');
$this->end(); ?>