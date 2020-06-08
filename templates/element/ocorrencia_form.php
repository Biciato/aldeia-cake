<?php echo $this->Form->create(null, ['id' => 'ocorrencia-form-' . $key, 'url' => false]); ?>
    <input type="hidden" name="tipo" value="" />
    <?php if(@$comentario)
      {
          ?>
            <input type="hidden" name="comentario_de" value="<?php echo $key; ?>"/>
          <?
      } ?>
    <div class="row <?php echo ($accordion) ? 'accordion' : ''; ?> scope-1 form-group no-caret" <?php echo (@$comentario) ? "" : 'style="display:none"'; ?> data-scope="1" data-key="" data-parent-key="nova_ocorrencia" data-parent-id="nova_ocorrencia" >
        <div class="col-sm-12">
        <a href="javascript:void(0)" data-tipo="0" class="btn btn-default btn-sm btn-icon"  data-container="body" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Da família para a escola" data-skin="dark">
            <i class="fa fa-arrow-left"></i>
        </a> &nbsp;
        <a href="javascript:void(0)" data-tipo="1" class="btn btn-default btn-sm btn-icon"  data-container="body" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Apenas uma observação" data-skin="dark">
            <i class="fa fa-arrow-up"></i>
        </a> &nbsp;
        <a href="javascript:void(0)" data-tipo="2" class="btn btn-default btn-sm btn-icon"  data-container="body" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Da escola para a família" data-skin="dark">
            <i class="fa fa-arrow-right"></i>
        </a>
        </div>
    </div>
    <div class="row <?php echo ($accordion) ? 'accordion' : ''; ?> form-group no-caret scope-1" <?php echo (@$comentario) ? "" : 'style="display:none"'; ?> data-scope="1" data-key="" data-parent-key="nova_ocorrencia" data-parent-id="nova_ocorrencia" >
        <div class="col-sm-12">
            <label for="texto">Texto</label>
            <textarea class="form-control" name="texto" style="height:300px" id="texto-nova-ocorrencia"></textarea>
            <div class="form-text"></div>
        </div>
    </div>
    <div class="row <?php echo ($accordion) ? 'accordion' : ''; ?> form-group no-caret scope-1" <?php echo (@$comentario) ? "" : 'style="display:none"'; ?> data-scope="1" data-key="" data-parent-key="nova_ocorrencia" data-parent-id="nova_ocorrencia">
        <div class="col-sm-12">
            <label for="arquivo">Arquivo</label>
            <input class="form-control" type="file" name="arquivo"/>
            <div class="form-text"></div>
        </div>
    </div>
    <div class="row <?php echo ($accordion) ? 'accordion' : ''; ?> form-group no-caret scope-1" <?php echo (@$comentario) ? "" : 'style="display:none"'; ?> data-scope="1" data-key="" data-parent-key="nova_ocorrencia" data-parent-id="nova_ocorrencia">
        <?php if(@$comentario)
          {
            ?>
                <div class="col-sm-12" style="text-align: right; justify-content: flex-end">
                    <a class="btn btn-success" style="margin-bottom: 25px; color:white;"  id="nova-ocorrencia-<?php echo $key; ?>">Inserir</a>
                </div>
        <?php } ?>
    </div>
<?php echo $this->Form->end(); ?>