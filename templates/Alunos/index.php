<?php extract($config); 
$this->start('search-topbar');
	echo $this->Element('search-topbar', ['placeholder' => 'Pesquisar em alunos']);
$this->end(); ?>
<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Alunos
			</h3>
		</div>
		<div class="kt-portlet__head-label">
			<a href="<?php echo $this->Url->build(['controller' => 'alunos', 'action' => 'novo']); ?>" class="btn btn-success">
				Adicionar novo
			</a>
		</div>
	</div>
	<div class="kt-portlet__body">
		<?php  
		foreach($alunos as $aluno)
		  {
		  	$full_telefones_array = $aluno->pessoa->telefones_array;
		  	$emails_array = [];
		  	foreach($aluno->parentes as $parente)
		  	  {
		  	  	$full_telefones_array = array_merge($full_telefones_array, $parente->pessoa->telefones_array);
		  	  	array_push($emails_array, $parente->pessoa->email);
		  	  }
		  	$filter_config = 
		  	  [
		  	  	'nome' => strtolower($aluno->pessoa->nome),
		  	  	'email' => strtolower(implode('/', $emails_array)),
		  	  	'telefones' => str_replace([" ", "-", "(", ")"], "", implode('/', $full_telefones_array))
		  	  ];
		  	?>
		  	<div data-filter='<?php echo json_encode($filter_config); ?>' class="row accordion scope-0"  data-key="<?php echo $aluno->id; ?>" data-parent-key="" id="aluno_<?php echo $aluno->id; ?>">
		  		<div class="col-sm-12">
		  			<h4>
		  				<?php echo $aluno->pessoa->nome; ?>
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
$this->append('css'); 
echo $this->Html->css('jquery.atwho.min');
$this->end();
$this->append('script'); 
$dt_this_year = new \DateTime(date('Y') . '-03-31');
$dt_next_year = new \DateTime(date('Y') . '-03-31');
	?>
	<script type="text/javascript">
		var number_format = function(number, decimals, dec_point, thousands_point) 
          {
            if (number == null || !isFinite(number)) {
            	console.log(number);
                throw new TypeError("number is not valid");
            }
            if (!decimals) {
                var len = number.toString().split('.').length;
                decimals = len > 1 ? len : 0;
            }
            if (!dec_point) {
                dec_point = '.';
            }
            if (!thousands_point) {
                thousands_point = ',';
            }
            number = parseFloat(number).toFixed(decimals);
            number = number.replace(".", dec_point);
            var splitNum = number.split(dec_point);
            splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
            number = splitNum.join(dec_point);

            return number;
          }
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
	var options_nacionalidades = '<option value="">Selecione...</option>' +
	  <?php 
	    foreach($nacionalidades as $id => $label)
	      {
	        ?>
	        '<option value="<?php echo $id; ?>"><?php echo $label; ?></option>' +
	        <?php
	      }
	  ?>
	  '';
	var options_naturalidades = '<option value="">Selecione...</option>' +
	  <?php 
	    foreach($naturalidades as $id => $label)
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
	var lista_pessoas = <?php echo json_encode($lista_pessoas); ?>;
	var lista_tags    =  <?php echo json_encode($lista_tags); ?>;
	</script>
	<?php 
	echo $this->Html->script('vanilla-masker');
	echo $this->Html->script('jquery.caret.min');
	echo $this->Html->script('jquery.atwho.min');
    echo $this->Html->script('datepicker-pt-br');
	echo $this->Html->script('class-formulario-aluno'); 
	echo $this->Html->script('class-formulario-ocorrencia'); 
	echo $this->Html->script('lista-alunos');
 $this->end(); ?>