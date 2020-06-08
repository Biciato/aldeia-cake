<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Documentos
			</h3>
		</div>
		<div class="kt-portlet__head-label">
			<a href="javascript:void(0)" class="btn btn-success" id="adicionar-novo-doc">
				Adicionar novo
			</a>
		</div>
	</div>
	<!--begin::Form-->
		<div class="kt-portlet__body">
			<div class="kt-notification-v2 sumario" id="documentos-notification">
				<?php 
					foreach($documentos as $documento)
					  {
					  		?> 
					  		    <a href="<?php echo $this->Url->build(
					  		              [
					  		                'controller' => 'documentos',
					  		                'action'     => 'ver',
					  		                'documento',
					  		                str_replace(" ", "-", strtolower($documento->nome)),
					  		                $documento->id
					  		              ]); ?>" class="kt-notification-v2__item item-sumario" data-id="<?php echo $documento->id; ?>">
					  		          <div class="kt-notification-v2__itek-wrapper thick-border-left">
					  		            <div class="kt-notification-v2__item-title">
					  		              <?php echo $documento->nome; ?>
					  		            </div>
					  		            <div class="kt-notification-v2__item-desc">
					  		              <?php echo substr(strip_tags($documento->descricao), 0, 120) . "..."; ?>
					  		            </div>
					  		          </div>  
					  		        </a>
					  		  
					  	<?php
					  }
				?>
			</div>
		</div>
	<!--end::Form-->
</div>
<div class="kt-portlet kt-hidden" id="portlet-adicionar-documento">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Adicionar documento
			</h3>
		</div>
	</div>
		<div class="kt-portlet__body">
			    <?php echo $this->Form->create(null, ['class' => 'kt-form', 'id' => 'form-adicionar-documento']); ?>
	                        <div class="kt-portlet__body">
	                        <div class="form-group">
	                            <input type="text" class="form-control" name="nome" placeholder="Título">
	                        </div>
	                        <div class="form-group">
	                        	<textarea class="form-control" name="descricao" placeholder="Descrição" style="height: 106px;"></textarea>
	                        </div>
	                    </div>
	                <?php echo $this->Form->end(); ?>
		</div>
		<div style="text-align:right" class="kt-footer">
			<a href="javascript:void(0)" class="btn btn-primary" id="cancelar-adicao-documento">Cancelar</a>
			<a href="javascript:void(0)" class="btn btn-success" id="adicionar-documento">Salvar</a>
		</div>
</div>
<?php 
$this->append('script'); 
	echo $this->Html->script('https://code.jquery.com/ui/1.10.4/jquery-ui.js'); 
	echo $this->Html->script('documentos');
$this->end(); ?>