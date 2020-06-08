<?php extract($config);
$key = ($update) ? $key : 1;
$form_options = ['id' => 'colaborador-form-' . $key, 'class' => 'kt-form', 'data-key' => $key, 'enctype' => 'multipart/form-data', 'url' => false];
if($update)
  {
    $form_options['data-update'] = 1;
    $form_options['data-enderecos'] = count($update->pessoa->enderecos);
    $form_options['data-telefones'] = count($update->pessoa->telefones_array);
  } ?>

<input type="file" class="sr-only" style="display:none" id="inputImage-<?php echo $key; ?>" name="avatar" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
<?php echo $this->Form->create(null, $form_options); ?>  
            <?php 
              if($update)
                {
                  ?>
                    <input type="hidden" name="id" value="<?php echo $update->id; ?>">
                    <input type="hidden" name="pessoa-colaborador[id]" value="<?php echo $update->pessoa->id; ?>">
                    <input type="hidden" name="login[id]" value="<?php echo $update->pessoa->login->id; ?>">
                    <textarea name="" class="old-children-data kt-hidden"><?php echo ($update->dados_filhos) ? $update->dados_filhos : "[]"; ?></textarea>
                  <?php
                }
            ?>
            <div class="form-group row">
               <div class="col-sm-9">
                  <label for="pessoa-colaborador[nome]">Nome</label>
                  <input class="form-control" type="text" name="pessoa-colaborador[nome]" value="<?php echo @$update->pessoa->nome; ?>">
                  <div class="form-text"></div>
               </div>
               <div class="col-sm-3">
                  <label for="pessoa-colaborador[sexo]">Sexo</label>
                  <div class="kt-radio-inline">
                     <label class="kt-radio kt-radio--solid">
                     <input type="radio" <?php echo (@$update->pessoa->sexo == 0) ? "checked=\"checked\"" : ""; ?> name="pessoa-colaborador[sexo]" value="0"> Masculino
                     <span></span>
                     </label>
                     <label class="kt-radio kt-radio--solid" <?php echo (@$update->pessoa->sexo == 1) ? "checked=\"checked\"" : ""; ?>>
                     <input type="radio" name="pessoa-colaborador[sexo]" value="1"> Feminino
                     <span></span>
                     </label>
                  </div>
                  <div class="form-text"></div>
               </div>
            </div>
            <div class="form-group row">
               <div class="col-sm-4">
                  <label for="pessoa-colaborador[apelido]">Apelido ou nome de tratamento</label>
                  <input class="form-control" type="text" name="pessoa-colaborador[apelido]" value="<?php echo @$update->pessoa->apelido; ?>">
                  <div class="form-text"></div>
               </div>
               <div class="col-sm-4">
                  <label for="funcao">Função do colaborador</label>
                  <select class="form-control" name="funcao">
                     <option value="">
                        Selecione...
                     </option>
                     <?php foreach($funcoes as $id => $funcao)
                       {
                        ?>
                          <option <?php echo (@$update->funcao == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $funcao; ?></option>
                        <?php
                       } 
                     ?>
                  </select>
                  <div class="form-text"></div>
               </div>
               <div class="col-sm-4">
                 <label for="unidade">Unidade que trabalha</label>
                   <div class="kt-radio-inline">
                       <?php 
                         $unidades_keys = array_keys($unidades);
                         foreach($unidades as $id => $unidade)
                           {
                             $first = ($id == $unidades_keys[0]);
                             ?>
                               <label class="kt-radio kt-radio--solid">
                                 <input type="radio" <?php echo ((@$update->unidade == $id)||((!$update)&&($first))) ? "checked=\"checked\"" : ""; ?> name="unidade" value="<?php echo $id; ?>"> <?php echo $unidade; ?>
                                 <span></span>
                               </label>
                             <?php
                           }
                       ?>
                   </div>
               </div>
            </div>
            <div class="row accordion scope-0"  data-key="avatar_<?php echo $key; ?>" data-avatar="true" id="avatar_<?php echo $key; ?>">
              <div class="col-sm-12">
                <h4>
                  Imagem de perfil
                </h4>
              </div>  
            </div>
            <div class="kt-separator scope-0 kt-separator--space-sm" style="margin-bottom: 20px;"></div>
            <div class="form-group row" style="display:none;" data-parent-id="avatar_<?php echo $key; ?>">
              <div class="col-sm-12">
                <div style="margin:auto; height:500px; width:500px;">
                  <?php if(@$update->pessoa->caminho_arquivo_avatar)
                    {
                      $img = $this->Url->build(
                        [
                          'controller' => 'colaboradores',
                          'action'     => 'thumb',
                          500,
                          $update->pessoa->id
                        ], ['fullBase' => true]);
                    } 
                  else
                    {
                      $img = 'branco.gif';
                    }
                  echo $this->Html->image($img, ['style' => 'max-width: 100%;', 'id' => 'croppable-' . $key]);
                   ?>
                </div>
              </div>
            </div>
            <div class="form-group row" style="display:none;" data-parent-id="avatar_<?php echo $key; ?>">
              <div class="col-sm-12 text-center" id="cropper-controls-box-<?php echo $key; ?>" style="display:none;">
                <div class="btn-group">
                   <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Aumentar o zoom" data-method="zoom" data-option="0.1">
                      <span data-animation="false" title="">
                        <span class="fa fa-search-plus"></span>
                      </span>
                    </button>
                    <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Reduzir o zoom" data-method="zoom" data-option="-0.1" >
                      <span  data-animation="false" title="">
                          <span class="fa fa-search-minus"></span>
                      </span>
                   </button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Mover imagem para a esquerda"  data-method="move" data-option="-10" data-second-option="0">
                    <span data-animation="false" title="">
                      <span class="fa fa-arrow-left"></span>
                    </span>
                  </button>
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Mover imagem para a direita" data-method="move" data-option="10" data-second-option="0">
                    <span data-animation="false">
                      <span class="fa fa-arrow-right"></span>
                    </span>
                  </button>
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Mover imagem para cima" data-method="move" data-option="0" data-second-option="-10">
                    <span data-animation="false" title="">
                      <span class="fa fa-arrow-up"></span>
                    </span>
                  </button>
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Mover imagem para baixo" data-method="move" data-option="0" data-second-option="10">
                    <span data-animation="false" title="">
                      <span class="fa fa-arrow-down"></span>
                    </span>
                  </button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Girar imagem em sentido anti-horário" data-method="rotate" data-option="-45">
                    <span data-animation="false" title="">
                      <span class="fa fa-rotate-left"></span>
                    </span>
                  </button>
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Girar imagem em sentido horário" data-method="rotate" data-option="45">
                    <span data-animation="false" title="">
                      <span class="fa fa-rotate-right"></span>
                    </span>
                  </button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Refletir imagem na horizontal" data-method="scaleX" data-option="-1">
                    <span data-animation="false">
                      <span class="fa fa-arrows-h"></span>
                    </span>
                  </button>
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Refletir imagem na vertical" data-method="scaleY" data-option="-1">
                    <span data-animation="false">
                      <span class="fa fa-arrows-v"></span>
                    </span>
                  </button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn cropper-upload-<?php echo $key; ?> cropper-controls-<?php echo $key; ?> btn-primary btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Fazer upload de imagem" >
                    <span  data-animation="false">
                      <span class="fa fa-upload"></span>
                    </span>
                  </button>
                  <button type="button" class="btn cropper-controls-<?php echo $key; ?> btn-danger btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Remover imagem" data-method="remove">
                    <span data-animation="false" title="">
                      <span class="fa fa-trash"></span>
                    </span>
                  </button>
                  <button type="button" class="btn  cropper-controls-<?php echo $key; ?> btn-success btn-icon" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Confirmar o corte da imagem" data-method="confirm">
                    <span  data-animation="false">
                      <span class="fa fa-crop"></span>
                    </span>
                  </button>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-3">
                <label for="pessoa-colaborador[data_nascimento]">Data de nascimento</label>
                <input type="text" autocomplete="false" value="<?php echo (@$update->pessoa->data_nascimento_formatada) ? $update->pessoa->data_nascimento_formatada : ""; ?>" name="pessoa-colaborador[data_nascimento]" id="birthdate-<?php echo $key; ?>" class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                 <label for="pessoa-colaborador[nacionalidade]">Nacionalidade</label>
                 <select class="form-control" name="pessoa-colaborador[nacionalidade]">
                    <option value="">
                       Selecione...
                    </option>
                    <?php foreach($nacionalidades as $id => $nacionalidade)
                      {
                       ?>
                         <option <?php echo (@$update->pessoa->nacionalidade == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $nacionalidade; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
                 <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                 <label for="pessoa-colaborador[naturalidade]">Naturalidade</label>
                 <select class="form-control" name="pessoa-colaborador[naturalidade]">
                    <option value="">
                       Selecione...
                    </option>
                    <?php foreach($estados as $id => $naturalidade)
                      {
                       ?>
                         <option <?php echo (@$update->pessoa->naturalidade == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $naturalidade; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
                 <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                 <label for="pessoa-colaborador[cor]">Cor</label>
                 <select class="form-control" name="pessoa-colaborador[cor]">
                    <option value="">
                       Selecione...
                    </option>
                    <?php foreach($cores as $id => $cor)
                      {
                       ?>
                         <option <?php echo (@$update->pessoa->cor == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $cor; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
                 <div class="form-text"></div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-2">
                <label for="pessoa-colaborador[estado_civil]">Estado Civil</label>
                 <select class="form-control" name="pessoa-colaborador[estado_civil]">
                    <option value="">
                       Selecione...
                    </option>
                    <?php foreach($estados_civis as $id => $estado_civil)
                      {
                       ?>
                         <option <?php echo (@$update->pessoa->estado_civil == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $estado_civil; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
              </div>
              <div class="col-sm-3">
                <label for="pessoa-colaborador[cpf]">CPF</label>
                <input type="text" autocomplete="false" value="<?php echo (@$update->pessoa->cpf) ? $update->pessoa->cpf : ""; ?>" name="pessoa-colaborador[cpf]"  class="form-control backend-check-<?php echo $key; ?>" data-field="cpf" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="pessoa-colaborador[rg]">RG</label>
                <input type="text" autocomplete="false" value="<?php echo (@$update->pessoa->rg) ? $update->pessoa->rg : ""; ?>" name="pessoa-colaborador[rg]"  class="form-control backend-check-<?php echo $key; ?>" data-field="rg" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-2">
                <label for="pessoa-colaborador[data_expedicao_rg]">Data de expedição</label>
                <input type="text" autocomplete="false" value="<?php echo (@$update->pessoa->data_expedicao_rg) ? $update->pessoa->data_expedicao_rg : ""; ?>" name="pessoa-colaborador[data_expedicao_rg]"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-2">
                <label for="pessoa-colaborador[orgao_expeditor]">Órgão expeditor</label>
                <input type="text" autocomplete="false" value="<?php echo (@$update->pessoa->orgao_expeditor) ? $update->pessoa->orgao_expeditor : ""; ?>" name="pessoa-colaborador[orgao_expeditor]"  class="form-control" >
                <div class="form-text"></div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-3">
                <label for="assina_contrato">Assina contrato?</label>
                 <select class="form-control" name="assina_contrato">
                    <option value="">
                       Selecione...
                    </option>
                    <?php foreach($boolean_selectbox as $tinyint => $label)
                      {
                       ?>
                         <option <?php echo ((@$update)&&(@$update->assina_contrato == $tinyint)) ? "selected=\"selected\"" : ""; ?> value="<?php echo $tinyint; ?>"><?php echo $label; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
                 <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="contrato_trabalho_ativo">Possui contrato de trabalho ativo?</label>
                 <select class="form-control" name="contrato_trabalho_ativo">
                    <option value="">
                       Selecione...
                    </option>
                    <?php foreach($boolean_selectbox as $tinyint => $label)
                      {
                       ?>
                         <option <?php echo ((@$update)&&(@$update->contrato_trabalho_ativo == $tinyint)) ? "selected=\"selected\"" : ""; ?> value="<?php echo $tinyint; ?>"><?php echo $label; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
                 <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="visao_do_parente">Acessa com visão do parente?</label>
                 <select class="form-control" name="visao_do_parente">
                    <option value="">
                       Selecione...
                    </option>
                    <?php foreach($boolean_selectbox as $tinyint => $label)
                      {
                       ?>
                         <option <?php echo ((@$update)&&(@$update->visao_do_parente == $tinyint)) ? "selected=\"selected\"" : ""; ?> value="<?php echo $tinyint; ?>"><?php echo $label; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
                 <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="altera_vencimento">Altera vencimentos?</label>
                 <select class="form-control" name="altera_vencimento">
                    <option value="">
                       Selecione...
                    </option>
                    <?php foreach($boolean_selectbox as $tinyint => $label)
                      {
                       ?>
                         <option <?php echo ((@$update)&&(@$update->altera_vencimento == $tinyint)) ? "selected=\"selected\"" : ""; ?> value="<?php echo $tinyint; ?>"><?php echo $label; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
                 <div class="form-text"></div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-4">
                <label for="titulo_eleitor">Título de eleitor</label>
                <input type="text"  value="<?php echo (@$update->titulo_eleitor) ? $update->titulo_eleitor : ""; ?>" name="titulo_eleitor"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="secao_titulo_eleitor">Seção</label>
                <input type="text"  value="<?php echo (@$update->secao_titulo_eleitor) ? $update->secao_titulo_eleitor : ""; ?>" name="secao_titulo_eleitor"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="zona_titulo_eleitor">Zona</label>
                <input type="text"  value="<?php echo (@$update->zona_titulo_eleitor) ? $update->zona_titulo_eleitor : ""; ?>" name="zona_titulo_eleitor"  class="form-control" >
                <div class="form-text"></div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-3">
                <label for="carteira_trabalho">Carteira de trabalho</label>
                <input type="text"  value="<?php echo (@$update->carteira_trabalho) ? $update->carteira_trabalho : ""; ?>" name="carteira_trabalho"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="serie_carteira_trabalho">Série</label>
                <input type="text"  value="<?php echo (@$update->serie_carteira_trabalho) ? $update->serie_carteira_trabalho : ""; ?>" name="serie_carteira_trabalho"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="data_expedicao_carteira_trabalho">Data de expedição</label>
                <input type="text"  value="<?php echo (@$update->data_expedicao_carteira_trabalho) ? $update->data_expedicao_carteira_trabalho : ""; ?>" name="data_expedicao_carteira_trabalho"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="pis">PIS</label>
                <input type="text"  value="<?php echo (@$update->pis) ? $update->pis : ""; ?>" name="pis"  class="form-control" >
                <div class="form-text"></div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-4">
                <label for="grau_instrucao">Grau de instrução</label>
                <input type="text"  value="<?php echo (@$update->grau_instrucao) ? $update->grau_instrucao : ""; ?>" name="grau_instrucao"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="matricula">Matrícula</label>
                <input type="text"  value="<?php echo (@$update->matricula) ? $update->matricula : ""; ?>" name="matricula"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="certificado_reservista">Certificado de reservista</label>
                <input type="text"  value="<?php echo (@$update->certificado_reservista) ? $update->certificado_reservista : ""; ?>" name="certificado_reservista"  class="form-control" >
                <div class="form-text"></div>
              </div>              
            </div>
            <div class="form-group row">
              <div class="col-sm-3">
                <label for="data_admissao">Data de admissão</label>
                <input type="text"  value="<?php echo (@$update->data_admissao) ? $update->data_admissao : ""; ?>" name="data_admissao"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="data_demissao">Data de demissão</label>
                <input type="text"  value="<?php echo (@$update->data_demissao) ? $update->data_demissao : ""; ?>" name="data_demissao"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="salario_base">Salário base</label>
                <input type="text"  value="<?php echo (@$update->salario_base) ? $update->salario_base : ""; ?>" name="salario_base"  class="form-control" >
                <div class="form-text"></div>
              </div>   
              <div class="col-sm-3">
                <label for="vale_transporte">Valor do vale transporte</label>
                <input type="text"  value="<?php echo (@$update->vale_transporte) ? $update->vale_transporte : ""; ?>" name="vale_transporte"  class="form-control" >
                <div class="form-text"></div>
              </div>           
            </div>
            <div class="form-group row">
              <div class="col-sm-4">
                <label for="horario_entrada">Horário de entrada</label>
                <input type="text"  value="<?php echo (@$update->horario_entrada) ? $update->horario_entrada->format('H:i') : ""; ?>" name="horario_entrada"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="horario_intervalo">Horário do intervalo</label>
                <input type="text"  value="<?php echo (@$update->horario_intervalo) ? $update->horario_intervalo->format('H:i') : ""; ?>" name="horario_intervalo"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="horario_saida">Horário de saída</label>
                <input type="text"  value="<?php echo (@$update->horario_saida) ? $update->horario_saida->format('H:i') : ""; ?>" name="horario_saida"  class="form-control" >
                <div class="form-text"></div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-4">
                <label for="nome_pai">Nome do pai</label>
                <input type="text"  value="<?php echo (@$update->nome_pai) ? $update->nome_pai : ""; ?>" name="nome_pai"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="nome_mae">Nome da mãe</label>
                <input type="text"  value="<?php echo (@$update->nome_mae) ? $update->nome_mae : ""; ?>" name="nome_mae"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-4">
                <label for="filhos">Filhos?</label>
                 <select class="form-control" name="filhos" id="filhos-<?php echo $key; ?>">
                    <option value="">
                       Selecione...
                    </option>
                    <?php for($i = 0; $i < 11; $i++)
                      {
                        $suffix = ($i > 1) ? 's' : '';
                        $label = ($i === 0) ? "Não possui" : $i . ' filho' . $suffix;
                       ?>
                         <option <?php echo (@$update->filhos === $i) ? "selected=\"selected\"" : ""; ?> value="<?php echo $i; ?>"><?php echo $label; ?></option>
                       <?php
                      } 
                    ?>
                 </select>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-3">
                <label for="pessoa-colaborador[email]">Email</label>
                <input type="text"  value="<?php echo (@$update->pessoa->email) ? $update->pessoa->email : ""; ?>" name="pessoa-colaborador[email]"  class="form-control backend-check-<?php echo $key; ?>" data-field="email" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="pessoa-colaborador[email_secundario]">Email secundário</label>
                <input type="text"  value="<?php echo (@$update->pessoa->email_secundario) ? $update->pessoa->email_secundario : ""; ?>" name="pessoa-colaborador[email_secundario]"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="login[senha]">Senha</label>
                <input type="password" name="login[senha]"  class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-3">
                <label for="login[repetir_senha]">Repetir senha</label>
                <input type="password" name="login[repetir_senha]"  class="form-control" >
                <div class="form-text"></div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">
                <label>Unidade que acessa</label>
                <input type="hidden" name="login[unidades_acesso]" value="[]">
                  <div class="kt-checkbox-inline">
                      <?php 
                        $unidades_keys = array_keys($unidades);
                        foreach($unidades as $id => $unidade)
                          {
                            $unidades_acesso = @json_decode($update->pessoa->login->unidades_acesso, true);
                            ?>
                              <label class="kt-checkbox kt-checkbox--solid">
                                <input type="checkbox" <?php echo (@in_array($id, $unidades_acesso)) ? "checked=\"checked\"" : ""; ?> name="login[unidades_acesso][]" value="<?php echo $id; ?>"> <?php echo $unidade; ?>
                                <span></span>
                              </label>
                            <?php
                          }
                      ?>
                  </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">
                <label>Módulos que acessa (clique com o botão direito para definir a landing page)</label>
                <input type="hidden" name="login[modulos_acesso]" value="[]">
                <input type="hidden" name="login[landing_page]" value="<?php echo @$update->pessoa->login->landing_page; ?>">
                  <div class="kt-checkbox-inline">
                      <?php 
                        foreach($modulos as $id => $modulo)
                          {
                            $modulos_acesso = @json_decode($update->pessoa->login->modulos_acesso, true);
                            ?>
                              <label class="kt-checkbox kt-checkbox--solid modulos-checkbox-<?php echo $key; ?>">
                                <input type="checkbox" <?php echo (@in_array($id, $modulos_acesso)) ? "checked=\"checked\"" : ""; ?> name="login[modulos_acesso][]" value="<?php echo $id; ?>"> <?php echo $modulo; ?>
                                <span></span>
                              </label>
                            <?php
                          }
                      ?>
                  </div>
              </div>
            </div>
            <div class="row form-group margin-top-25">
               <div class="col-sm-12">
                 <label><h3>Endereços</h3></label>
               </div>
            </div>
            <?php if($update)
              {
                foreach($update->pessoa->enderecos as $address_index => $endereco)
                  {
                    ?>
                    <input type="hidden" name="enderecos[<?php echo $address_index ?>][id]" value="<?php echo $endereco->id; ?>">
                    <div class="form-group row address-fields-<?php echo $address_index ?>-<?php echo $key; ?>" >
                      <div class="col-sm-4">
                        <label for="cep">CEP</label>
                        <input type="text" data-key="<?php echo $address_index ?>" data-selector=".address-fields-<?php echo $address_index ?>-<?php echo $key; ?>" id="cep-<?php echo $address_index ?>-<?php echo $key; ?>" class="form-control" name="enderecos[<?php echo $address_index ?>][cep]" value="<?php echo $endereco->cep; ?>">
                        <div class="form-text"></div>
                      </div>
                      <div class="col-sm-8">
                        <label for="cep">Logradouro</label>
                        <input type="text" class="form-control" name="enderecos[<?php echo $address_index ?>][logradouro]" value="<?php echo $endereco->logradouro; ?>"> 
                        <div class="form-text"></div>
                      </div>
                    </div>
                    <div class="form-group row address-fields-<?php echo $address_index ?>-<?php echo $key; ?>" >
                        <div class="col-sm-4">
                          <label for="cep">Bairro</label>
                          <input type="text" class="form-control" name="enderecos[<?php echo $address_index ?>][bairro]" value="<?php echo $endereco->bairro; ?>">
                          <div class="form-text"></div>
                        </div>
                        <div class="col-sm-4">
                          <label for="cep">Cidade</label>
                          <input type="text" class="form-control" name="enderecos[<?php echo $address_index ?>][cidade]" value="<?php echo $endereco->cidade; ?>">
                          <div class="form-text"></div>
                        </div>
                        <div class="col-sm-4">
                          <label for="cep">Estado</label>
                          <input type="text" class="form-control" name="enderecos[<?php echo $address_index ?>][estado]" value="<?php echo $endereco->estado; ?>">
                          <div class="form-text"></div>
                        </div>
                    </div>
                    <div class="form-group row address-fields-<?php echo $address_index ?>-<?php echo $key; ?>">
                        <div class="col-sm-2">
                          <label for="cep">Número</label>
                          <input type="text" class="form-control" name="enderecos[<?php echo $address_index ?>][numero]" value="<?php echo $endereco->numero; ?>">
                          <div class="form-text"></div>
                        </div>
                        <div class="col-sm-10">
                          <label for="cep">Complemento</label>
                          <input type="text" class="form-control" name="enderecos[<?php echo $address_index ?>][complemento]" value="<?php echo $endereco->complemento; ?>">
                          <div class="form-text"></div>
                        </div>
                    </div>
                    <div class="form-group row address-fields-<?php echo $address_index ?>-<?php echo $key; ?>">
                    <div class="col-sm-12">
                      <label for="cep">&nbsp;</label>
                      <a class="btn btn-danger remover-endereco-<?php echo $key; ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $address_index ?>" data-id="<?php echo $endereco->id; ?>">Remover endereço</a>
                    </div>
                    </div>
                    <div class="kt-separator kt-separator--space-sm address-fields-<?php echo $address_index ?>-<?php echo $key; ?>">
                    </div>
                    <?php
                  }
              } 
            ?>
            <div class="row form-group" id="addresses-button-<?php echo $key; ?>">
               <div class="col-sm-12">
                 <a href="javascript:void(0)" class="btn btn-success" id="adicionar-endereco-<?php echo $key; ?>">Adicionar endereço</a>
               </div>
            </div>
            <div class="row form-group margin-top-25" >
               <div class="col-sm-12">
                 <label><h3>Telefones</h3></label>
               </div>
            </div>
            <?php if($update)
              {
                $telefones = $update->pessoa->telefones_array;
                  foreach($telefones as $phone_index => $phone)
                    {
                     ?>
                      <div class="form-group row phone-field-<?php echo $phone_index; ?>-<?php echo $key; ?>">
                       <div class="col-sm-11">
                           <label>Telefone</label>
                           <input class="form-control" id="phone-<?php echo $phone_index; ?>-<?php echo $key; ?>" name="pessoa-colaborador[telefones][]" type="text" value="<?php echo $phone; ?>" />
                       </div>
                       <div class="col-sm-1">
                           <label>&nbsp;</label>
                           <a class="btn btn-danger btn-block remover-telefone-<?php echo $key; ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $phone_index; ?>-<?php echo $key; ?>">Remover</a>
                       </div>
                     </div>
                     <?php
                    } 
              }
            ?>
            <div class="form-group parent-fields-<?php echo $key; ?>-<?php echo $key; ?> row" id="tel-button-<?php echo $key; ?>">
              <div class="col-sm-12">
                <label>&nbsp;</label>
                <a href="javascript:void(0)" class="btn btn-success btn-icon adicionar-telefone-<?php echo $key; ?>">
                  <i class="fa fa-plus"></i>
                </a>
              </div>
            </div>
          <?php 
            if($update)
              {
                ?>
                <div class="row">
                  <div class="col-sm-12" style="text-align: right; justify-content: flex-end">
                      <button class="btn btn-success" style="margin-bottom: 25px;"  id="inserir-colaborador-<?php echo $update->id; ?>">Salvar</button>
                  </div>
                </div>
                <?php
              }
         echo $this->Form->end(); ?>
<?php
  if(!$update)
    {
      $this->append('script');
      echo $this->Html->script('vanilla-masker');
      echo $this->Html->script('datepicker-pt-br');
      echo $this->Html->script('class-formulario-colaborador'); 
      echo $this->Html->script('cropper'); 
      echo $this->Html->script('jquery-cropper'); 
      echo $this->Html->script('novo-colaborador'); 
      $this->end();
      $this->append('css');
      echo $this->Html->css('cropper'); 
      $this->end();
    }
 ?>