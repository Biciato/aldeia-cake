<?php extract($config); ?>
<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Inserir novo serviço
			</h3>
		</div>
	</div>
    <div class="kt-portlet__body" >
      <?php echo $this->Form->create(null, ['id' => 'servico-form', 'url' => false]); ?>
	    <div class="row form-group">
	    	<div class="col-sm-6">
	    		<label for="unidade">
	    			Unidade
	    		</label>
	    		<select class="form-control" name="unidade">
	    			<option value="">Selecione...</option>
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
	    	<div class="col-sm-6">
	    		<label for="servico">
	    			Serviço
	    		</label>
	    		<select class="form-control" name="servico">
	    			<option value="">Selecione...</option>
	    			<?php foreach($servicos as $id => $label) 
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
	    <div class="row form-group">
	    	<div class="col-sm-4">
	    		<label for="curso">
	    			Curso
	    		</label>
	    		<select class="form-control" name="curso">
	    			<option value="">Selecione...</option>
	    			<?php foreach($cursos as $entity) 
	      			  {
	      			  	?>
	      			  	<option value="<?php echo $entity->id; ?>"><?php echo $entity->nome; ?></option>
	      			  	<?php
	      			  }
	    			?>
	    		</select>
	    		<div class="form-text"></div>
	    	</div>
	    	<div class="col-sm-4">
	    		<label for="agrupamento">
	    			Agrupamento
	    		</label>
	    		<select class="form-control disabled" disabled="disabled" name="agrupamento">
	    			<option value="">Selecione...</option>
	    		</select>
	    		<div class="form-text"></div>
	    	</div>	
	    	<div class="col-sm-4">
	    		<label for="nivel">
	    			Nível
	    		</label>
	    		<select class="form-control disabled" disabled="disabled" name="nivel">
	    			<option value="">Selecione...</option>
	    		</select>
	    		<div class="form-text"></div>
	    	</div>		
	    </div>
	    <div class="row form-group">
	    	<div class="col-sm-4">
	    		<label for="turno">
	    			Turno
	    		</label>
	    		<select class="form-control" name="turno">
	    			<option value="">Selecione...</option>
	    			<?php foreach($turnos as $id => $entity) 
	      			  {
	      			  	?>
	      			  	<option value="<?php echo $entity->id; ?>"><?php echo $entity->nome; ?></option>
	      			  	<?php
	      			  }
	    			?>
	    		</select>
	    		<div class="form-text"></div>
	    	</div>
	    	<div class="col-sm-4">
	    		<label for="permanencia">
	    			Permanência
	    		</label>
	    		<select class="form-control disabled" disabled="disabled" name="permanencia">
	    			<option value="">Selecione...</option>
	    		</select>
	    		<div class="form-text"></div>
	    	</div>	
	    	<div class="col-sm-4">
	    		<label for="horario">
	    			Horário
	    		</label>
	    		<select class="form-control disabled" disabled="disabled" name="horario">
	    			<option value="">Selecione...</option>
	    		</select>
	    		<div class="form-text"></div>
	    	</div>		
	    </div>
	    <div class="row form-group">
	    	<div class="col-sm-4">
	    		<label for="valor">Valor</label>
	    		<input type="text" class="form-control" name="valor[valor]">
	    		<div class="form-text"></div>
	    	</div>
	    	<div class="col-sm-4">
	    		<label for="valor">Data de início</label>
	    		<input type="text" class="form-control" name="valor[data_inicio]">
	    		<div class="form-text"></div>
	    	</div>
	    	<div class="col-sm-4">
	    		<label for="valor">Data final</label>
	    		<input type="text" class="form-control" name="valor[data_final]">
	    		<div class="form-text"></div>
	    	</div>
	    </div>
      <?php echo $this->Form->end(); ?>
	</div>
	<div style="text-align:right; justify-content: flex-end;" class="kt-footer">
    <button class="btn btn-success" id="inserir-servico">Salvar</button>
  </div>
</div>
<?php $this->append('script');
echo $this->Html->script('vanilla-masker');
echo $this->Html->script('datepicker-pt-br');

$recursive = ['cursos' => [], 'turnos' => []];
foreach($cursos as $curso)
  {
  	$agrupamentos = [];
  	foreach($curso->agrupamentos_entities as $agrupamento)
  	  {
  	  	$niveis = [];
  	  	foreach($agrupamento->niveis_entities as $nivel)
  	  	  {
  	  	  	$niveis[$nivel->id] = $nivel->nome;
  	  	  }
  	  	$agrupamentos[$agrupamento->id] = 
  	  	  [
  	  	  	'nome' => $agrupamento->nome,
  	  	  	'niveis' => $niveis
  	  	  ];
  	  }
  	$recursive['cursos'][$curso->id] = ['agrupamentos' => $agrupamentos];
  }
foreach($turnos as $turno)
  {
  	$permanencias = [];
  	foreach($turno->permanencias_entities as $permanencia)
  	  {
  	  	$horarios = [];
  	  	foreach($permanencia->horarios_entities as $horario)
  	  	  {
  	  	  	$horarios[$horario->id] = $horario->nome;
  	  	  }
  	  	$permanencias[$permanencia->id] =
  	  	  [
  	  	  	'nome' => $permanencia->nome,
  	  	  	'horarios' => $horarios
  	  	  ];
  	  }
  	$recursive['turnos'][$turno->id] = ['permanencias' => $permanencias];
  }
?>
<script type="text/javascript">
	var dados = <?php echo json_encode($recursive); ?>
</script>
<?php
echo $this->Html->script('novo-servico');
$this->end();	
?>