<?php $unique = uniqid(); 
extract($config);?> 
<div style="display: none;" class="row accordion scope-1"  data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>"  id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>" data-unique="<?php echo $unique; ?>">
 	<?php echo $this->Form->create(null, ['id' => 'interactions-form-' . $interacao->id, 'enctype' => 'multipart/form-data']); 	?>
 	    <input type="hidden" name="id" value="<?php echo $interacao->id; ?>"> 
 	    <div class="form-group row">
 	    	<div class="col-sm-12">
 	    		<p>
 	    			<b>Aluno: </b> <?php echo $interacao->prospect->pessoa->nome; ?><br/>
 	    			<br/>
 	    			<b>Parentes: </b><br/>
 	    			<?php foreach($interacao->prospect->parentes as $parente)
 	    			  {
 	    			  	?>
 	    			  	<b>Nome: </b> <?php echo $parente->pessoa->nome; ?> (<?php echo $parentescos[$parente->parentesco]; ?>)<br/>
 	    			  	<b>Email: </b> <?php echo $parente->pessoa->email; ?><br/>
 	    			  	<b>Telefone<?php echo (count($parente->pessoa->telefones_array) > 1) ? 's' : ''; ?>: </b><?php echo implode(',', $parente->pessoa->telefones_array); ?><br/>
 	    			  	<br/>
 	    			  	<?php
 	    			  } 
 	    			?>
 	    		</p>
 	    		<?php if($interacao->informacao)
 	    		  {
 	    		  	?>
 	    		  	<p>
 	    		  		<b>Informações: </b><br />
 	    		  		<?php echo $interacao->informacao_formatada; ?>	
 	    		  	</p>
 	    		  	<?php
 	    		  } ?>
 	    	</div>
 	    </div>
 	    <div class="form-group row"> 
 	    	<div class="col-sm-6">
 	    		<label>Descrição</label>
 	    		<select type="text" class="form-control" name="tipo"> 
 	    			<?php foreach ($tipos_interacao as $id => $label) { ?> 	
 	    				<option value="<?php echo $id; ?>" <?php	echo ($interacao->tipo == $id) ? "selected=\"selected\"" : ""; ?>><?php	echo $label; ?></option>
 	    			<?php } ?> 
 	    		</select>
 	    		<div class="form-text"></div> 
 	    	</div>
 	    	<div class="col-sm-6">
 	    		<label>Título</label>
 	    		<input type="text" class="form-control" name="titulo" value="<?php echo $interacao->titulo; ?>">
 	    		<div class="form-text"></div>
 	    	</div>
 	    </div>
 	    <div class="form-group row">
 	      <div class="col-sm-3">
 	        <label>Responsável</label>
 	        <select type="text" class="form-control" name="responsavel">
 	          <option value="">Selecione...</opiton> 
 	          <?php foreach ($responsaveis as $id => $responsavel) { ?>
 	              <option <?php	echo ($interacao->responsavel == $id) ? "selected=\"selected\"" : ""; ?> value="<?php	echo $id; ?>"><?php	echo $responsavel; ?></option>
 	              <?php } ?>
 	        </select>
 	        <div class="form-text"></div>
 	      </div>
 	      <div class="col-sm-3">
 	        <label>Arquivo</label>
 	        <input type="file" class="form-control" name="arquivo">
 	        <div class=" kt-font-primary">
 	        	<?php if(($interacao->caminho_arquivo)&&($interacao->titulo_arquivo))
 	        	  {
 	        	  	?> 
 	        	  	<div class="dropdown">
 	        	  	   <button class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdown-<?php echo $interacao->id; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
 	        	  	    <?php $titulo = $interacao->titulo_arquivo;
 	        	  	    if(strlen($titulo) > 43)
 	        	  	      {
 	        	  	      	$pedacos = explode('.', $titulo);
 	        	  	      	$ext = array_pop($pedacos);
 	        	  	      	$titulo = substr($titulo, 0, 39) . "[...]." . $ext;
 	        	  	      }
 	        	  	    echo $titulo; ?>
 	        	  	   </button>
 	        	  	   <div class="dropdown-menu" aria-labelledby="dropdown-<?php echo $interacao->id; ?>">
 	        	  	     <a class="dropdown-item ver-arquivo" target="_blank"  href="<?php echo $this->Url->build(['controller' => 'prospects', 'action' => 'visualizar-arquivo', $interacao->id], true); ?>" title="Caso o arquivo tenha uma extensão não suportada pelo navegador (ex: .zip), a nova aba iniciará um download por padrão"> 
 	        	  	       Abrir em nova aba
 	        	  	     </a>
 	        	  	     <a class="dropdown-item" target="_blank"  href="<?php echo $this->Url->build(['controller' => 'prospects', 'action' => 'baixar-arquivo', $interacao->id], true); ?>">
 	        	  	       Baixar
 	        	  	     </a>
 	        	  	     <a class="dropdown-item danger remover-arquivo" data-id="<?php echo $interacao->id; ?>" href="javascript:void(0)">
 	        	  	       Remover arquivo
 	        	  	     </a>
 	        	  	   </div>
 	        	  	 </div>
 	        	  	<?php
 	        	  }
 	        	?>
 	        </div>
 	      </div>
 	      <div class="col-sm-3">
 	        <label>Data</label>
 	        <input type="text" class="form-control" id="interaction-date-<?php echo $interacao->id; ?>" name="data"  value="<?php echo $interacao->data_formatada; ?>">
 	        <div class="form-text"></div>
 	      </div>
 	      <div class="col-sm-3">
 	        <label>Hora</label>
 	         <input type="text" id="interaction-time-<?php echo $interacao->id; ?>" class="form-control" name="hora"  value="<?php echo $interacao->hora_formatada; ?>">
 	         <div class="form-text"></div>
 	      </div>
 	    </div>
 	    <div class="form-group row">
 	      <div class="col-sm-12">
 	        <label>Mensagem</label>
 	        <textarea style="height:150px" class="form-control" name="mensagem"><?php echo $interacao->mensagem; ?></textarea>
 	      </div>
 	    </div>
 	    <div class="form-group row">
 	      <div class="col-sm-12">
 	        <label>Observação</label>
 	        <textarea style="height:150px" class="form-control" name="observacao"><?php echo $interacao->observacao; ?></textarea>
 	      </div>
 	 	</div>
 	 	<div class="row">
 	 		<div class="col-sm-1">
 	 		<label style=" width: 100%">Concluída?</label>
 	 		  <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success kt-switch--lg">
 	 		  <label>
 	 		  <input type="hidden" name="concluida" value="0">
 	 		  <input type="checkbox" <?php echo ($interacao->concluida) ? "checked=\"checked\"" : ""; ?> id="actual-input-<?php echo $key; ?>" name="concluida" value="1">
 	 		  <span></span>
 	 		  </label>
 	 		  </span>
 	 		</div>
 	 		<div class="col-sm-11"></div>
 	 	</div>
 	 	<div class="row">
 	 	  <div class="col-sm-12" style="text-align: right; justify-content: flex-end;">
 	 	      <button class="btn btn-success inserir-interacao" data-id="<?php echo $interacao->id; ?>" style="margin-bottom: 25px;">Salvar</button>
 	 	  </div>
 	 	</div>
 	<?php echo $this->Form->end(); ?> 
 	<div style="display: none;"  class="kt-separator scope-1 kt-separator--space-sm"></div> 