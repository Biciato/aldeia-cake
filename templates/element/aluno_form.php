<?php extract($config);
$key = ($update) ? $key : 1;
$form_options = ['id' => 'aluno-form-' . $key, 'class' => 'kt-form', 'data-key' => $key, 'enctype' => 'multipart/form-data'];
if($update)
  {
    $form_options['data-update']    = 1;
    $form_options['data-enderecos'] = count($update->pessoa->enderecos);
    $form_options['data-parentes']  = count($update->parentes);
    $form_options['data-telefones'] = count($update->pessoa->telefones_array);
  }
echo $this->Form->create(null, $form_options); 
if($update)
  {
    ?>
    <input type="hidden" name="id" value="<?php echo $update->id; ?>">
    <input type="hidden" name="pessoa-aluno[id]" value="<?php echo $update->pessoa->id; ?>">
    <?php
  }?>
<div class="row accordion scope-0 inner-accordion" data-form="<?php echo $key; ?>" id="dados-pessoais-<?php echo $key; ?>" parent-id="">
	<div class="col-sm-12">
		<h4>
			Dados Pessoais
		</h4>
	</div>	
</div>
<div class="kt-separator scope-0 kt-separator--space-sm"></div>
<div class="row form-group" style="display:none" data-parent-id="dados-pessoais-<?php echo $key; ?>">
	<div class="col-sm-3">
	   <label for="pessoa-aluno[nome]">Nome</label>
	   <input class="form-control" type="text" name="pessoa-aluno[nome]" value="<?php echo @$update->pessoa->nome; ?>">
	   <div class="form-text"></div>
	</div>
	<div class="col-sm-2">
	   <label for="data_matricula">Data de matrícula</label>
	   <input class="form-control" type="text" name="data_matricula" value="<?php echo @$update->data_matricula_formatada; ?>">
	   <div class="form-text"></div>
	</div>
	<div class="col-sm-2">
	   <label for="data_inicio">Data de início</label>
	   <input class="form-control" type="text" name="data_inicio" value="<?php echo @$update->data_inicio_formatada; ?>">
	   <div class="form-text"></div>
	</div>
  <div class="col-sm-2">
    <label for="pessoa-aluno[cpf]">CPF</label>
     <input class="form-control" type="text" name="pessoa-aluno[cpf]" value="<?php echo @$update->pessoa->cpf; ?>">
     <div class="form-text"></div>
  </div>
	<div class="col-sm-3">
	   <label for="pessoa-aluno[sexo]">Sexo</label>
	   <div class="kt-radio-inline">
	      <label class="kt-radio kt-radio--solid">
	      <input type="radio" <?php echo (@$update->pessoa->sexo == 0) ? "checked=\"checked\"" : ""; ?> name="pessoa-aluno[sexo]" value="0"> Masculino
	      <span></span>
	      </label>
	      <label class="kt-radio kt-radio--solid" >
	      <input type="radio" <?php echo (@$update->pessoa->sexo == 1) ? "checked=\"checked\"" : ""; ?> name="pessoa-aluno[sexo]" value="1"> Feminino
	      <span></span>
	      </label>
	   </div>
	   <div class="form-text"></div>
	</div>
</div>
<div class="row form-group" style="display:none" data-parent-id="dados-pessoais-<?php echo $key; ?>">
	<div class="col-sm-2">
		<label for="pessoa-aluno[data_nascimento]">Data de nascimento</label>
		<input class="form-control" type="text" name="pessoa-aluno[data_nascimento]" id="birthdate-<?php echo $key; ?>" value="<?php echo @$update->pessoa->data_nascimento_formatada; ?>">
		<div class="form-text"></div>
	</div>
  <div class="col-sm-2">
    <label>Idade atual</label>
    <input class="form-control disabled" type="text" name="" disabled="disabled" id="idade-atual-<?php echo $key; ?>">
    <div class="form-text"></div>
  </div>
	<div class="col-sm-2">
		<label>Idade em <?php $dt_this_year = new \DateTime(date('Y') . '-03-31'); echo $dt_this_year->format('d/m/Y');  ?></label>
		<input class="form-control disabled" type="text" name="" disabled="disabled" id="idade-esse-ano-<?php echo $key; ?>">
		<div class="form-text"></div>
	</div>
	<div class="col-sm-2">
		<label>Idade em <?php $dt_next_year = new \DateTime(date('Y') . '-03-31'); $dt_next_year->modify("+1 year"); echo $dt_next_year->format('d/m/Y');  ?></label>
		<input class="form-control disabled" type="text" name="" disabled="disabled" id="idade-ano-que-vem-<?php echo $key; ?>">
		<div class="form-text"></div>
	</div>
	<div class="col-sm-2">
		<label for="pessoa-aluno[nacionalidade]">Nacionalidade</label>
		<select class="form-control" name="pessoa-aluno[nacionalidade]">
			<option value="">Selecione...</option>
      <?php foreach($nacionalidades as $id => $label)
        {
          ?>
            <option <?php echo (@$update->pessoa->nacionalidade == $id) ? 'selected="selected"' : ''; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
          <?php
        } ?>
		</select>
	</div>
	<div class="col-sm-2">
		<label for="pessoa-aluno[naturalidade]">Naturalidade</label>
		<select class="form-control" name="pessoa-aluno[naturalidade]">
			<option value="">Selecione...</option>
      <?php foreach($naturalidades as $id => $label)
        {
          ?>
            <option <?php echo (@$update->pessoa->naturalidade == $id) ? 'selected="selected"' : ''; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
          <?php
        } ?>
		</select>
	</div>
