<?php extract($config);
$key = ($update) ? $key : 1;
$form_options = ['id' => 'prospect-form-' . $key, 'class' => 'kt-form', 'data-key' => $key, 'enctype' => 'multipart/form-data'];
if($update)
  {
    $form_options['data-update'] = 1;
    $form_options['data-parentes']   = count($update->parentes);
    $form_options['data-enderecos']  = count($update->enderecos);
    $form_options['data-interacoes'] = count($update->interacoes);
    $form_options['data-telefones'] = 0;
    foreach($update->parentes as $parente)
      {
        $form_options['data-telefones'] += count(json_decode($parente->pessoa->telefones, true));
      }
  }

 echo $this->Form->create(null, $form_options); ?>  
            <?php 
              if($update)
                {
                  ?>
                    <input type="hidden" name="id" value="<?php echo $update->id; ?>">
                    <input type="hidden" name="pessoa-prospect[id]" value="<?php echo $update->pessoa->id; ?>">
                  <?php
                }
            ?>
            <div class="form-group row">
               <div class="col-sm-9">
                  <label for="pessoa-prospect[nome]">Nome do aluno</label>
                  <input class="form-control" type="text" name="pessoa-prospect[nome]" value="<?php echo @$update->pessoa->nome; ?>">
                  <div class="form-text"></div>
               </div>
               <div class="col-sm-3">
                  <label for="pessoa-prospect[sexo]">Sexo</label>
                  <div class="kt-radio-inline">
                     <label class="kt-radio kt-radio--solid">
                     <input type="radio" <?php echo (@$update->pessoa->sexo == 0) ? "checked=\"checked\"" : ""; ?> name="pessoa-prospect[sexo]" value="0"> Masculino
                     <span></span>
                     </label>
                     <label class="kt-radio kt-radio--solid" <?php echo (@$update->pessoa->sexo == 1) ? "checked=\"checked\"" : ""; ?>>
                     <input type="radio" name="pessoa-prospect[sexo]" value="1"> Feminino
                     <span></span>
                     </label>
                  </div>
                  <div class="form-text"></div>
               </div>
            </div>
            <div class="row form-group">
              <div class="col-sm-2">
                <label for="pessoa-prospect[data_nascimento]">Data de nascimento</label>
                <input type="hidden" name="pessoa-prospect[data_nascimento]" value="">
                <input type="text" autocomplete="false" value="<?php echo (@$update->pessoa->data_nascimento_formatada) ? $update->pessoa->data_nascimento_formatada : ""; ?>" name="pessoa-prospect[data_nascimento]" id="birthdate-<?php echo $key; ?>" class="form-control" >
                <div class="form-text"></div>
              </div>
              <div class="col-sm-1">
              <label style="visibility: hidden; width: 100%">A</label>
                <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success kt-switch--lg">
                <label>
                <input type="hidden" name="pessoa-prospect[ja_nascido]" value="0">
                <input type="checkbox" <?php echo ((!$update)||(@$update->pessoa->data_nascimento_formatada)) ? "checked=\"checked\"" : ""; ?> id="actual-input-<?php echo $key; ?>" name="pessoa-prospect[ja_nascido]" value="1">
                <span></span>
                </label>
                </span>
              </div>
              <div class="col-sm-3">
                <label>Idade atual</label>
                <input type="text" class="form-control disabled" disabled="disabled" id="idade-atual-<?php echo $key; ?>">
              </div>
              <div class="col-sm-3">
                <label>Idade em <?php $dt_this_year = new \DateTime(date('Y') . '-03-31'); echo $dt_this_year->format('d/m/Y');  ?></label>
                <input type="text" class="form-control disabled" disabled="disabled" id="idade-esse-ano-<?php echo $key; ?>">
              </div>
              <div class="col-sm-3">
                <label>Idade em <?php $dt_next_year = new \DateTime(date('Y') . '-03-31'); $dt_next_year->modify("+1 year"); echo $dt_next_year->format('d/m/Y');  ?></label>
                <input type="text" class="form-control disabled" disabled="disabled" id="idade-ano-que-vem-<?php echo $key; ?>">
              </div>
            </div>
            <div class="form-group row">
               <div class="col-sm-12">
                  <label for="irmao_ja_matriculado">Irmão já matriculado?</label>
                  <div class="kt-radio-inline">
                     <label class="kt-radio kt-radio--solid">
                     <input type="radio" <?php echo (!@$update->irmaos_matriculados) ? "checked=\"checked\"" : ""; ?> name="irmao_ja_matriculado"  value="0"> Não
                     <span></span>
                     </label>
                     <label class="kt-radio kt-radio--solid">
                     <input type="radio" <?php echo (@$update->irmaos_matriculados) ? "checked=\"checked\"" : ""; ?> name="irmao_ja_matriculado" value="1"> Sim
                     <span></span>
                     </label>
                  </div>
               </div>
            </div>
            <div class="form-group row kt-hidden" id="brother-selector-<?php echo $key; ?>">
               <div class="col-sm-12">
                  <label for="irmao_ja_matriculado">Nome do irmão</label>
                  <select id="select-brother-<?php echo $key; ?>" class="form-control">
                  </select>
               </div>
            </div>
            <div class="row form-group margin-top-25" >
               <div class="col-sm-12">
                 <label><h3>Endereços</h3></label>
               </div>
            </div>
            <?php if($update)
              {
                foreach($update->enderecos as $address_index => $endereco)
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
                    <div class="kt-separator kt-separator--space-sm   address-fields-<?php echo $address_index ?>-<?php echo $key; ?>">
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
            <div class="row form-group margin-top-25">
               <div class="col-sm-12">
                 <label><h3>Parentes</h3></label>
               </div>
            </div>
            <div class="form-group row kt-hidden" id="brother-parents-<?php echo $key; ?>">
               <div class="col-sm-12">
                  <label for="usar_dados_parente">Utilizar dados de parente de irmão já cadastrado?</label>
                  <div class="kt-radio-inline">
                     <label class="kt-radio kt-radio--solid">
                     <input type="radio" name="usar_dados_parente" value="0"> Não
                     <span></span>
                     </label>
                     <label class="kt-radio kt-radio--solid">
                     <input type="radio" name="usar_dados_parente" value="1"> Sim
                     <span></span>
                     </label>
                  </div>
               </div>
            </div>
            <?php if($update)
              {
                foreach($update->parentes as $parent_index => $parente)
                  {
                    ?>
                            <div class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
                            <input type="hidden" name="parentes[<?php echo $parent_index; ?>][id]" value="<?php echo $parente->id; ?>">
                            <input type="hidden" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][id]" value="<?php echo $parente->pessoa->id; ?>">
                              <div class="col-sm-3">
                                <label>Parentesco</label>
                                <select class="form-control" name="parentes[<?php echo $parent_index; ?>][parentesco]">
                                  <option value="">Selecione...</option>
                                    <?php 
                                      foreach($parentescos as $id => $label)
                                        {
                                          ?>
                                          <option value="<?php echo $id; ?>" <?php echo($parente->parentesco == $id) ? "selected=\"selected\"" : ""; ?>><?php echo $label; ?></option>
                                          <?php
                                        }
                                    ?>
                                </select>
                                <div class="form-text"></div>
                              </div>
                              <div class="col-sm-5">
                                <label>Nome</label>
                                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][nome]" value="<?php echo $parente->pessoa->nome; ?>">
                                <div class="form-text"></div>
                              </div>
                              <div class="col-sm-4">
                                <label>Ocupação</label>
                                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][ocupacao]" value="<?php echo $parente->pessoa->ocupacao; ?>">
                                <div class="form-text"></div>
                              </div>
                            </div>
                            <div class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
                              <div class="col-sm-6">
                                <label>Email</label>
                                <input type="text" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][email]" value="<?php echo $parente->pessoa->email; ?>">
                                <div class="form-text"></div>
                              </div>
                              <div class="col-sm-5">
                                <label>CPF</label>
                                <input type="text" id="cpf-<?php echo $parent_index; ?>-<?php echo $key; ?>" class="form-control" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][cpf]" value="<?php echo $parente->pessoa->cpf; ?>">
                                <div class="form-text"></div>
                              </div>
                              <div class="col-sm-1">
                                <label>Notificações</label>
                                <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success kt-switch--lg">
                                  <label>
                                    <input type="hidden" name="parentes[<?php echo $parent_index; ?>][atibuicoes]" value="">
                                    <input type="checkbox" <?php echo ($parente->notificacoes == 1) ? "checked=\"checked\"" : ""; ?>  name="parentes[<?php echo $parent_index; ?>][atribuicoes]" value="[2]">
                                    <span></span>
                                  </label>
                                </span>
                              </div>
                            </div>
                            <div class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row">
                              <div class="col-sm-12">
                                <label>Telefones</label>
                                <input type="hidden" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][telefones]" />
                              </div>
                            </div>
                            <div class="form-group parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> row" id="parents-tel-button-<?php echo $parent_index; ?>">
                              <div class="col-sm-12">
                                <label>&nbsp;</label>
                                <a href="javascript:void(0)" class="btn btn-success btn-icon adicionar-telefone-<?php echo $key; ?>" data-parent="<?php echo $parent_index; ?>">
                                  <i class="fa fa-plus"></i>
                                </a>
                              </div>
                            </div>
                            <?php $telefones = json_decode($parente->pessoa->telefones, true);
                            foreach($telefones as $phone_index => $phone)
                              {
                               ?>
                                <div class="form-group row parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?> phone-field-<?php echo $phone_index; ?>-<?php echo $key; ?>">
                                 <div class="col-sm-11">
                                     <label>Telefone</label>
                                     <input class="form-control" id="phone-<?php echo $phone_index; ?>-<?php echo $key; ?>" name="parentes[<?php echo $parent_index; ?>][pessoa-parente][telefones][]" type="text" value="<?php echo $phone; ?>" />
                                 </div>
                                 <div class="col-sm-1">
                                     <label>&nbsp;</label>
                                     <a class="btn btn-danger btn-block remover-telefone-<?php echo $key; ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $phone_index; ?>-<?php echo $key; ?>">Remover</a>
                                 </div>
                               </div>
                               <?php
                              } 
                            ?>
                          <div class="form-group row parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>" id="remove-parent-button-<?php echo $parent_index; ?>-<?php echo $key; ?>">
                            <div class="col-sm-12">
                              <label for="cep">&nbsp;</label>
                              <a class="btn btn-danger remover-parente-<?php echo $key; ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $parent_index; ?>" data-id="<?php echo $parente->id; ?>">Remover parente</a>
                            </div>
                          </div>
                          <div class="kt-separator kt-separator--space-sm   parent-fields-<?php echo $parent_index; ?>-<?php echo $key; ?>"></div>
                    <?php
                  }
              } 
            ?>
            <div class="row form-group" id="parents-button-<?php echo $key; ?>">
               <div class="col-sm-12">
                 <a href="javascript:void(0)" class="btn btn-success" id="adicionar-parente-<?php echo $key; ?>">Adicionar parente</a>
               </div>
            </div>
            <div class="row form-group margin-top-25">
              <div class="col-sm-12">
                <label><h3>Interações</h3></label>
              </div>
            </div>
                <?php 
                  if($update)
                    {
                      foreach($update->interacoes as $interaction_index => $interacao)
                        {
                          $icon_color = "info";
                          $disabled = "";
                          switch ($interacao->status) 
                            {
                            case 1:
                              $icon_color = "success";
                              $disabled = "disabled=\"disabled\"";
                              break;
                            case 2: 
                              $icon_color = "warning";
                              break;
                            } ?>
                          <div class="row accordion scope-0 disabled-accordion form-hidden interaction-forms-<?php echo $key; ?>" data-key="<?php echo $interaction_index; ?>">
                              <div class="col-sm-12">
                                <h4>
                                  <?php echo $interacao->data_formatada . " " . $interacao->hora_formatada; ?> <small style="font-size: 11px"> <?php echo $interacao->titulo; ?> <?php echo ($interacao->responsavel) ? " - " . $interacao->responsavel->pessoa->nome : ""; ?></small> 
                                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon <?php echo $icon_color; ?>" >
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <?php if($interacao->status == 1)
                                          {
                                            ?>
                                              <rect id="bound" x="0" y="0" width="24" height="24"/>
                                              <circle id="Oval-5" fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
                                              <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" id="Path-92" fill="#000000" fill-rule="nonzero"/>
                                            <?php
                                          } 
                                        elseif($interacao->status == 2)
                                          {
                                            ?>
                                              <rect id="bound" x="0" y="0" width="24" height="24"/>
                                            <path d="M11.1669899,4.49941818 L2.82535718,19.5143571 C2.557144,19.9971408 2.7310878,20.6059441 3.21387153,20.8741573 C3.36242953,20.9566895 3.52957021,21 3.69951446,21 L21.2169432,21 C21.7692279,21 22.2169432,20.5522847 22.2169432,20 C22.2169432,19.8159952 22.1661743,19.6355579 22.070225,19.47855 L12.894429,4.4636111 C12.6064401,3.99235656 11.9909517,3.84379039 11.5196972,4.13177928 C11.3723594,4.22181902 11.2508468,4.34847583 11.1669899,4.49941818 Z" id="Path-117" fill="#000000" opacity="0.3"/>
                                              <rect id="Rectangle-9" fill="#000000" x="11" y="9" width="2" height="7" rx="1"/>
                                              <rect id="Rectangle-9-Copy" fill="#000000" x="11" y="17" width="2" height="2" rx="1"/>
                                            <?php
                                          }
                                        else
                                          {
                                            ?>
                                              <rect id="bound" x="0" y="0" width="24" height="24"/>
                                              <circle id="Oval-5" fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
                                              <rect id="Rectangle-9" fill="#000000" x="11" y="10" width="2" height="7" rx="1"/>
                                              <rect id="Rectangle-9-Copy" fill="#000000" x="11" y="7" width="2" height="2" rx="1"/>
                                            <?php
                                          }
                                        ?>   
                                      </g>
                                  </svg>
                                </h4>
                              </div>  
                            </div>
                            <div class="kt-separator scope-0 kt-separator--space-sm   "></div>
                            <div class="row accordion scope-1 disabled-accordion" style="display:none;" id="form-interaction-<?php echo $key; ?>-<?php echo $interaction_index; ?>">
                              
                                <?php if(!$disabled)
                                  {
                                    ?>
                                    <input type="hidden" name="interacoes[<?php echo $interaction_index; ?>][id]" value="<?php echo $interacao->id; ?>">
                                    <?php
                                  } ?>
                                <div class="form-group interaction-fields-<?php echo $interaction_index; ?>-<?php echo $key; ?> row" style="margin-top:1.5rem">
                                  <div class="col-sm-6">
                                    <label>Descrição</label>
                                    <select <?php echo $disabled; ?> class="form-control" name="interacoes[<?php echo $interaction_index; ?>][tipo]">
                                    <?php 
                                      foreach($tipos_interacao as $id => $label)
                                        {
                                          ?>
                                          <option value="<?php echo $id; ?>" <?php echo($interacao->tipo == $id) ? "selected=\"selected\"" : ""; ?>><?php echo $label; ?></option>
                                          <?php
                                        }
                                    ?>
                                    </select>
                                    <div class="form-text"></div>
                                  </div>
                                  <div class="col-sm-6">
                                    <label>Título</label>
                                    <input <?php echo $disabled; ?> type="text" class="form-control" name="interacoes[<?php echo $interaction_index; ?>][titulo]" value="<?php echo $interacao->titulo; ?>">
                                    <div class="form-text"></div>
                                  </div>
                                </div>
                                <div class="form-group interaction-fields-<?php echo $interaction_index; ?>-<?php echo $key; ?> row">
                                  <div class="col-sm-3">
                                    <label>Responsável</label>
                                    <select <?php echo $disabled; ?> class="form-control" name="interacoes[<?php echo $interaction_index; ?>][responsavel]">
                                      <option value="">Selecione...</opiton> 
                                      <?php foreach($responsaveis as $id => $responsavel)
                                        {
                                          ?>
                                          <option <?php echo (@$interacao->responsavel->id == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $responsavel; ?></option>
                                          <?php
                                        } 
                                      ?>
                                    </select>
                                    <div class="form-text"></div>
                                  </div>
                                  <div class="col-sm-3">
                                    <label>Arquivo</label>
                                    <input <?php echo $disabled; ?> type="file" class="form-control" name="interacoes[<?php echo $interaction_index; ?>][arquivo]">
                                    <div class="kt-font-primary">
                                      <?php if(($interacao->caminho_arquivo)&&($interacao->titulo_arquivo))
                                        {
                                          ?> 
                                          <div class="dropdown">
                                             <button class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdown-<?php echo $interacao->id; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <?php $titulo = $interacao->titulo_arquivo;
                                              if(strlen($titulo) > 43)
                                                {
                                                  $pedacos = explode('.', $titulo);
                                                  $ext = array_pop($pedacos);
                                                  $titulo = substr($titulo, 0, 39) . "[...]." . $ext;
                                                }
                                              echo $titulo; ?>
                                             </button>
                                             <div class="dropdown-menu" aria-labelledby="dropdown-<?php echo $interacao->id; ?>">
                                               <a class="dropdown-item ver-arquivo" target="_blank"  href="<?php echo $this->Url->build(['controller' => 'prospects', 'action' => 'visualizar-arquivo', $interacao->id], true); ?>" title="Caso o arquivo tenha uma extensão não suportada pelo navegador (ex: .zip), a nova aba iniciará um download por padrão"> 
                                                 Abrir em nova aba
                                               </a>
                                               <a class="dropdown-item" target="_blank"  href="<?php echo $this->Url->build(['controller' => 'prospects', 'action' => 'baixar-arquivo', $interacao->id], true); ?>">
                                                 Baixar
                                               </a>
                                               <?php if(!$disabled)
                                                 {
                                                  ?>
                                                   <a class="dropdown-item danger remover-arquivo-<?php echo $key; ?>" data-key="<?php echo $key; ?>" data-id="<?php echo $interacao->id; ?>" data-index="<?php echo $interaction_index; ?>" href="javascript:void(0)">
                                                     Remover arquivo
                                                   </a>
                                                  <?php
                                                 }
                                                ?>
                                             </div>
                                           </div>
                                          <?php
                                        }
                                      ?>
                                    </div>
                                  </div>
                                  <div class="col-sm-3">
                                    <label>Data</label>
                                    <input <?php echo $disabled; ?> type="text" class="form-control" <?php echo (!$disabled) ? 'id="interaction-date-<?php echo $interaction_index; ?>-<?php echo $key; ?>"' : ''; ?> name="interacoes[<?php echo $interaction_index; ?>][data]"  value="<?php echo $interacao->data_formatada; ?>">
                                    <div class="form-text"></div>
                                  </div>
                                  <div class="col-sm-3">
                                    <label>Hora</label>
                                     <input type="text" <?php echo $disabled; ?>  <?php echo (!$disabled) ? 'id="interaction-time-<?php echo $interaction_index; ?>-<?php echo $key; ?>"' : ''; ?>  class="form-control" name="interacoes[<?php echo $interaction_index; ?>][hora]"  value="<?php echo $interacao->hora_formatada; ?>">
                                     <div class="form-text"></div>
                                  </div>
                                </div>
                                <div class="form-group interaction-fields-<?php echo $interaction_index; ?>-<?php echo $key; ?> row">
                                  <div class="col-sm-12">
                                    <label>Mensagem</label>
                                    <textarea <?php echo $disabled; ?> style="height:150px" class="form-control" name="interacoes[<?php echo $interaction_index; ?>][mensagem]"><?php echo $interacao->mensagem; ?></textarea>
                                  </div>
                                </div>
                                <div class="form-group interaction-fields-<?php echo $interaction_index; ?>-<?php echo $key; ?> row">
                                  <div class="col-sm-12">
                                    <label>Observação</label>
                                    <textarea <?php echo $disabled; ?> style="height:150px" class="form-control" name="interacoes[<?php echo $interaction_index; ?>][observacao]"><?php echo $interacao->observacao; ?></textarea>
                                  </div>
                                </div>
                                <div class="form-group row interaction-fields-<?php echo $interaction_index; ?>-<?php echo $key; ?>" id="remove-interaction-button-<?php echo $interaction_index; ?>"> 
                                  <div class="col-sm-12">
                                    <label for="cep">&nbsp;</label>
                                    <?php if(!$disabled)
                                      {
                                        ?>
                                        <a  class="btn btn-danger remover-interacao-<?php echo $key; ?>" style="color:white" href="javascript:void(0)" data-key="<?php echo $interaction_index; ?>" data-id="<?php echo $interacao->id; ?>">Remover interação</a>
                                        <?php
                                      }
                                    else
                                      {
                                        ?>
                                        <a  class="btn btn-danger disabled" style="color:white" href="javascript:void(0)">Remover interação</a>
                                        <?php
                                      } ?>
                                  </div>
                                </div> 
                                <div class="kt-separator kt-separator--space-sm   interaction-fields-<?php echo $interaction_index; ?>-<?php echo $key; ?>"></div>
                                  
                            </div>
                            <div class="kt-separator scope-0 kt-separator--space-sm  " style="display:none;"></div>
                          <?php
                        }
                    }
                ?>
            <div class="row form-group no-accordion" id="interactions-button-<?php echo $key; ?>">
              <div class="col-sm-12">
                  <a href="javascript:void(0)" class="btn btn-success" id="adicionar-interacao-<?php echo $key; ?>">Adicionar interação</a>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">
                <label for="unidade">Unidade</label>
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
            <div class="form-group row">
               <div class="col-sm-4">
                  <label for="permanencia">Permanência</label>
                  <select class="form-control" name="permanencia">
                     <option value="">
                        Selecione...
                     </option>
                     <?php foreach($permanencias as $id => $turno)
                       {
                        ?>
                          <option <?php echo (@$update->permanencia == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $turno; ?></option>
                        <?php
                       } 
                     ?>
                  </select>
                  <div class="form-text"></div>
               </div>
               <div class="col-sm-4">
                  <label for="turno">Turno</label>
                  <select class="form-control" name="turno">
                     <option value="">
                        Selecione...
                     </option>
                     <?php foreach($turnos as $id => $turno)
                       {
                        ?>
                          <option  <?php echo (@$update->turno == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $turno; ?></option>
                        <?php
                       } 
                     ?>
                  </select>
                  <div class="form-text"></div>
               </div>
               <div class="col-sm-4">
                  <label for="horario">Horário</label>
                  <select class="form-control" name="horario">
                     <option value="">
                        Selecione...
                     </option>
                     <?php foreach($horarios as $id => $horario)
                       {
                        ?>
                          <option  <?php echo (@$update->horario == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $horario; ?></option>
                        <?php
                       } 
                     ?>
                  </select>
                  <div class="form-text"></div>
               </div>
            </div>
            <div class="form-group row">
               <div class="col-sm-12">
                  <label for="necessidades_especiais">Tem alguma necessidade educacional especial?</label>
                  <div class="kt-radio-inline">
                     <label class="kt-radio kt-radio--solid">
                        <input type="radio" <?php echo ((@$update->necessidades_especiais == 0)||(!$update)) ? "checked=\"checked\"" : ""; ?> name="necessidades_especiais" value="0"> Não
                     <span></span>
                     </label>
                     <label class="kt-radio kt-radio--solid">
                        <input type="radio" <?php echo (@$update->necessidades_especiais == 1) ? "checked=\"checked\"" : ""; ?> name="necessidades_especiais" value="1"> Sim
                     <span></span>
                     </label>
                  </div>
               </div>
            </div>
            <div class="form-group row">
               <div class="col-sm-12">
                  <label for="acompanhamento_sistematico">Faz algum acompanhamento sistemático com algum profissional?</label>
                  <div class="kt-radio-inline">
                     <label class="kt-radio kt-radio--solid">
                     <input type="radio" <?php echo (!count(@$update->acompanhamentos_sistematicos_array)) ? "checked=\"checked\"" : ""; ?> name="acompanhamento_sistematico" value="0"> Não
                     <span></span>
                     </label>
                     <label class="kt-radio kt-radio--solid">
                     <input type="radio"  <?php echo (count(@$update->acompanhamentos_sistematicos_array)) ? "checked=\"checked\"" : ""; ?> name="acompanhamento_sistematico" value="1"> Sim
                     <span></span>
                     </label>
                  </div>
               </div>
            </div>  
            <div class="form-group row <?php echo (!count(@$update->acompanhamentos_sistematicos_array)) ? "kt-hidden" : ""; ?>" id="checkboxes-<?php echo $key; ?>">
              <input type="hidden" name="acompanhamentos_sistematicos">
              <div class="col-sm-12">
                <label>Acompanhamentos Sistemáticos</label>
                <div class="kt-checkbox-inline" data-name="acompanhamentos_sistematicos">
                  <?php ; 
                    foreach($acompanhamentos_sistematicos as $id => $label)
                      {
                        ?>
                        <label class="kt-checkbox kt-checkbox--bold">
                          <input name="acompanhamentos_sistematicos[]" <?php echo (@in_array($id, @$update->acompanhamentos_sistematicos_array)) ? "checked=\"checked\"" : ""; ?> type="checkbox" value="<?php echo $id; ?>"> <?php echo $label; ?>
                          <span></span>
                        </label>
                        <?php
                      } 
                  ?>
                </div>
              </div>
              <span class="form-text"></span>
            </div>           
            <div class="form-group row">
              <div class="col-sm-3">
                <div>
                  <label for="data_primeiro_atendimento">Data do primeiro atendimento</label>
                  <input autocomplete="false" class="form-control" type="text" name="data_primeiro_atendimento" placeholder="dd/mm/yyyy" value="<?php echo @$update->data_primeiro_atendimento_formatada; ?>">
                  <div class="form-text"></div>
                </div>
              </div>
              <div class="col-sm-3">
                <div>
                  <label for="responsavel_atendimento">Responsável pelo antendimento</label>
                  <select class="form-control" name="responsavel_atendimento">
                    <option value="">Selecione...</option>
                    <?php foreach($responsaveis as $id => $responsavel)
                      {
                        ?>
                        <option <?php echo ($update->responsavel_atendimento == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $responsavel; ?></option>
                        <?php
                      } 
                    ?>
                  </select>
                  <div class="form-text"></div>
                </div>
              </div>
              <div class="col-lg-3">
            <label for="como_conheceu">Como conheceu a Aldeia?</label>
            <select class="form-control" name="como_conheceu">
              <option value=""> Selecione...</option>
              <?php foreach($meios_conhecimento as $id => $meio)
                {
                 ?>
                   <option <?php echo (@$update->como_conheceu == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $meio; ?></option>
                 <?php
                } 
              ?>
            </select>
            <span class="form-text"></span>
          </div>
              <div class="col-lg-3">
            <label for="meio_atendimento">O atendimento foi feito</label>
            <select class="form-control" name="meio_atendimento">
              <option value="">Selecine.,..</option>
              <?php foreach($meios_atendimento as $id => $meio)
                {
                 ?>
                   <option <?php echo (@$update->meio_atendimento == $id) ? "selected=\"selected\"" : ""; ?> value="<?php echo $id; ?>"><?php echo $meio; ?></option>
                 <?php
                } 
              ?>
            </select>
            <span class="form-text"></span>
            </div>
          </div>
          <?php 
            if($update)
              {
                ?>
                <div class="row">
                  <div class="col-sm-12" style="text-align: right; justify-content: flex-end;">
                      <button class="btn btn-success" style="margin-bottom: 25px;"  id="inserir-prospect-<?php echo $update->id; ?>">Salvar</button>
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
          echo $this->Html->script('class-formulario-prospect'); ?>
      <script type="text/javascript">
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
      var options_tipo_interacao = '<option value="">Selecione...</option>' +
        <?php 
          foreach($tipos_interacao as $id => $label)
            {
              ?>
              '<option value="<?php echo $id; ?>"><?php echo $label; ?></option>' +
              <?php
            }
        ?>
        '';
      var options_responsaveis = '<option value="">Selecione...</option>' +
        <?php 
          foreach($responsaveis as $id => $label)
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
      <?php echo $this->Html->script('novo-prospect'); 
      $this->end(); 
    }
 ?>