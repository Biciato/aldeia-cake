<?php extract($config);
$key = ($update) ? $key : 1;
$form_options = ['id' => 'turma-form-' . $key, 'class' => 'kt-form', 'data-key' => $key, 'enctype' => 'multipart/form-data'];
if($update)
  {
    $form_options['data-update']    = 1;
    $form_options['data-colaboradores'] = count($update->colaboradores_array);
  }
echo $this->Form->create(null, $form_options); 
if($update)
  {
    ?>
    <input type="hidden" name="id" value="<?php echo $update->id; ?>">
    <?php
  }?>
    <div class="row form-group">
        <div class="col-sm-6">
            <label for="nome">Nome ou número</label>
            <input type="text" class="form-control" name="nome" value="<?php echo @$update->nome; ?>">
            <div class="form-text"></div>
        </div>
        <div class="col-sm-6">
            <label for="unidade">Unidade</label>
            <select <?php echo (@$update) ? 'disabled="disabled"' : ''; ?> class="form-control" name="unidade">
                <option value="">Selecione...</option>
                <?php foreach($unidades as $unidade)
                    {
                    ?>
                        <option <?php echo (@$update->unidade == $unidade->id) ? 'selected="selected"' : ''; ?> value="<?php echo $unidade->id; ?>"><?php echo $unidade->nome; ?></option>
                    <?php
                    } 
                ?>
            </select>
            <div class="form-text"></div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">
            <label for="servico">Serviço</label>
            <select <?php echo (@$update) ? 'disabled="disabled"' : ''; ?> class="form-control  <?php echo (@$update) ? 'disabled' : ''; ?>" name="servico" >
                <option value="">Selecione...</option>
                <?php foreach($servicos as $servico)
                    {
                    ?>
                        <option <?php echo (@$update->servico == $servico->id) ? 'selected="selected"' : ''; ?> value="<?php echo $servico->id; ?>"><?php echo $servico->nome; ?></option>
                    <?php
                    }
                ?>
            </select>
            <div class="form-text"></div>
        </div>
        <div class="col-sm-3">
            <label for="quantidade_vagas">Quantidade de vagas</label>
            <input class="form-control" name="quantidade_vagas" type="number" value="<?php echo @$update->quantidade_vagas; ?>"/>
            <div class="form-text"></div>
        </div>
        <div class="col-sm-3">
            <label for="horario_inicial">Horário inicial</label>
            <input  class="form-control" name="horario_inicial" value="<?php echo ($update) ? $update->horario_inicial->format('H:i') : ""; ?>"/>
            <div class="form-text"></div>
        </div>
        <div class="col-sm-3">
            <label for="horario_final">Horário final</label>
            <input class="form-control" name="horario_final" value="<?php echo ($update) ? $update->horario_final->format('H:i') : ""; ?>"/>
            <div class="form-text"></div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-12">
            <label>Dias da semana</label>
            <div class="kt-checkbox-inline" >
                <input type="hidden" name="dias_semana">
                <?php
                    $dias_semana = 
                      [
                        'Domingo',
                        'Segunda',
                        'Terça',
                        'Quarta',
                        'Quinta',
                        'Sexta',
                        'Sábado'
                      ];
                    foreach($dias_semana as $numero => $dia)
                      {
                        ?>
                        <label class="kt-checkbox kt-checkbox--bold">
                            <input name="dias_semana[]" type="checkbox" <?php echo (@in_array($numero, $update->dias_semana_array))? 'checked="checked"' : ''; ?> value="<?php echo $numero; ?>" > <?php echo $dia; ?>
                            <span></span>
                        </label>
                        <?php
                      }	
                ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="colaboradores"/>
    <div class="row form-group" id="botao-colaborador-<?php echo $key; ?>"  data-parent-id="colaborador-<?php echo $key; ?>">
        <div class="col-sm-12">
            <a href="javascript:void(0)" class="btn btn-success" id="adicionar-colaborador-<?php echo $key; ?>">Adicionar colaborador</a>
        </div>
    </div>
    <?php if($update)
      {
        foreach($update->colaboradores_array as $colaborador_key => $colaborador_id)
          {
            ?>
            <div data-parent-id="colaboradores-<?php echo $key; ?>" class="form-group row colaborador-fields-<?php echo $colaborador_key; ?>-<?php echo $key; ?>" >
                <div class="col-sm-11">
                    <label for="colaborador">Colaborador</label>
                    <select class="form-control" data-colaborador="<?php echo $colaborador_id; ?>" data-key="<?php echo $colaborador_key; ?>" data-selector=".colaborador-fields-<?php echo $colaborador_key; ?>-<?php echo $key; ?>" id="colaborador-<?php echo $colaborador_key; ?>-<?php echo $key; ?>" name="colaboradores[]">
                    </select> 
                    <div class="form-text"></div>
                </div>
                <div class="col-sm-1">
                    <label style="visibility:hidden">æææ</label>
                    <a class="btn btn-danger remover-colaborador-<?php echo $key; ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $colaborador_key; ?>">Remover</a>
                </div>
            </div>
            <?php
          }
      } 
    ?>
    
<?php  echo $this->Form->end(); 
if($update)
  {
    ?>
   <div style="text-align:right; justify-content:flex-end" class="kt-footer">
        <button class="btn btn-success" id="inserir-turma-<?php echo $key; ?>">Salvar</button>
    </div>
    <?php
  }
echo $this->Form->end();
  if(!$update)
    {
        $this->append('css'); 
            echo $this->Html->css('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css');
        $this->end();
        $this->append('script');
        echo $this->Html->script('class-formulario-turma');
        echo $this->Html->script('vanilla-masker');
        echo $this->Html->script('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js');
        ?>
        <script type="text/javascript">
            $(document).ready(function()
                {
                window['turmas-form-1'] = new FormularioTurma(1, function()
                    {
                        window.location.reload();
                    });
                window['turmas-form-1'].turma_form.init();
                });
        </script>
        <?php
    $this->end();
    }
 ?>