</div>
<div class="row accordion scope-0 inner-accordion" data-form="<?php echo $key; ?>" id="atendimento-<?php echo $key; ?>" parent-id="">
  <div class="col-sm-12">
    <h4>
      Atendimento
    </h4>
  </div>  
</div>
<div class="kt-separator scope-0 kt-separator--space-sm"></div>
<div class="row form-group" style="display:none" data-parent-id="atendimento-<?php echo $key; ?>">
    <div class="col-sm-12">
      <label>Ano letivo</label>
      <select class="form-control" name="ano_letivo">
        <option value="">Selecione...</option>
        <?php $dt = new \DateTime();
          $anos = [$dt->format('Y')];
          $dt = $dt->modify('+1 year');
          $anos[] = $dt->format('Y');
          foreach($anos as $ano)
            {
              ?>
                <option value="<?php echo $ano; ?>" <?php echo (@$update->ano_letivo == $ano) ? 'selected="selected"' : "";  ?>><?php echo $ano; ?></option>
              <?php
            } 
        ?>
      </select>
      <div class="form-text"></div>
    </div>
</div>
<div class="row form-group" style="display:none" data-parent-id="atendimento-<?php echo $key; ?>">
    <div class="col-sm-12">
       <label for="unidade">Unidade</label>
       <div class="kt-radio-inline">
          <?php foreach($unidades as $id => $label)
            {
              ?>
              <label class="kt-radio kt-radio--<?php echo (!$update) ? "disabled" : "solid" ?>">
              <input <?php echo (@$update->unidade == $id) ? 'checked="checked"' : ""; ?> type="radio" <?php echo (!$update) ? 'class="disabled" disabled="disabled"' : ''; ?> name="unidade" value="<?php echo $id; ?>"> <?php echo $label; ?>
              <span></span>
              </label>
              <?php
            }
          ?>
       </div>
       <div class="form-text"></div>
    </div>
</div>
<div class="row form-group" style="display:none" data-parent-id="atendimento-<?php echo $key; ?>">
  <div class="col-sm-12">
    <label for="unidade">Matrícula</label>
    <select class="form-control <?php echo (!$update) ? 'disabled' : ''; ?>" <?php echo (!$update) ? 'disabled="disabled"' : ''; ?> name="matricula">
      <option value="">Selecione...</option>
      <?php for ($i=1; $i < 300; $i++) 
        { 
          ?>
          <option <?php echo (@$update->matricula == $i) ? 'selected="selected"' : ''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
          <?php
        }
       ?>
    </select>
    <div class="form-text"></div>
  </div>
</div>
<div class="row form-group" style="display:none" data-parent-id="atendimento-<?php echo $key; ?>">
  <div class="col-sm-4">
    <label for="curso">
      Curso
    </label>
    <select class="form-control <?php echo (!$update) ? 'disabled' : ''; ?>" <?php echo (!$update) ? 'disabled="disabled"' : ''; ?> name="curso">
      <option value="">Selecione...</option>
      <?php if($update)
        {
          foreach($cursos as $id => $label) 
            {
              ?>
              <option <?php echo (@$update->curso == $id) ? 'selected="selected"' : ""; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
              <?php
            }
        }
      ?>
    </select>
    <div class="form-text"></div>
  </div>
  <div class="col-sm-4">
    <label for="agrupamento">
      Agrupamento
    </label>
    <select class="form-control <?php echo (!$update) ? 'disabled' : ''; ?>" <?php echo (!$update) ? 'disabled="disabled"' : ''; ?> name="agrupamento">
      <option value="">Selecione...</option>
      <?php if($update)
        {
          foreach($agrupamentos as $id => $label) 
            {
              ?>
              <option <?php echo (@$update->agrupamento == $id) ? 'selected="selected"' : ""; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
              <?php
            }
        }
      ?>
    </select>
    <div class="form-text"></div>
  </div>  
  <div class="col-sm-4">
    <label for="nivel">
      Nível
    </label>
    <select class="form-control <?php echo (!$update) ? 'disabled' : ''; ?>" <?php echo (!$update) ? 'disabled="disabled"' : ''; ?> name="nivel">
      <option value="">Selecione...</option>
       <?php if($update)
        {
          foreach($niveis as $id => $label) 
            {
              ?>
              <option <?php echo (@$update->nivel == $id) ? 'selected="selected"' : ""; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
              <?php
            }
        }
      ?>
    </select>
    <div class="form-text"></div>
  </div>    
