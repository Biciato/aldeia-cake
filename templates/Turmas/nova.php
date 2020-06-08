<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Nova turma
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
      <?php echo $this->element('turma_form', ['config' => ['update' => false, 'unidades' => $unidades, 'servicos' => $servicos]]); ?>
	  <div style="text-align:right; justify-content:flex-end" class="kt-footer">
        <button class="btn btn-success" id="inserir-turma-1">Salvar</button>
    </div>
	</div>
</div>
<div style="display:none">
<?php echo $this->Form->create(null, ['url' => false]);
echo $this->Form->end(); ?>
</div>
