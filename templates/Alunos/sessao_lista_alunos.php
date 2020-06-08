<?php 
$unique = uniqid();
?>
<div style="display: none;" class="row accordion no-caret scope-1" data-scope="1" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="0" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>" data-unique="<?php echo $unique; ?>">
	<?php echo $this->Element('aluno_form', ['update' => $aluno, 'config' => $config, 'unique' => $unique]); ?>
</div>