</div>
<div class="row form-group campos-atendimento-<?php echo $key; ?>" style="display:none" data-parent-id="atendimento-<?php echo $key; ?>">
  <div class="col-sm-4">
    <label for="turno">
      Turno
    </label>
    <select class="form-control <?php echo (!$update) ? 'disabled' : ''; ?>" <?php echo (!$update) ? 'disabled="disabled"' : ''; ?> name="turno">
      <option value="">Selecione...</option>
      <?php foreach($turnos as $id => $entity) 
          {
            ?>
            <option <?php echo (@$update->turno == $entity->id) ? 'selected="selected"' : ""; ?> value="<?php echo $entity->id; ?>"><?php echo $entity->nome; ?></option>
            <?php
          }
      ?>
    </select>
    <div class="form-text"></div>
  </div>
  <div class="col-sm-4">
    <label for="permanencia">
      Permanência
    </label>
    <select class="form-control <?php echo (!$update) ? 'disabled' : ''; ?>" <?php echo (!$update) ? 'disabled="disabled"' : ''; ?> name="permanencia">
      <option value="">Selecione...</option>
       <?php if($update)
        {
          foreach($permanencias as $id => $label) 
            {
              ?>
              <option <?php echo (@$update->permanencia == $id) ? 'selected="selected"' : ""; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
              <?php
            }
        }
      ?>
    </select>
    <div class="form-text"></div>
  </div>  
  <div class="col-sm-4">
    <label for="horario">
      Horário
    </label>
    <select class="form-control <?php echo (!$update) ? 'disabled' : ''; ?>" <?php echo (!$update) ? 'disabled="disabled"' : ''; ?> name="horario">
      <option value="">Selecione...</option>
       <?php if($update)
        {
          foreach($horarios as $id => $label) 
            {
              ?>
              <option <?php echo (@$update->horario == $id) ? 'selected="selected"' : ""; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
              <?php
            }
        }
      ?>
    </select>
    <div class="form-text"></div>
  </div>    
</div>
<?php if($update)
  {
    ?>
    <div class="turmas-<?php echo $key; ?>">
        <input type="hidden" name="turmas"/>
    </div>
    <?php
    foreach($turmas_servico as $turmas)
      {
        ?>
        <div class="row form-group turmas-<?php echo $key; ?>" style="display:none;"  data-parent-id="atendimento-<?php echo $key; ?>">
            <div class="col-sm-12">
                <label for="turmas[]">Turma <?php echo ($turmas['servico']->servico == 3) ? 'Sistema Creche' : $turmas['servico']->ServicoAux->nome;?></label>
                <select class="form-control" name="turmas[<?php echo $turmas['servico']->ServicoAux->id; ?>]" id="turmas-<?php echo $key; ?>-<?php echo $turmas['servico']->id; ?>">
                    <option value="">Selecione...</option>
                    <?php foreach($turmas['turmas'] as $turma)
                      {
                        ?>
                        <option <?php echo (@$update->turmas_array[$turmas['servico']->ServicoAux->id] == $turma->id) ? 'selected="selected"' : ""; ?> value="<?php echo $turma->id; ?>"><?php echo $turma->nome; ?></option>
                        <?php
                      } ?>
                </select>
            </div>
        </div>
    <?php
      }
  } 
?>
<div class="row accordion scope-0 inner-accordion" data-form="<?php echo $key; ?>" id="enderecos-<?php echo $key; ?>" parent-id="">
	<div class="col-sm-12">
		<h4>
			Endereços
		</h4>
	</div>	
