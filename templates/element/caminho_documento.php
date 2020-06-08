<?php if($tipo == 'documento')
  {
  	$context = $documento;
  	$proximos = $documento->primeiros_capitulos;
  	$parent_key = $parent_id = '';
  	$id = 'doc_accordion';
  	$display = "block";
  }
else
  {
  	$context = $capitulo;
  	$proximos = $capitulo->proximos;
  	$display = "none";
  }
$url = $this->Url->build(['controller' => 'documentos', 'action' => 'ver', $tipo, str_replace(" ", "-", strtolower($context->nome)), $context->id]);
?>
<div style="display: <?php echo $display; ?>" class="row accordion scope-<?php echo $scope; ?>" data-parent-id="<?php echo $parent_id; ?>"  data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" id="<?php echo $id; ?>">
	<div class="col-sm-12">
		<h4>
			<?php echo $context->nome; ?> <a href="<?php echo $url; ?>" class="btn btn-sm btn-default">Ver</a>
		</h4>
	</div>	
</div>
<div  style="display: <?php echo $display; ?>" class="kt-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
<?php $scope++; 
foreach($proximos as $proximo)
  {
  	echo $this->Element('caminho_documento',  ['capitulo' => $proximo, 'scope' => $scope, 'key' => $proximo->id, 'parent_key' => $key, 'tipo' => 'capitulo', 'id' => 'capitulo_' . $proximo->id, 'parent_id' => $id]);
  }
?>