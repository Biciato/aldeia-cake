<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-12">
		<?php $documento = (isset($documento)) ? $documento : $capitulo->Documento; 
		echo $this->Element('caminho_documento', ['documento' => $documento, 'scope' => 0, 'key' => 'doc', 'tipo' => 'documento']);
		?>
	</div>
</div>
<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				<?php echo ($tipo === 'documento') ? $documento->nome : $capitulo->nome; ?>
			</h3>
		</div>
		<div class="kt-portlet__head-label">
			<a href="javascript:void(0)" class="btn btn-primary" id="editar-capitulo">
				Editar atual
			</a>
			&nbsp;&nbsp;
			<a href="javascript:void(0)" class="btn btn-danger" id="excluir-capitulo">
				Excluír atual
			</a>
			&nbsp;&nbsp;
			<a href="javascript:void(0)" class="btn btn-success" id="adicionar-novo-capitulo">
				Adicionar novo
			</a>
		</div>
	</div>
		<div class="kt-portlet__body" >
			<?php if($tipo !== 'documento')
			  {
			  	?>
				  	<div class="col-sm-12 color-darker-gray">
				  		<?php echo $capitulo->conteudo; ?>
				  	</div>
			  	<?php
			  } ?>
			<span id="capitulos-notification">
				<?php 
					if($tipo === 'documento')
					  {
					  	echo $this->Element('sumario', ['capitulos' => $capitulos]);
					  }
					elseif($tipo === 'capitulo')
					  {
					  	echo $this->Element('sumario', ['capitulos' => $capitulo->proximos]);
					  }
				?>
			</span>
		</div>
</div>
<div class="kt-portlet kt-hidden" id="portlet-adicionar-capitulo">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Adicionar capítulo
			</h3>
		</div>
	</div>
		<div class="kt-portlet__body">
			<?php echo $this->Form->create(null, ['class' => 'kt-form', 'id' => 'form-adicionar-capitulo']); ?>
			        <div class="form-group">
			            <input type="text" class="form-control" name="nome" placeholder="Título">
			        </div>
			        <div class="form-group">
			        	<textarea class="form-control" id="editor" name="conteudo" placeholder="Conteúdo" style="height: 1000px;"></textarea>
			        </div>
			        <input type="hidden" name="pai" value="<?php echo ($tipo === 'documento') ? "" : $capitulo->id; ?>">
			        <input type="hidden" name="documento" value="<?php echo ($tipo === 'documento') ? $documento->id : $capitulo->documento; ?>">
			<?php echo $this->Form->end(); ?>
		</div>
		<div style="text-align:right" class="kt-footer">
			<a href="javascript:void(0)" class="btn btn-primary" id="cancelar-adicao-capitulo">Cancelar</a>
			&nbsp;
			<a href="javascript:void(0)" class="btn btn-success" id="adicionar-capitulo">Salvar</a>
		</div>
</div>
<div class="kt-portlet kt-hidden" id="portlet-editar">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Editar <?php echo str_replace('i', 'í', $tipo); ?>
			</h3>
		</div>
	</div>
		<div class="kt-portlet__body">
			    <?php echo $this->Form->create(null, ['class' => 'kt-form', 'id' => 'form-editar-' . $tipo]);
	                if($tipo === 'capitulo')
	                  {
	                   	?>
		                   	<div class="form-group">
		                   	    <input type="text" class="form-control" name="nome" placeholder="Título" value="<?php echo $capitulo->nome; ?>">
		                   	</div>
		                   	<div class="form-group">
		                   		<textarea class="form-control" id="editor1" name="conteudo" placeholder="conteudo" style="height: 1000px;"><?php echo $capitulo->conteudo; ?></textarea>
		                   	</div>
		                   	<input type="hidden" name="id" value="<?php echo $capitulo->id; ?>">
	                   	<?php
	                  } 
	                elseif($tipo === 'documento')
	                 {
	                   	?>
	                   		<div class="form-group">
	                   		    <input type="text" class="form-control" name="nome" placeholder="Título" value="<?php echo $documento->nome; ?>">
	                   		</div>
	                   		<div class="form-group">
	                   			<textarea class="form-control" name="descricao" placeholder="Descrição" style="height: 106px;"><?php echo $documento->descricao; ?></textarea>
	                   		</div>
		                   	<input type="hidden" name="id" value="<?php echo $documento->id; ?>">
	                   	<?php
	                  }
	                ?>
	            <?php echo $this->Form->end(); ?>
		</div>
		<div style="text-align:right" class="kt-footer">
			<a href="javascript:void(0)" class="btn btn-primary" id="cancelar-edicao">Cancelar</a>
			<a href="javascript:void(0)" class="btn btn-success" id="editar">Salvar</a>
		</div>
</div>
	
<?php $this->append('script');
	echo $this->Html->script('https://code.jquery.com/ui/1.10.4/jquery-ui.js');
	echo $this->Html->script('ckeditor');
	echo $this->Html->script('ckfinder/ckfinder'); ?>
	<script type="text/javascript">
		var dados = {'tipo': "<?php echo $tipo; ?>", 'id': "<?php echo ($tipo === 'documento') ? $documento->id : $capitulo->id; ?>",  documento: "<?php echo ($tipo === 'documento') ? $documento->id : $capitulo->documento; ?>"};
	</script>
<?php echo $this->Html->script('capitulos');
$this->end();
$this->append('css'); 
	echo $this->Html->css('"https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css');
$this->end(); ?>