</div>
<div class="kt-separator scope-0 kt-separator--space-sm"></div>
<?php if($update)
  {
    foreach($update->pessoa->enderecos as $address_index => $endereco)
      {
        ?>
        <input type="hidden" name="enderecos[<?php echo $address_index ?>][id]" value="<?php echo $endereco->id; ?>">
        <div style="display:none;" data-parent-id="enderecos-<?php echo $key; ?>" class="form-group row address-fields-<?php echo $address_index; ?>-<?php echo $key; ?>" >
          <div class="col-sm-4">
            <label for="cep">CEP</label>
            <input type="text" value="<?php echo $endereco->cep; ?>" data-key="<?php echo $address_index; ?>" data-selector=".address-fields-<?php echo $address_index; ?>-<?php echo $key; ?>" id="cep-<?php echo $address_index; ?>-<?php echo $key; ?>" class="form-control" name="enderecos[<?php echo $address_index; ?>][cep]">
            <div class="form-text"></div>
          </div>
          <div class="col-sm-8">
            <label for="cep">Logradouro</label>
            <input type="text" value="<?php echo $endereco->logradouro; ?>" class="form-control" name="enderecos[<?php echo $address_index; ?>][logradouro]">
            <div class="form-text"></div>
          </div>
        </div>
        <div style="display:none;" data-parent-id="enderecos-<?php echo $key; ?>" class="form-group row address-fields-<?php echo $address_index; ?>-<?php echo $key; ?>">
            <div class="col-sm-4">
              <label for="cep">Bairro</label>
              <input type="text" value="<?php echo $endereco->bairro; ?>" class="form-control" name="enderecos[<?php echo $address_index; ?>][bairro]">
              <div class="form-text"></div>
            </div>
            <div class="col-sm-4">
              <label for="cep">Cidade</label>
              <input type="text" value="<?php echo $endereco->cidade; ?>" class="form-control" name="enderecos[<?php echo $address_index; ?>][cidade]">
              <div class="form-text"></div>
            </div>
            <div class="col-sm-4">
              <label for="cep">Estado</label>
              <input type="text" value="<?php echo $endereco->estado; ?>" class="form-control" name="enderecos[<?php echo $address_index; ?>][estado]">
              <div class="form-text"></div>
            </div>
        </div>
        <div style="display:none;" data-parent-id="enderecos-<?php echo $key; ?>" class="form-group row address-fields-<?php echo $address_index; ?>-<?php echo $key; ?>">
            <div class="col-sm-2">
              <label for="cep">Número</label>
              <input type="text" value="<?php echo $endereco->numero; ?>" class="form-control" name="enderecos[<?php echo $address_index; ?>][numero]">
              <div class="form-text"></div>
            </div>
            <div class="col-sm-10">
              <label for="cep">Complemento</label>
              <input type="text" value="<?php echo $endereco->complemento; ?>" class="form-control" name="enderecos[<?php echo $address_index; ?>][complemento]">
              <div class="form-text"></div>
            </div>
        </div>
        <div style="display:none;" data-parent-id="enderecos-<?php echo $key; ?>" class="form-group row address-fields-<?php echo $address_index; ?>-<?php echo $key; ?>">
        <div class="col-sm-12">
          <label for="cep">&nbsp;</label>
          <a class="btn btn-danger remover-endereco-<?php echo $key; ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $address_index; ?>">Remover endereço</a>
        </div> 
        </div>
        <div  style="display:none;" data-parent-id="enderecos-<?php echo $key; ?>" style="margin-bottom:20px" class="kt-separator kt-separator--space-sm  address-fields-<?php echo $address_index; ?>-<?php echo $key; ?>"> 
        </div>
        <?php
      }
  } 
?>
<div class="row form-group" id="addresses-button-<?php echo $key; ?>" style="display:none" data-parent-id="enderecos-<?php echo $key; ?>">
   <div class="col-sm-12">
     <a href="javascript:void(0)" class="btn btn-success" id="adicionar-endereco-<?php echo $key; ?>">Adicionar endereço</a>
   </div>
</div>
<div class="row accordion scope-0 inner-accordion" data-form="<?php echo $key; ?>" id="parentes-<?php echo $key; ?>" parent-id="">
	<div class="col-sm-12">
		<h4>
			Parentes
		</h4>
	</div>	
