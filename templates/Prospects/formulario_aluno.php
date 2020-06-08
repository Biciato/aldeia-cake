<div class="tab-pane" id="aba_aluno_<?php echo $key; ?>" role="tabpanel">
   <div class="form-group row">
      <div class="col-sm-9">
         <label for="nome">Nome do aluno</label>
         <input class="form-control" type="text" name="aluno[<?php echo $key ?>][nome]" id="name_aluno_<?php echo $key; ?>" placeholder="Nome do aluno">
         <div class="form-text"></div>
      </div>
      <div class="col-sm-3">
         <label for="sexo">Sexo</label>
         <div class="kt-radio-inline">
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][sexo]" checked="" value="2"> Masculino
            <span></span>
            </label>
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][sexo]" value="3"> Feminino
            <span></span>
            </label>
         </div>
         <div class="form-text"></div>
      </div>
   </div>
   <div class="form-group row">
      <div class="col-sm-12">
         <label for="irmao_ja_matriculado">Irmão já matriculado?</label>
         <div class="kt-radio-inline">
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][irmao_ja_matriculado]" value="0"> Não
            <span></span>
            </label>
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][irmao_ja_matriculado]" value="1"> Sim
            <span></span>
            </label>
         </div>
      </div>
   </div>
   <div class="row">
      <table class="table-bordered table table-stripped table-condensed">
         <thead>
            <tr>
               <th align="center" valign="middle" colspan="7">
                  <h6>Endereços</h6>
               </th>
               <th valign="middle">
                  <a href="javascript:void(0)" class="btn btn-success btn-xs" id="add_address_<?php echo $key; ?>" data-key="<?php echo $key; ?>"><small>Adicionar</small></a>
               </th>
            </tr>
            <tr>
               <th>
                  CEP
               </th>
               <th>
                  Logradouro
               </th>
               <th>
                  Bairro
               </th>
               <th>
                  Cidade
               </th>
               <th>
                  Estado
               </th>
               <th>
                  Numero
               </th>
               <th>
                  Complemento
               </th>
               <th>
                  Ações
               </th>
            </tr>
         </thead>
         <tbody id="enderecos_aluno_<?php echo $key; ?>">
         </tbody>
      </table>
   </div>
   <div class="form-group row">
      <div class="col-sm-12">
         <label for="usar_dados_parente">Utilizar dados de parente de irmão já cadastrado?</label>
         <div class="kt-radio-inline">
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][usar_dados_parente]" value="0"> Não
            <span></span>
            </label>
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][usar_dados_parente]" value="1"> Sim
            <span></span>
            </label>
         </div>
      </div>
   </div>
   <div class="row">
      <table class="table-bordered table table-stripped table-condensed">
         <thead>
            <tr>
               <th align="center" valign="middle" colspan="7">
                  <h6>Parentes</h6>
               </th>
               <th valign="middle"><a href="javascript:void(0)" class="btn btn-success btn-xs" id="add_parent_<?php echo $key; ?>" data-key="<?php echo $key; ?>"><small>Adicionar</small></a></th>
            </tr>
            <tr>
               <th>
                  Parentesco
               </th>
               <th>
                  Nome
               </th>
               <th>
                  Ocupação
               </th>
               <th>
                  Email
               </th>
               <th>
                  CPF
               </th>
               <th>
                  Telefones
               </th>
               <th>
                  Notificações
               </th>
               <th>
                  Ações
               </th>
            </tr>
         </thead>
         <tbody id="parentes_aluno_<?php echo $key; ?>">
         </tbody>
      </table>
   </div>
   <div class="form-group row">
      <div class="col-sm-12">
         <label for="unidade">Unidade</label>
         <div class="kt-radio-inline">
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][unidade]" value="0"> Unidade I
            <span></span>
            </label>
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][unidade]" value="1"> Unidade II
            <span></span>
            </label>
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][unidade]" value="1"> Unidade III
            <span></span>
            </label>
         </div>
      </div>
   </div>
   <div class="form-group row">
      <div class="col-sm-4">
         <label for="permanencia">Permanência</label>
         <select class="form-control" name="aluno[<?php echo $key ?>][permanencia]">
            <option>
               Selecione...
            </option>
            <option>A</option>
            <option>B</option>
            <option>C</option>
         </select>
      </div>
      <div class="col-sm-4">
         <label for="turno">Turno</label>
         <select class="form-control" name="aluno[<?php echo $key ?>][turno]">
            <option>
               Selecione...
            </option>
            <option>A</option>
            <option>B</option>
            <option>C</option>
         </select>
      </div>
      <div class="col-sm-4">
         <label for="horario">Horário</label>
         <select class="form-control" name="aluno[<?php echo $key ?>][horario]">
            <option>
               Selecione...
            </option>
            <option>A</option>
            <option>B</option>
            <option>C</option>
         </select>
      </div>
   </div>
   <div class="form-group row">
      <div class="col-sm-12">
         <label for="unidade">Portador de necessidades especiais?</label>
         <div class="kt-radio-inline">
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][unidade]" value="0"> Não
            <span></span>
            </label>
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][unidade]" value="1"> Sim
            <span></span>
            </label>
         </div>
      </div>
   </div>
   <div class="form-group row">
      <div class="col-sm-12">
         <label for="acompanhamento_sistematico">Necessita de acompanhamento sistemático?</label>
         <div class="kt-radio-inline">
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][acompanhamento_sistematico]" value="0"> Não
            <span></span>
            </label>
            <label class="kt-radio kt-radio--solid">
            <input type="radio" name="aluno[<?php echo $key ?>][acompanhamento_sistematico]" value="1"> Sim
            <span></span>
            </label>
         </div>
      </div>
   </div>
</div>