<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Inserir novo colaborador
			</h3>
		</div>
	</div>
    <div class="kt-portlet__body" >
	<?php echo $this->Element('colaborador_form', ['update' => false, 'origem' => 0, 'config' => $config]); ?>
	</div>
	<div style="text-align:right; justify-content: flex-end" class="kt-footer">
    <button class="btn btn-success" id="inserir-colaborador-1">Salvar</button>
  </div>
</div>