</div>
<div class="kt-separator scope-0 kt-separator--space-sm"></div>
<?php if($update)
  {
    foreach($update->parentes as $parent_index => $parente)
      {
        ?>
          <input type="hidden" name="parentes[<?php echo $parent_index; ?>][id]" value="<?php echo $parente->id; ?>">
          <input type="hidden" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][id]" value="<?php echo $parente->pessoa->id; ?>">
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
            <div class="col-sm-4">
              <label>Parentesco</label>
              <select class="form-control" name="parentes[<?php echo $parent_index; ?>][parentesco]">
                <option value="">Selecione...</option>
                <?php 
                  foreach($parentescos as $id => $label)
                    {
                      ?>
                      '<option <?php echo ($id == $parente->parentesco) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
                      <?php
                    }
                ?>
              </select>
              <div class="form-text"></div>
            </div>
            <div class="col-sm-4">
              <label>Nome</label>
              <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][nome]" value="<?php echo $parente->pessoa->nome; ?>">
              <div class="form-text"></div>
            </div>                      
            <div class="col-sm-4">
               <label for="parentes[<?php echo $parent_index; ?>][pessoa-parente][sexo]">Sexo</label>
               <div class="kt-radio-inline">
                  <label class="kt-radio kt-radio--solid">
                  <input <?php echo ($parente->pessoa->sexo == 0) ? 'checked="checked"' : ""; ?> type="radio" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][sexo]" value="0"> Masculino
                  <span></span>
                  </label>
                  <label class="kt-radio kt-radio--solid" >
                  <input <?php echo ($parente->pessoa->sexo == 1) ? 'checked="checked"' : ""; ?> type="radio" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][sexo]" value="1"> Feminino
                  <span></span>
                  </label>
               </div>
               <div class="form-text"></div>
            </div>
          </div>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
            <div class="col-sm-4">
              <label>CPF</label>
              <input type="text" id="cpf-<?php echo $parent_index; ?>-<?php echo $key; ?>" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][cpf]" value="<?php echo $parente->pessoa->cpf; ?>">
              <div class="form-text"></div>
            </div>
            <div class="col-sm-4">
              <label>RG</label>
              <input type="text" id="rg-<?php echo $parent_index; ?>-<?php echo $key; ?>" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][rg]" value="<?php echo $parente->pessoa->rg; ?>">
              <div class="form-text"></div>
            </div>
            <div class="col-sm-4">
              <label>Órgão expeditor</label>
              <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][orgao_expeditor]" value="<?php echo $parente->pessoa->orgao_expeditor; ?>">
              <div class="form-text"></div>
            </div>
          </div>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
            <div class="col-sm-3">
              <label>Email</label>
              <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][email]" value="<?php echo $parente->pessoa->email; ?>">
              <div class="form-text"></div>
            </div>
            <div class="col-sm-3">
              <label>Data de nascimento</label>
              <input type="text" id="data-nascimento-parente-<?php echo $parent_index; ?>-<?php echo $key; ?>" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][data_nascimento]" value="<?php echo $parente->pessoa->data_nascimento_formatada; ?>">
              <div class="form-text"></div>
            </div>
            <div class="col-sm-3">
              <label>Nacionalidade</label>
              <select class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][nacionalidade]">
                <option value="">Selecione...</option>
                <?php foreach($nacionalidades as $id => $label)
                  {
                    ?>
                    <option <?php echo ($parente->pessoa->nacionalidade == $id) ? 'selected="selected"' : ""; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
                    <?php
                  } 
                ?>
              </select>
              <div class="form-text"></div>
            </div>
            <div class="col-sm-3">
              <label>Naturalidade</label>
              <select class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][naturalidade]">
                <option value="">Selecione...</option>
                <?php foreach($naturalidades as $id => $label)
                  {
                    ?>
                    <option <?php echo ($parente->pessoa->naturalidade == $id) ? 'selected="selected"' : ""; ?> value="<?php echo $id; ?>"><?php echo $label; ?></option>
                    <?php
                  } 
                ?>
              </select>
              <div class="form-text"></div>
            </div>
          </div>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="row accordion scope-0 inner-accordion parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>" data-form="<?php echo $key; ?>" id="atribuicoes-parente-<?php echo $parent_index; ?>-<?php echo $key; ?>">
            <div class="col-sm-12">
              <h4>
                Atribuições
              </h4>
            </div>  
          </div> 
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" style="margin-bottom:20px" class="kt-separator kt-separator--space-sm  parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>"> 
          </div>
          <div style="display:none" data-grandparent-id="parentes-<?php echo $key; ?>" data-parent-id="atribuicoes-parente-<?php echo $parent_index; ?>-<?php echo $key; ?>" class="row form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>" >
            <div class="col-sm-12">
              <div class="kt-radio-inline" data-name="responsavel_legal">
                  <label class="kt-radio kt-radio--bold">
                    <input name="responsavel_legal"  type="radio" value="<?php echo $parent_index; ?>" <?php echo ($update->responsavel_id == $parente->id) ? 'checked="checked"' : ''; ?>>  Responsável legal
                        <span></span>
                  </label>
              </div>
            </div>
          </div> 
          <div style="display:none" data-grandparent-id="parentes-<?php echo $key; ?>" data-parent-id="atribuicoes-parente-<?php echo $parent_index; ?>-<?php echo $key; ?>" class="row form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>" >
            <div class="col-sm-12">
              <input type="hidden" name="parentes[<?php echo $parent_index; ?>][atribuicoes]" value="[]">
              <div class="kt-checkbox-list" data-name="parentes[<?php echo $parent_index; ?>][atribuicoes]">
                  <label class="kt-checkbox kt-checkbox--bold">
                    <input name="parentes[<?php echo $parent_index; ?>][atribuicoes][]"  type="checkbox" <?php echo (in_array(0, $parente->atribuicoes_array)) ? 'checked="checked"' : ''; ?> value="0">  Contactar em caso de emergência
                        <span></span>
                  </label>
                  <label class="kt-checkbox kt-checkbox--bold">
                    <input name="parentes[<?php echo $parent_index; ?>][atribuicoes][]"  type="checkbox" <?php echo (in_array(1, $parente->atribuicoes_array)) ? 'checked="checked"' : ''; ?> value="1">  Autorização de saída do aluno
                        <span></span>
                  </label>
                  <label class="kt-checkbox kt-checkbox--bold">
                    <input name="parentes[<?php echo $parent_index; ?>][atribuicoes][]"  type="checkbox" <?php echo (in_array(2, $parente->atribuicoes_array)) ? 'checked="checked"' : ''; ?> value="2"> Receber Circular
                        <span></span>
                  </label>
                  <label class="kt-checkbox kt-checkbox--bold">
                    <input name="parentes[<?php echo $parent_index; ?>][atribuicoes][]"  type="checkbox" <?php echo (in_array(3, $parente->atribuicoes_array)) ? 'checked="checked"' : ''; ?> value="3">  Acesso ao Financeiro
                        <span></span>
                  </label>
              </div>
            </div>
          </div> 
         
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
            <div class="col-sm-12">
              <label>Telefones</label>
              <input type="hidden" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][telefones]" />
            </div>
          </div>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row" id="parents-tel-button-<?php echo $parent_index; ?>-<?php echo $key; ?>">
            <div class="col-sm-12">
              <label>&nbsp;</label>
              <a href="javascript:void(0)" class="btn btn-success btn-icon adicionar-telefone-<?php echo $key; ?>" data-parent="<?php echo $parent_index; ?>">
                <i class="fa fa-plus"></i> 
              </a>
            </div>
          </div>
          <?php foreach($parente->pessoa->telefones_array as $telefone)
            {
              $phone_unique = uniqid();
              ?>
              <div style="display:none;" data-parent-id="parentes-<?php echo $key ?>" class="form-group row parent-fields-<?php echo $parent_index; ?>-<?php echo $key ?> phone-field-<?php echo $phone_unique; ?>-<?php echo $key ?>">
                <div class="col-sm-11">
                    <label>Telefone</label>
                    <input class="form-control" id="phone-<?php echo $phone_unique; ?>-<?php echo $key ?>" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][telefones][]" type="text" value="<?php echo $telefone; ?>"/>
                </div>
                <div class="col-sm-1">
                    <label>&nbsp;</label>
                    <a class="btn btn-danger btn-block remover-telefone-<?php echo $key ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $phone_unique; ?>-<?php echo $key ?>">Remover</a>
                </div>
              </div>
              <?php
            }
          ?>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
            <div class="col-sm-12">
              <div class="kt-checkbox-inline" data-name="mesmo_endereco">
                  <input type="hidden" name="parentes[<?php echo $parent_index; ?>][endereco][mesmo_endereco]" value="0"/>
                  <label class="kt-checkbox kt-checkbox--bold">
                    <input name="parentes[<?php echo $parent_index; ?>][endereco][mesmo_endereco]" data-key="<?php echo $parent_index; ?>" <?php echo (!$parente->pessoa->enderecos) ? 'checked="checked"' : ''; ?> class="mesmo-endereco-<?php echo $key; ?>"  type="checkbox" value="1">  Mesmo endereço do aluno
                        <span></span>
                  </label>
              </div>
            </div>
          </div>
          <?php $endereco = (count($parente->pessoa->enderecos) > 0) ? $parente->pessoa->enderecos[0] : false; ?>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" <?php echo (!$endereco) ? 'style="display:none;"' : ''; ?> data-address-fields="true" class="form-group parent-address-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>" >
            <div class="col-sm-4">
              <label for="cep">CEP</label>
              <input type="text" data-key="<?php echo $parent_index; ?>" data-selector=".parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>" id="cep-parente-<?php echo $parent_index; ?>-<?php echo $key; ?>" class="form-control" name="parentes[<?php echo $parent_index; ?>][endereco][cep]" value="<?php echo @$endereco->cep; ?>">
              <div class="form-text"></div>
            </div>
            <div class="col-sm-8">
              <label for="cep">Logradouro</label>
              <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][endereco][logradouro]" value="<?php echo @$endereco->logradouro; ?>">
              <div class="form-text"></div>
            </div>
          </div>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" style="display:none;" data-address-fields="true" class="form-group parent-address-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>">
              <div class="col-sm-4">
                <label for="cep">Bairro</label>
                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][endereco][bairro]" value="<?php echo @$endereco->bairro; ?>">
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="cep">Cidade</label>
                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][endereco][cidade]" value="<?php echo @$endereco->cidade; ?>">
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="cep">Estado</label>
                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][endereco][estado]" value="<?php echo @$endereco->estado; ?>">
                <div class="form-text"></div>
              </div>
          </div>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" style="display:none;" data-address-fields="true" class="form-group parent-address-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>">
              <div class="col-sm-2">
                <label for="cep">Número</label>
                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][endereco][numero]" value="<?php echo @$endereco->numero; ?>">
                <div class="form-text"></div>
              </div>
              <div class="col-sm-10">
                <label for="cep">Complemento</label>
                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][endereco][complemento]" value="<?php echo @$endereco->complemento; ?>">
                <div class="form-text"></div>
              </div>
          </div>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
              <div class="col-sm-8">
                <label>Empresa que trabalha</label>
                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][empresa]" value="<?php echo $parente->pessoa->empresa; ?>">
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label>Ocupação</label>
                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][ocupacao]" value="<?php echo $parente->pessoa->ocupacao; ?>">
                <div class="form-text"></div>
              </div> 
          </div>
          <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" class="form-group row parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>" id="remove-parent-button-<?php echo $parent_index; ?>-<?php echo $key; ?>"> 
            <div class="col-sm-12">
              <label for="cep">&nbsp;</label>
              <a class="btn btn-danger remover-parente-<?php echo $key; ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $parent_index; ?>">Remover parente</a>
            </div>
          </div> 
            <div style="display:none;" data-parent-id="parentes-<?php echo $key; ?>" style="margin-bottom:20px" class="kt-separator kt-separator--space-sm  parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>"> 
          </div>    
        <?php
      }
  } 
