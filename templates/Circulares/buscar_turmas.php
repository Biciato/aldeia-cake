<?php 
    foreach($unidades as $unidade)
      {
        ?>
        <div style="display:none;" class="row dados_turmas accordion scope-1" data-scope="1" data-parent-id="sanfona-turmas" id="unidade-<?php echo $unidade->id; ?>">
            <div class="col-sm-12">
                <h4><?php echo $unidade->nome; ?></h4>
            </div>
        </div>
        <div class="kt-separator scope-1 kt-separator--space-sm separator-turmas"></div>
        <div style="display:none;" class="row dados_turmas accordion no-caret scope-2" data-scope="2" data-closed-by="sanfona-turmas" data-parent-id="unidade-<?php echo $unidade->id; ?>">
                <div class="col-sm-12">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--solid">
                            <input type="checkbox" name="unidades_circular" value="" id="todas_escolaridade_<?php echo $unidade->id; ?>" data-unidade="<?php echo $unidade->id; ?>"> Todas de escolaridade<span></span>
                        </label>
                    </div>
                </div>
            </div>
        <div style="display:none;" class="kt-separator scope-2 kt-separator--space-sm separator-turmas"></div>
        <?php
        foreach($unidade->turmas_servico as $servico)
          {
            ?>
            <div style="display:none;" class="row dados_turmas accordion scope-2" data-scope="2" data-closed-by="sanfona-turmas" id="servico-<?php echo $servico['servico']['id']; ?>-unidade-<?php echo $unidade->id; ?>" data-parent-id="unidade-<?php echo $unidade->id; ?>">
                <div class="col-sm-12">
                    <h4><?php echo $servico['servico']['nome']; ?></h4>
                </div>
            </div>
            <div style="display:none;" class="kt-separator scope-2 kt-separator--space-sm separator-turmas"></div>
            <div style="display:none;" class="row dados_turmas accordion no-caret scope-3" data-scope="3" data-closed-by="sanfona-turmas" data-parent-id="servico-<?php echo $servico['servico']['id']; ?>-unidade-<?php echo $unidade->id; ?>">
                <div class="col-sm-12">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--solid">
                            <input type="checkbox" name="turmas[]" class="cb_servico cb_servico_<?php echo $servico['servico']['id']; ?> <?php echo ($servico['servico']['nome'] == "Escolaridade") ? "cb_servico_escolaridade" : ""; ?>" data-servico="<?php echo $servico['servico']['id']; ?>" data-unidade="<?php echo $unidade->id; ?>" value="" id="todas_<?php echo $unidade->id ?>_<?php echo $servico['servico']['id']; ?>"> Todas<span></span>
                        </label>
                    </div>
                </div>
            </div>
            <div style="display:none;" class="kt-separator scope-3 kt-separator--space-sm separator-turmas"></div>
            <?php 
            foreach($servico['turmas'] as $turma)
              {
                ?>
                    <div style="display:none;" class="row dados_turmas accordion scope-3" data-scope="3" data-closed-by="sanfona-turmas" id="servico-<?php echo $servico['servico']['id']; ?>-unidade-<?php echo $unidade->id; ?>-turma-<?php echo $turma->id; ?>" data-parent-id="servico-<?php echo $servico['servico']['id']; ?>-unidade-<?php echo $unidade->id; ?>">
                        <div class="col-sm-12">
                            <h4><?php echo $turma->nome; ?> (<?php echo count($turma->alunos); ?> aluno<?php echo (count($turma->alunos) == 1) ? "" : "s" ?>)</h4>
                        </div>
                    </div>
                    <div style="display:none;" class="kt-separator scope-3 kt-separator--space-sm separator-turmas"></div>
                    <div style="display:none;" class="row dados_turmas accordion scope-6 no-caret" data-parent-id="servico-<?php echo $servico['servico']['id']; ?>-unidade-<?php echo $unidade->id; ?>-turma-<?php echo $turma->id; ?>" id="alunos-servico-<?php echo $servico['servico']['id']; ?>-unidade-<?php echo $unidade->id; ?>-turma-<?php echo $turma->id; ?>">
                        <div class="col-sm-12">
                            <div class="kt-checkbox-list">
                                <?php 
                                foreach($turma->alunos as $aluno)
                                  {
                                    ?>
                                        <label class="kt-checkbox kt-checkbox--solid">
                                            <input type="checkbox" name="alunos[]" class="cb_aluno cb_aluno_unidade_<?php echo $unidade->id; ?>_servico_<?php echo $servico['servico']['id']; ?>_turma_<?php echo $turma->id; ?> <?php echo ($servico['servico']['nome'] == "Escolaridade") ? "cb_escolaridade" : ""; ?>" data-servico="<?php echo $servico['servico']['id']; ?>" data-unidade="<?php echo $unidade->id; ?>" value="<?php echo $aluno->id; ?>|<?php echo $turma->id; ?>"> <?php echo $aluno->pessoa->nome; ?><span></span>
                                        </label>
                                    <?php
                                  }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php
              }
          }
      }
?>