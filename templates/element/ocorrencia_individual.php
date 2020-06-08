<?php
$tipos = 
  [
     0 => 'fa-arrow-left',
     1 => 'fa-arrow-up',
     2 => 'fa-arrow-right'
  ];
?>
<div class="row accordion no-caret scope-<?php echo $scope; ?>" data-parent-id="<?php echo $parent_id; ?>" style="<?php echo (!isset($force_display)) ? 'display:none;' : 'width: 100%;'; ?> margin-bottom:20px">
    <div class="col-sm-12">
        <i class="fa <?php echo $tipos[$ocorrencia->tipo]; ?>"></i> <?php echo $ocorrencia->data_criacao->format('d/m/Y H:i:s'); ?>
        <br/>
        <?php echo $ocorrencia->texto_formatado; ?>
        <br/>
        Registrado por: <?php echo $ocorrencia->usuario->nome; ?>. <?php echo ($ocorrencia->visto_por) ? 'Visto por: ' . $ocorrencia->visto_por_formatado : ""; ?>
    </div>
</div>
<?php if(isset($ocorrencia->comentarios))
  {
    foreach($ocorrencia->comentarios as $comentario)
    {
      ?>
      <div class="row accordion no-caret scope-<?php echo ((int)$scope + 1); ?>" data-parent-id="<?php echo $parent_id; ?>" style="<?php echo (!isset($force_display)) ? 'display:none;' : 'width: 100%;'; ?> margin-bottom:20px">
      <div class="col-sm-12">
          <i class="fa <?php echo $tipos[$comentario->tipo]; ?>"></i> <?php echo $comentario->data_criacao->format('d/m/Y H:i:s'); ?>
          <br/>
          <?php echo $comentario->texto_formatado; ?>
          <br/>
          Registrado por: <?php echo $comentario->usuario->nome; ?>. <?php echo ($comentario->visto_por) ? 'Visto por: ' . $comentario->visto_por_formatado : ""; ?>
      </div>
  </div>
      <?php
    } 
  } 
?>
<div class="row accordion no-caret scope-<?php echo $scope; ?>" data-parent-id="<?php echo $parent_id; ?>" style="<?php echo (!isset($force_display)) ? 'display:none;' : 'width: 100%;'; ?> margin-bottom:20px;">
    <div class="col-sm-12">
    <?php if(($marcar_visto)&&(!in_array($usuario_id, $ocorrencia->visto_por_array)))
      {
        ?>
          <a class="btn btn-success btn-icon marcar-como-visto" data-id="<?php echo $ocorrencia->id; ?>"><i class="fa fa-check"></i></a>
        <?php
      } ?>
        <a class="btn btn-primary btn-icon inserir-comentario" data-id="<?php echo $ocorrencia->id; ?>"><i class="fa fa-comment"></i></a>
    </div>
</div>
<div class="kt-separator scope-<?php echo $scope; ?> kt-separator--space-sm" style="<?php echo (!isset($force_display)) ? 'display:none;' : 'width: 100%;'; ?>"></div>