?>
<div class="row form-group" id="parents-button-<?php echo $key; ?>" style="display:none" data-parent-id="parentes-<?php echo $key; ?>">
   <div class="col-sm-12">
     <a href="javascript:void(0)" class="btn btn-success" id="adicionar-parente-<?php echo $key; ?>">Adicionar parente</a>
   </div>
</div>
<?php 
  if($update)
    {
      ?>
        <div class="row accordion scope-0 inner-accordion" data-form="<?php echo $key; ?>" id="ocorrencias-<?php echo $key; ?>" parent-id="">
          <div class="col-sm-12">
            <h4>
              Ocorrências
            </h4>
          </div>	
        </div>
        <?php 
         foreach($update->ocorrencias as $ocorrencia)
          {
            echo $this->element('ocorrencia_individual', ['ocorrencia' => $ocorrencia, 'scope' => 2, 'parent_id' => 'ocorrencias-' . $key, 'marcar_visto' => false, 'usuario_id' => null]);
          }
    }
?>
<div class="kt-separator scope-0 kt-separator--space-sm"></div>
<div class="row accordion scope-0 inner-accordion" data-form="<?php echo $key; ?>" id="servicos-<?php echo $key; ?>" parent-id="">
  <div class="col-sm-12">
    <h4>
      Serviços
    </h4>
  </div>  
