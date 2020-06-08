<?php 
	if($blocks !== "turma")
      {
        if(count($blocks))
          {
              foreach($blocks as $block_data)
                {
                    extract($block_data);
                    ?>
                    <div style="display: none;" class="row accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>">
                        <div class="col-sm-12">
                            <h4><?php echo $nome; ?>
                            <span class="end">
                              <?php echo $quantity; ?>
                            </span></h4>
                        </div>
                    </div>
                    <div style="display: none;"  class="kt-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
                    <?php
                }
          }
        else
          {
              echo "sem-resultados";
          }
      }
	else
	  {
      $unique = uniqid();
        ?>
	  	<div class="row accordion scope-4" data-scope="4" data-key="<?php echo $update->id; ?>" id="<?php echo $unique; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-id="<?php echo $parent_id; ?>">
        <div class="col-sm-12" style="margin-top:20px">
          <p>Alunos</p>
          <?php foreach($alunos as $aluno)
            {
              ?>
              <div class="linha-aluno">
                <h4><?php echo $aluno->pessoa->nome; ?></h4>
              </div>
              <div  class="kt-separator kt-separator--space-sm"></div>
              <?php
            } ?>
          <div class="row form-group" style="margin-top: 40px; margin-bottom:40px;">
              <div class="col-sm-12">
                  <a href="/turmas/lista-alunos/<?php echo $update->id; ?>" target="_blank" class="btn btn-success">Imprimir lista de alunos</a>
              </div>
          </div>
            <div class="row form-group" style="margin-top:20px; margin-bottom:40px;" >
                <div class="col-sm-10">
                  <label for="mes">Mês</label>
                  <select class="form-control" name="mes" id="mes_frequencia_<?php echo $update->id; ?>">
                    <option value="">Selecione...</option>
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                  </select>
                </div>
                <div class="col-sm-2">
                  <label style="visibility:hidden">æææ</label>
                  <a class="btn btn-success gerar-frequencia" data-ano="<?php echo $update->ano_letivo; ?>" data-turma="<?php echo $update->id; ?>" style="color:white" href="javascript:void(0)" id="gerar_frequencia_<?php echo $unique; ?>">Gerar controle de frequência</a>
                </div>
            </div>
          <?php echo $this->element('turma_form', ['config' => ['update' => $update, 'key' => $update->id, 'unidades' => $unidades, 'servicos' => $servicos]]); ?>
        </div>
      </div>
      <div  class="kt-separator scope-4 kt-separator--space-sm"></div>
	  	<?php
	  	
	  }
?>