<?php if(count($turmas_servico) > 0)
  {
    ?>
    <div class="turmas-<?php echo $key; ?>">
        <input type="hidden" name="turmas"/>
    </div>
    <?php
    foreach($turmas_servico as $turmas)
      {
        ?>
        <div class="row form-group turmas-<?php echo $key; ?>" data-parent-id="agendamento-<?php echo $key; ?>">
            <div class="col-sm-12">
                <label for="turmas[]">Turma <?php echo ($turmas['servico']->servico == 3) ? 'Sistema Creche' : $turmas['servico']->ServicoAux->nome;?></label>
                <select class="form-control" name="turmas[<?php echo $turmas['servico']->ServicoAux->id; ?>]" id="turmas-<?php echo $key; ?>-<?php echo $turmas['servico']->id; ?>">
                    <option value="">Selecione...</option>
                    <?php foreach($turmas['turmas'] as $turma)
                      {
                        ?>
                        <option value="<?php echo $turma->id; ?>"><?php echo $turma->nome; ?></option>
                        <?php
                      } ?>
                </select>
            </div>
        </div>
        <?php
      }
  } 
else
  {
    echo "sem-resultados";
  }
?>