</div>
<div class="kt-separator scope-0 kt-separator--space-sm"></div>
<?php if($update) 
  {
    ?>
    <div class="row" style="display:none;" data-parent-id="servicos-<?php echo $key; ?>">
    <input type="hidden" name="servicos" value="[]">
      <div class="kt-checkbox-list servicos-checkbox-list">
        <?php
        foreach($servicos_atribuidos as $servico)
          {
            ?>
                <label class="kt-checkbox kt-checkbox--<?php echo (($servico->obrigatorio)||($servico->sistema_creche)) ? "disabled" : "solid"; ?>">
                  <input <?php echo ((($servico->obrigatorio)||($servico->sistema_creche))||(in_array((string)$servico->id, $update->servicos_array))) ? 'checked="checked"' : ''; ?> type="checkbox" name="servicos[]" value="<?php echo $servico->id; ?>"  <?php echo (($servico->obrigatorio)||($servico->sistema_creche)) ? 'disabled="disabled"' : ''; ?>> <?php echo $servico->ServicoAux->nome; ?> <span></span>
                </label>
            <?php
          }
        ?>
        </div>
      </div>
      <?php
  }
?>
<div class="row accordion scope-0 inner-accordion" data-form="<?php echo $key; ?>" id="financeiro-<?php echo $key; ?>" parent-id="">
  <div class="col-sm-12">
    <h4>
      Financeiro
    </h4>
  </div>  
</div>
<div class="kt-separator scope-0 kt-separator--space-sm"></div>
<?php if($update) 
  {
    ?>
      <div style="display:none; margin:20px 4% 0 4%; " data-form="<?php echo $key; ?>" data-parent-id="financeiro-<?php echo $key; ?>" class="row accordion boleto-row scope-0 inner-accordion"  id="boletos-<?php echo $key; ?>">
        <div class="col-sm-12">
          <h4>
            Boletos
          </h4>
        </div>  
      </div>
      <div data-parent-id="financeiro-<?php echo $key; ?>" style="margin:0 4% 20px 4%; display:none;"  class="kt-separator kt-separator--space-sm"> 
          </div>      
      <div style="display:none;" data-grandparent-id="financeiro-<?php echo $key; ?>" data-parent-id="boletos-<?php echo $key; ?>" class="row boleto-row">
        <div class="col-sm-2">
          Vencimento
        </div>
        <div class="col-sm-4">
          Tipo
        </div>
        <div class="col-sm-2">
          Valor
        </div>
        <div class="col-sm-2">
          R$ Pago
        </div>
        <div class="col-sm-2">
          Data Pago
        </div>
      </div>
      <?php 
        foreach($update->pessoa->boletos as $boleto)
          {
            ?>
              <a style="display:none;" target="_blank" data-grandparent-id="financeiro-<?php echo $key; ?>" data-parent-id="boletos-<?php echo $key; ?>" class="row boleto-row transition-24" href="/financeiro/boleto/<?php echo $boleto->id; ?>">
                  <div class="col-sm-2">
                    <?php echo $boleto->data_vencimento_formatada; ?>
                  </div>
                  <div class="col-sm-4">
                    <?php echo $boleto->motivo_boleto; ?>
                  </div>
                  <div class="col-sm-2">
                    <?php echo $boleto->valor_formatado; ?>
                  </div>
                  <div class="col-sm-2">
                    <?php echo $boleto->valor_pago_sacado; ?>
                  </div>
                  <div class="col-sm-2">
                    <?php echo $boleto->data_pagamento; ?>
                  </div> 
              </a>
            <?php
          }
      ?>


      <div class="row" style="display:none; margin-top: 40px;" data-parent-id="financeiro-<?php echo $key; ?>">
      <input type="hidden" name="financeiro" value="">
      <?php $total = 0; 
      foreach ($servicos_atribuidos_financeiro as $servico)
        {
          $desconto = (@$update->financeiro_array[$servico->id]) ? $update->financeiro_array[$servico->id] : "";
          $valor_novo = (int)$servico->valor_atual->valor;
          if($desconto)
            {
              $valor_desconto = ceil((($valor_novo*(int)$desconto)/100));
              $valor_novo     = ($valor_novo - $valor_desconto);
            }
          $total   += $valor_novo;
        ?>
        <div class="linha-financeiro">
              <?php echo $servico->ServicoAux->nome; ?>
              <div class="numeros-financeiro">
                <input data-valor="<?php echo $valor_novo; ?>" data-valor-original="<?php echo $servico->valor_atual->valor; ?>" type="text" value="<?php echo $desconto; ?>%" type="tel" name="financeiro[<?php echo $servico->id; ?>]"  class="campo-desconto" />
                <div class="valor-financeiro">
                  R$ <?php echo number_format(($valor_novo/100), 2, ",", "."); ?>
                </div>
              </div>
          </div>
        <?php
        }
      ?>
      <div class="linha-financeiro">
            Total
            <div class="numeros-financeiro">
              <div class="valor-financeiro" id="total-financeiro-<?php echo $key; ?>">
                R$ <?php echo number_format(($total/100), 2, ',', '.'); ?>
              </div>
            </div>
        </div>
      </div>
      <div class="row form-group" style="display:none; margin-top: 20px; border:none" data-parent-id="financeiro-<?php echo $key; ?>">
        <div class="col-sm-12">
          <label for="cep">Dia de vencimento dos boletos</label>
          <input type="text" class="form-control" name="dia_vencimento" value="<?php echo @$update->dia_vencimento; ?>">
          <div class="form-text"></div>
        </div>
      </div>
      <?php
  } 
