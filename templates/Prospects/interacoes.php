<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Interações
			</h3>
		</div>
		<div class="kt-portlet__head-label">
			
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
						<option value="">Todos Resultados</option>
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
						<option value="">Todos Resultados</option>
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
				<div class="col-sm-4" id="date_range">
					<label>Primeiro atendimento</label>
					<input class="form-control filter_input" type="text" name="primeiro_atendimento">
					<div class="form-text"></div>
				</div>
				<div class="col-sm-4">
					<label>Status da interação</label>
					<select name="status" class="form-control filter_input">
						<option value="">Todos Resultados</option>
						<?php foreach($status_interacoes as $id => $label) 
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
					<label>Responsável</label>
					<select name="responsavel" class="form-control filter_input">
						<option value="">Todos Resultados</option>
						<option value="no">Sem responsável atribuído</option>
						<?php foreach($responsaveis as $id => $label) 
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
		<?php echo $this->Form->end(); 
		foreach($interacoes as $interacao)
		  {
		  	$icon_color = "info";
		  	switch ($interacao->status) 
		  	  {
		  		case 1:
		  			$icon_color = "success";
		  			break;
		  		case 2: 
		  		 	$icon_color = "warning";
		  			break;
		  	  }
		  	$parentes = [];
		  	foreach($interacao->prospect->parentes as $parente)
		  	  { 
		  	  	$telefones = [];
		  	  	foreach($parente->pessoa->telefones_array as &$telefone)
		  	  	  {
		  	  	  	$telefones[] = str_replace(["(", ")", " ", "-"], "", $telefone);
		  	  	  }
		  	  	$parentes[] = 
		  	  	  [
		  	  	  	'nome' => $parente->pessoa->nome,
		  	  	  	'telefones' => $telefones,
		  	  	  	'email' => $parente->pessoa->email
		  	  	  ];
		  	  }
		  	$responsavel = ($interacao->responsavel) ? $interacao->responsavel->id : "no";
		  	$filter_config = 
		  	  [
		  	  	'prospect' => 
		  	  	  [
		  	  	  	'nome' => $interacao->prospect->pessoa->nome,
		  	  	  	'parentes' => $parentes
		  	  	  ],
		  	  	'unidade' => $interacao->prospect->unidade,
		  	  	'agrupamento' => $interacao->prospect->agrupamento,
		  	  	'primeiro_atendimento' => $interacao->prospect->data_primeiro_atendimento_ymd,
		  	  	'status' => $interacao->status,
		  	  	'responsavel' => $responsavel
		  	  ];
		  	?>
		  	<div data-filter='<?php echo json_encode($filter_config); ?>' class="row accordion scope-0"  data-key="<?php echo $interacao->id; ?>" data-parent-key="" id="interacao_<?php echo $interacao->id; ?>">
		  		<div class="col-sm-12">
		  			<h4>
		  				<?php echo $interacao->prospect->pessoa->nome; ?> <small style="font-size: 11px"> <?php echo $interacao->titulo . " - " . $interacao->data_formatada . " " . $interacao->hora_formatada; ?> <?php echo ($interacao->responsavel) ? " - " . $interacao->responsavel->pessoa->nome : ""; ?></small> 
		  				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon <?php echo $icon_color; ?>" >
		  				    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
		  				    	<?php if($interacao->status == 1)
		  				    	  {
		  				    	  	?>
			  				    	  	<rect id="bound" x="0" y="0" width="24" height="24"/>
			  				    	  	<circle id="Oval-5" fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
			  				    	  	<path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" id="Path-92" fill="#000000" fill-rule="nonzero"/>
		  				    	  	<?php
		  				    	  } 
		  				    	elseif($interacao->status == 2)
		  				    	  {
		  				    	  	?>
			  				    	  	<rect id="bound" x="0" y="0" width="24" height="24"/>
			  				    		<path d="M11.1669899,4.49941818 L2.82535718,19.5143571 C2.557144,19.9971408 2.7310878,20.6059441 3.21387153,20.8741573 C3.36242953,20.9566895 3.52957021,21 3.69951446,21 L21.2169432,21 C21.7692279,21 22.2169432,20.5522847 22.2169432,20 C22.2169432,19.8159952 22.1661743,19.6355579 22.070225,19.47855 L12.894429,4.4636111 C12.6064401,3.99235656 11.9909517,3.84379039 11.5196972,4.13177928 C11.3723594,4.22181902 11.2508468,4.34847583 11.1669899,4.49941818 Z" id="Path-117" fill="#000000" opacity="0.3"/>
			  				    	  	<rect id="Rectangle-9" fill="#000000" x="11" y="9" width="2" height="7" rx="1"/>
			  				    	  	<rect id="Rectangle-9-Copy" fill="#000000" x="11" y="17" width="2" height="2" rx="1"/>
		  				    	  	<?php
		  				    	  }
		  				    	else
		  				    	  {
		  				    	  	?>
			  				    	  	<rect id="bound" x="0" y="0" width="24" height="24"/>
			  				    	  	<circle id="Oval-5" fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
			  				    	  	<rect id="Rectangle-9" fill="#000000" x="11" y="10" width="2" height="7" rx="1"/>
			  				    	  	<rect id="Rectangle-9-Copy" fill="#000000" x="11" y="7" width="2" height="2" rx="1"/>
		  				    	  	<?php
		  				    	  }
		  				    	?>   
		  				    </g>
		  				</svg>
		  			</h4>
		  		</div>	
		  	</div>
		  	<div class="kt-separator scope-0 kt-separator--space-sm "></div>
		  	<?php
		  } 
		?>
	</div>
	<?php echo $this->Form->create(null, ['id' => 'token-form']); echo $this->Form->end(); ?>
</div>
<?php 
$this->append('script'); 
	echo $this->Html->script('vanilla-masker');
    echo $this->Html->script('datepicker-pt-br');
    echo $this->Html->script('interacoes');
$this->end();
$this->append('css'); 
	echo $this->Html->css('dt');
	echo $this->Html->css('dt-responsive');
	echo $this->Html->css('dt-btns');
$this->end(); ?>