if($update)
  {
    ?>
    <div class="row" style="margin-top:20px">
      <div class="col-sm-12" style="text-align:right; justify-content: flex-end">
          <button class="btn btn-success" style="margin-bottom: 25px;"  id="inserir-aluno-<?php echo $update->id; ?>">Salvar</button>
      </div>
    </div>
    <?php
  }
echo $this->Form->end();
  if(!$update)
    {
      $recursive = ['cursos' => [], 'turnos' => []];
      foreach($cursos as $curso)
        {
          $agrupamentos = [];
          foreach($curso->agrupamentos_entities as $agrupamento)
            {
              $niveis = [];
              foreach($agrupamento->niveis_entities as $nivel)
                {
                  $niveis[$nivel->id] = $nivel->nome;
                }
              $agrupamentos[$agrupamento->id] = 
                [
                  'nome' => $agrupamento->nome,
                  'niveis' => $niveis
                ];
            }
          $recursive['cursos'][$curso->id] = ['agrupamentos' => $agrupamentos];
        }
      foreach($turnos as $turno)
        {
          $permanencias = [];
          foreach($turno->permanencias_entities as $permanencia)
            {
              $horarios = [];
              foreach($permanencia->horarios_entities as $horario)
                {
                  $horarios[$horario->id] = $horario->nome;
                }
              $permanencias[$permanencia->id] =
                [
                  'nome' => $permanencia->nome,
                  'horarios' => $horarios
                ];
            }
          $recursive['turnos'][$turno->id] = ['permanencias' => $permanencias];
        }
      $this->append('script');
      ?>
      <script type="text/javascript">
         var number_format = function(number, decimals, dec_point, thousands_point) 
           {
            if (number == null || !isFinite(number)) {
                throw new TypeError("number is not valid");
            }
            if (!decimals) {
                var len = number.toString().split('.').length;
                decimals = len > 1 ? len : 0;
            }
            if (!dec_point) {
                dec_point = '.';
            }
            if (!thousands_point) {
                thousands_point = ',';
            }
            number = parseFloat(number).toFixed(decimals);
            number = number.replace(".", dec_point);
            var splitNum = number.split(dec_point);
            splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
            number = splitNum.join(dec_point);

            return number;
          }
        var dados = <?php echo json_encode($recursive); ?>;
        var options_parentescos = '<option value="">Selecione...</option>' +
        <?php 
          foreach($parentescos as $id => $label)
            {
              ?>
              '<option value="<?php echo $id; ?>"><?php echo $label; ?></option>' +
              <?php
            }
        ?>
        '';
      var options_nacionalidades = '<option value="">Selecione...</option>' +
        <?php 
          foreach($nacionalidades as $id => $label)
            {
              ?>
              '<option value="<?php echo $id; ?>"><?php echo $label; ?></option>' +
              <?php
            }
        ?>
        '';
      var options_naturalidades = '<option value="">Selecione...</option>' +
        <?php 
          foreach($naturalidades as $id => $label)
            {
              ?>
              '<option value="<?php echo $id; ?>"><?php echo $label; ?></option>' +
              <?php
            }
        ?>
        '';
      var datas_corte =
        {
          esse_ano : function()
            {
              var dt = new Date('<?php echo $dt_this_year->format('Y-m-d'); ?>');
              return dt;
            },
          ano_que_vem : function()
            {
              var dt = new Date('<?php echo $dt_next_year->format('Y-m-d'); ?>');
              return dt;
            }
        }
      </script>
      <?php 
      echo $this->Html->script('vanilla-masker');
      echo $this->Html->script('datepicker-pt-br');
      echo $this->Html->script('class-formulario-aluno'); 
      echo $this->Html->script('cropper'); 
      echo $this->Html->script('jquery-cropper'); 
      echo $this->Html->script('novo-aluno'); 
      $this->end();
      $this->append('css');
      echo $this->Html->css('cropper'); 
      $this->end();
    }
 ?>