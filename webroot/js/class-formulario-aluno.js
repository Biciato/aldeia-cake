class FormularioAluno
  {
    constructor(form_id, success_callback)
      {
        this.key               = form_id;
        this.form              = $("#aluno-form-" + form_id);
        this.address_keys      = (this.form.data('update')) ? this.form.data('enderecos') : 0;
        this.parent_keys       = (this.form.data('update')) ? this.form.data('parentes')  : 0;
        this.phone_keys        = (this.form.data('update')) ? this.form.data('telefones') : 0;
        this.dados_atendimento = null;
        this.removed           =
          {
          	parentes  : [],
          	enderecos : [],
          };
        this.dp_config         = 
          {
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation:'auto bottom',
            locale: 'pt-BR',
            language: 'pt-BR'
          };
        this.insertSuccessCallback = success_callback;
        var classe = this;
        this.aluno_form = 
          {
            init: function()
              {
                var self = this;
                $(document).on('click', "#adicionar-endereco-" +  classe.key, {completeAddress: self.completeAddress}, self.addAddress);
                $(document).on('click', "#adicionar-parente-" +  classe.key,  {completeAddress: self.completeAddress}, self.addParent);
                $(document).on('click', '.remover-endereco-' + classe.key, self.removeAddress);
                $(document).on('click', '.remover-parente-' + classe.key,  self.removeParent);
                $(document).on('click', '.adicionar-telefone-' + classe.key + ':not(#tel-button-' + classe.key + ')', self.addPhone);
                $(document).on('click', '.remover-telefone-' + classe.key, self.removePhone);
                $(document).on('change', '#birthdate-' +  classe.key, self.updateAge);
                $(document).on('change', '.mesmo-endereco-' +  classe.key, self.parentAddress);
                $(document).on('click', "#inserir-aluno-" +  classe.key, self.insertStudent);
                $(document).on('click', '.accordion.inner-accordion[data-form="' + classe.key + '"]', self.innerAccordions);
                $(document).on('change', '[data-parent-id="atendimento-' + classe.key + '"] input, [data-parent-id="atendimento-' + classe.key + '"] select:not([name^="turmas["])', {buscarServicos: self.buscarServicos, limparServicos: self.limparServicos, buscarTurmas: self.buscarTurmas, limparTurmas: self.limparTurmas},self.toggleInputs);
                $(document).on('input', '[data-parent-id="financeiro-' + classe.key + '"] input:not([name="dia_vencimento"])', self.percentageInput);
                $(document).on('input', '[name="dia_vencimento"]', self.validDay);
                self.initInputs();
                if(classe.form.data('update'))
                  {
                    $(document).on('change', '[data-parent-id="servicos-' + classe.key + '"] input[type="checkbox"]', self.changeServicos);
                    var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
                    var tel = document.querySelectorAll("[id^='phone-'][id$='-" + classe.key + "']");
                    $.each(tel, function(t, _tel)
                      {
                        VMasker(_tel).maskPattern(telMask[0]);
                        _tel.addEventListener('input', classe.inputHandler.bind(undefined, telMask, 14), false);
                      });
                    VMasker($("[id^='cep-'][id$='-" + classe.key + "']")).maskPattern("99.999-999");
                    var completeAddress = self.completeAddress;
                    $(document).on('blur', "[id^='cep-'][id$='-" + classe.key + "']", completeAddress);
                    VMasker($("[id^='cpf-'][id$='-" + classe.key + "']")).maskPattern("999.999.999.99");
                    self.updateAge();
                  }
                return this;
              },
            initInputs: function()
              {
                classe.form.find('#birthdate-' + classe.key + ', [name^="data_"], [name^="pessoa-aluno[data_"]').datepicker(classe.dp_config);
                VMasker(classe.form.find('#birthdate-' + classe.key + ', [name^="data_"], [name^="pessoa-aluno[data_"]')).maskPattern("99/99/9999");
                VMasker($("[name='pessoa-aluno[cpf]']")).maskPattern("999.999.999.99");
              },
            updateAge: function()
              {
                var _date  = $("#birthdate-" + classe.key).val();
                if(_date)
                  {
                    _date      = _date.split('/');
                    var today  = new Date();
                    var date   = new Date(_date[2] + "-" + _date[1] + "-" + _date[0]);
                    var idade    = classe.getAge(date, today);
                    var idade_corte = classe.getAge(date, datas_corte.esse_ano());
                    var idade_corte_proximo_ano = classe.getAge(date, datas_corte.ano_que_vem());
                    $("#idade-atual-" + classe.key).val(idade.years + " anos e " + idade.months + " meses");
                    $("#idade-esse-ano-" + classe.key).val(idade_corte.years + " anos e " + idade_corte.months + " meses");
                    $("#idade-ano-que-vem-" + classe.key).val(idade_corte_proximo_ano.years + " anos e " + idade_corte_proximo_ano.months + " meses");
                  }
                else
                  {
                    $("#idade-atual-" + classe.key + ", #idade-ano-que-vem-" + classe.key + ", #idade-esse-ano-" + classe.key).val("");
                  }
              },
            completeAddress: function()
              {
                var selector = $(this).data('selector');
                var key = $(this).data('key');
                var cep = $(this).val().replace(/[\.-]+/g, "");
                var fields = classe.form.find(selector);
                $.ajax(
                  {
                    url: "https://api.postmon.com.br/v1/cep/" + cep,
                    type: "GET",
                    dataType: 'JSON',
                    success: function(resposta)
                      {
                        fields.find('[name$="[cidade]"]').val(resposta.cidade);
                        fields.find('[name$="[bairro]"]').val(resposta.bairro);
                        fields.find('[name$="[logradouro]"]').val(resposta.logradouro);
                        fields.find('[name$="[estado]"]').val(resposta.estado);
                        fields.find('[name$="[numero]"]').focus();
                      },
                  }
                );
              },
            addAddress: function(e)
              {
                classe.address_keys++;
                var address_keys = classe.address_keys;
                var markup = '<div data-parent-id="enderecos-telefones-' + classe.key + '" class="form-group row address-fields-' + address_keys + '-' + classe.key + '" >' +
                  '<div class="col-sm-4">' +
                    '<label for="cep">CEP</label>' +
                    '<input type="text" data-key="' + address_keys + '" data-selector=".address-fields-' + address_keys + '-' + classe.key + '" id="cep-' + address_keys + '-' + classe.key + '" class="form-control" name="enderecos[' + address_keys + '][cep]">' +
                    '<div class="form-text"></div>' +
                  '</div>' +
                  '<div class="col-sm-8">' +
                    '<label for="cep">Logradouro</label>' +
                    '<input type="text" class="form-control" name="enderecos[' + address_keys + '][logradouro]">' +
                    '<div class="form-text"></div>' +
                  '</div>' +
                '</div>' +
                '<div data-parent-id="enderecos-telefones-' + classe.key + '" class="form-group row address-fields-' + address_keys + '-' + classe.key + '">' +
                    '<div class="col-sm-4">' +
                      '<label for="cep">Bairro</label>' +
                      '<input type="text" class="form-control" name="enderecos[' + address_keys + '][bairro]">' +
                      '<div class="form-text"></div>' +
                    '</div>' +
                    '<div class="col-sm-4">' +
                      '<label for="cep">Cidade</label>' +
                      '<input type="text" class="form-control" name="enderecos[' + address_keys + '][cidade]">' +
                      '<div class="form-text"></div>' +
                    '</div>' +
                    '<div class="col-sm-4">' +
                      '<label for="cep">Estado</label>' +
                      '<input type="text" class="form-control" name="enderecos[' + address_keys + '][estado]">' +
                      '<div class="form-text"></div>' +
                    '</div>' +
                '</div>' +
                '<div data-parent-id="enderecos-telefones-' + classe.key + '" class="form-group row address-fields-' + address_keys + '-' + classe.key + '">' +
                    '<div class="col-sm-2">' +
                      '<label for="cep">Número</label>' +
                      '<input type="text" class="form-control" name="enderecos[' + address_keys + '][numero]">' +
                      '<div class="form-text"></div>' +
                    '</div>' +
                    '<div class="col-sm-10">' +
                      '<label for="cep">Complemento</label>' +
                      '<input type="text" class="form-control" name="enderecos[' + address_keys + '][complemento]">' +
                      '<div class="form-text"></div>' +
                    '</div>' +
                '</div>' +
                '<div data-parent-id="enderecos-telefones-' + classe.key + '" class="form-group row address-fields-' + address_keys + '-' + classe.key + '">' +
                '<div class="col-sm-12">' +
                  '<label for="cep">&nbsp;</label>' +
                  '<a class="btn btn-danger remover-endereco-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + address_keys + '">Remover endereço</a>' +
                '</div>' + 
                '</div>' +
                '<div  data-parent-id="enderecos-telefones-' + classe.key + '" style="margin-bottom:20px" class="kt-separator kt-separator--space-sm  address-fields-' + address_keys + '-' + classe.key + '">' + 
                '</div>';
                $(markup).insertBefore("#addresses-button-" + classe.key);
                VMasker(document.querySelector("#cep-" + address_keys + "-" + classe.key)).maskPattern("99.999-999");
                var completeAddress = e.data.completeAddress;
                $(document).on('blur', "#cep-" + address_keys  + "-" + classe.key, completeAddress);
              },
            addParent: function(e)
              {
                classe.parent_keys++;
                var parent_keys = classe.parent_keys;
                var markup = '<div data-parent-id="parentes-' + classe.key + '" class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                      '<div class="col-sm-4">' +
                        '<label>Parentesco</label>' +
                        '<select class="form-control" name="parentes[' + parent_keys + '][parentesco]">' +
                          options_parentescos +
                        '</select>' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-4">' +
                        '<label>Nome</label>' +
                        '<input type="text" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][nome]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +                      
                      '<div class="col-sm-4">' +
                         '<label for="parentes[' + parent_keys + '][pessoa-parente][sexo]">Sexo</label>' +
                         '<div class="kt-radio-inline">' +
                            '<label class="kt-radio kt-radio--solid">' +
                            '<input type="radio" name="parentes[' + parent_keys + '][pessoa-parente][sexo]" value="0"> Masculino' +
                            '<span></span>' +
                            '</label>' +
                            '<label class="kt-radio kt-radio--solid" >' +
                            '<input type="radio" name="parentes[' + parent_keys + '][pessoa-parente][sexo]" value="1"> Feminino' +
                            '<span></span>' +
                            '</label>' +
                         '</div>' +
                         '<div class="form-text"></div>' +
                      '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                      '<div class="col-sm-4">' +
                        '<label>CPF</label>' +
                        '<input type="text" id="cpf-' + parent_keys + '-' + classe.key + '" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][cpf]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-4">' +
                        '<label>RG</label>' +
                        '<input type="text" id="rg-' + parent_keys + '-' + classe.key + '" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][rg]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-4">' +
                        '<label>Órgão expeditor</label>' +
                        '<input type="text" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][orgao_expeditor]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                      '<div class="col-sm-3">' +
                        '<label>Email</label>' +
                        '<input type="text" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][email]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-3">' +
                        '<label>Data de nascimento</label>' +
                        '<input type="text" id="data-nascimento-parente-' + parent_keys + '-' + classe.key + '" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][data_nascimento]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-3">' +
                        '<label>Nacionalidade</label>' +
                        '<select class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][nacionalidade]">' +
                          options_nacionalidades +
                        '</select>' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-3">' +
                        '<label>Naturalidade</label>' +
                        '<select class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][naturalidade]">' +
                          options_naturalidades +
                        '</select>' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" class="row accordion scope-0 inner-accordion parent-fields-' + parent_keys + '-' + classe.key + '" data-form="' + classe.key + '" id="atribuicoes-parente-' + parent_keys + '-' + classe.key + '">' +
                      '<div class="col-sm-12">' +
                        '<h4>' +
                          'Atribuições' +
                        '</h4>' +
                      '</div>' +  
                    '</div>' + 
                     '<div data-parent-id="parentes-' + classe.key + '" style="margin-bottom:20px" class="kt-separator kt-separator--space-sm  parent-fields-' + parent_keys + '-' + classe.key + '">' + 
                    '</div>' +
                    '<div style="display:none" data-grandparent-id="parentes-' + classe.key + '" data-parent-id="atribuicoes-parente-' + parent_keys + '-' + classe.key + '" class="row form-group parent-fields-' + parent_keys + '-' + classe.key + '" >' +
                      '<div class="col-sm-12">' +
                        '<div class="kt-radio-inline" data-name="responsavel_legal">' +
                            '<label class="kt-radio kt-radio--bold">' +
                              '<input name="responsavel_legal"  type="radio" value="' + parent_keys + '">  Responsável legal' + 
                                  '<span></span>' +
                            '</label>' +
                        '</div>' +
                      '</div>' +
                    '</div>' + 
                    '<div style="display:none" data-grandparent-id="parentes-' + classe.key + '" data-parent-id="atribuicoes-parente-' + parent_keys + '-' + classe.key + '" class="row form-group parent-fields-' + parent_keys + '-' + classe.key + '" >' +
                      '<div class="col-sm-12">' +
                        '<input type="hidden" name="parentes[' + parent_keys + '][atribuicoes]" value="[]">' +
                        '<div class="kt-checkbox-list" data-name="parentes[' + parent_keys + '][atribuicoes]">' +
                            '<label class="kt-checkbox kt-checkbox--bold">' +
                              '<input name="parentes[' + parent_keys + '][atribuicoes][]"  type="checkbox" value="0">  Contactar em caso de emergência' + 
                                  '<span></span>' +
                            '</label>' +
                            '<label class="kt-checkbox kt-checkbox--bold">' +
                              '<input name="parentes[' + parent_keys + '][atribuicoes][]"  type="checkbox" value="1">  Autorização de saída do aluno' + 
                                  '<span></span>' +
                            '</label>' +
                            '<label class="kt-checkbox kt-checkbox--bold">' +
                              '<input name="parentes[' + parent_keys + '][atribuicoes][]"  type="checkbox" value="2"> Receber Circular' + 
                                  '<span></span>' +
                            '</label>' +
                            '<label class="kt-checkbox kt-checkbox--bold">' +
                              '<input name="parentes[' + parent_keys + '][atribuicoes][]"  type="checkbox" value="3">  Acesso ao Financeiro' + 
                                  '<span></span>' +
                            '</label>' +
                        '</div>' +
                      '</div>' +
                    '</div>' + 
                    '<div data-parent-id="parentes-' + classe.key + '" class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                      '<div class="col-sm-12">' +
                        '<label>Telefones</label>' +
                        '<input type="hidden" name="parentes[' + parent_keys + '][pessoa-parente][telefones]" />'+
                      '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row" id="parents-tel-button-' + parent_keys + '-' + classe.key + '">' +
                      '<div class="col-sm-12">' +
                        '<label>&nbsp;</label>' +
                        '<a href="javascript:void(0)" class="btn btn-success btn-icon adicionar-telefone-' + classe.key + '" data-parent="' + parent_keys + '">' +
                          '<i class="fa fa-plus"></i>' + 
                        '</a>' +
                      '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                      '<div class="col-sm-12">' +
                        '<div class="kt-checkbox-inline" data-name="mesmo_endereco">' +
                            '<input type="hidden" name="parentes[' + parent_keys + '][endereco][mesmo_endereco]" value="0"/>' +
                            '<label class="kt-checkbox kt-checkbox--bold">' +
                              '<input name="parentes[' + parent_keys + '][endereco][mesmo_endereco]" data-key="' + parent_keys + '" checked="checked" class="mesmo-endereco-' + classe.key + '"  type="checkbox" value="1">  Mesmo endereço do aluno' + 
                                  '<span></span>' +
                            '</label>' +
                        '</div>' +
                      '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" style="display:none;" data-address-fields="true" class="form-group parent-address-fields-' + parent_keys + '-' + classe.key + ' row parent-fields-' + parent_keys + '-' + classe.key + '" >' +
                      '<div class="col-sm-4">' +
                        '<label for="cep">CEP</label>' +
                        '<input type="text" data-key="' + parent_keys + '" data-selector=".parent-fields-' + parent_keys + '-' + classe.key + '" id="cep-parente-' + parent_keys + '-' + classe.key + '" class="form-control" name="parentes[' + parent_keys + '][endereco][cep]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-8">' +
                        '<label for="cep">Logradouro</label>' +
                        '<input type="text" class="form-control" name="parentes[' + parent_keys + '][endereco][logradouro]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" style="display:none;" data-address-fields="true" class="form-group parent-address-fields-' + parent_keys + '-' + classe.key + ' row parent-fields-' + parent_keys + '-' + classe.key + '">' +
                        '<div class="col-sm-4">' +
                          '<label for="cep">Bairro</label>' +
                          '<input type="text" class="form-control" name="parentes[' + parent_keys + '][endereco][bairro]">' +
                          '<div class="form-text"></div>' +
                        '</div>' +
                        '<div class="col-sm-4">' +
                          '<label for="cep">Cidade</label>' +
                          '<input type="text" class="form-control" name="parentes[' + parent_keys + '][endereco][cidade]">' +
                          '<div class="form-text"></div>' +
                        '</div>' +
                        '<div class="col-sm-4">' +
                          '<label for="cep">Estado</label>' +
                          '<input type="text" class="form-control" name="parentes[' + parent_keys + '][endereco][estado]">' +
                          '<div class="form-text"></div>' +
                        '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" style="display:none;" data-address-fields="true" class="form-group parent-address-fields-' + parent_keys + '-' + classe.key + ' row parent-fields-' + parent_keys + '-' + classe.key + '">' +
                        '<div class="col-sm-2">' +
                          '<label for="cep">Número</label>' +
                          '<input type="text" class="form-control" name="parentes[' + parent_keys + '][endereco][numero]">' +
                          '<div class="form-text"></div>' +
                        '</div>' +
                        '<div class="col-sm-10">' +
                          '<label for="cep">Complemento</label>' +
                          '<input type="text" class="form-control" name="parentes[' + parent_keys + '][endereco][complemento]">' +
                          '<div class="form-text"></div>' +
                        '</div>' +
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                        '<div class="col-sm-8">' +
                          '<label>Empresa que trabalha</label>' +
                          '<input type="text" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][empresa]">' +
                          '<div class="form-text"></div>' +
                        '</div>' +
                        '<div class="col-sm-4">' +
                          '<label>Ocupação</label>' +
                          '<input type="text" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][ocupacao]">' +
                          '<div class="form-text"></div>' +
                        '</div>' + 
                    '</div>' +
                    '<div data-parent-id="parentes-' + classe.key + '" class="form-group row parent-fields-' + parent_keys + '-' + classe.key + '" id="remove-parent-button-' + parent_keys + '-' + classe.key + '">' + 
                      '<div class="col-sm-12">' +
                        '<label for="cep">&nbsp;</label>' +
                        '<a class="btn btn-danger remover-parente-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + parent_keys + '">Remover parente</a>' +
                      '</div>' +
                    '</div>' + 
                      '<div data-parent-id="parentes-' + classe.key + '" style="margin-bottom:20px" class="kt-separator kt-separator--space-sm  parent-fields-' + parent_keys + '-' + classe.key + '">' + 
                    '</div>';
                $(markup).insertBefore("#parents-button-" + classe.key);
                VMasker(document.querySelector("#cpf-" + parent_keys  + "-" + classe.key)).maskPattern("999.999.999.99");
                VMasker(document.querySelector("#cep-parente-" + parent_keys + "-" + classe.key)).maskPattern("99.999-999");
                var completeAddress = e.data.completeAddress;
                $(document).on('blur', "#cep-parente-" + parent_keys  + "-" + classe.key, completeAddress);
                $('#data-nascimento-parente-' + parent_keys + '-' + classe.key).datepicker(classe.dp_config);
                VMasker($('#data-nascimento-parente-' + parent_keys + '-' + classe.key)).maskPattern("99/99/9999");
              },
           removeAddress: function(e)
              {
                var btn = $(this);
                var key = btn.data('key');
                if(btn.attr('data-id'))
                  {
                  	classe.removed.enderecos.push(btn.data('id'));
                  }
                var fields = $(".address-fields-" + key + "-" + classe.key);
                fields.remove();
              },
            removeParent: function(e)
              {
                var btn = $(this);
                var key = btn.data('key');
                if(btn.attr('data-id'))
                  {
                  	classe.removed.parentes.push(btn.data('id'));
                  }
                var fields = $(".parent-fields-" + key + "-" + classe.key);
                fields.remove();
              },
            addPhone: function()
              {
                classe.phone_keys++;
                var phone_keys = classe.phone_keys;
                var key = $(this).data('parent');
                var markup = '<div data-parent-id="parentes-' + classe.key + '" class="form-group row parent-fields-' + key + '-' + classe.key + ' phone-field-' + phone_keys + '-' + classe.key + '">' +
                  '<div class="col-sm-11">' +
                      '<label>Telefone</label>' +
                      '<input class="form-control" id="phone-' + phone_keys + '-' + classe.key + '" name="parentes[' + key + '][pessoa-parente][telefones][]" type="text" />' +
                  '</div>' +
                  '<div class="col-sm-1">' +
                      '<label>&nbsp;</label>' +
                      '<a class="btn btn-danger btn-block remover-telefone-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + phone_keys + '-' + classe.key + '">Remover</a>' +
                  '</div>' +
                '</div>';
                $(markup).insertAfter('#parents-tel-button-' + key + '-' + classe.key);
                var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
                var tel = document.querySelector('#phone-' + phone_keys + '-' + classe.key);
                VMasker(tel).maskPattern(telMask[0]);
                tel.addEventListener('input', classe.inputHandler.bind(undefined, telMask, 14), false);
              },
            removePhone: function()
              {
                var key = $(this).data('key');
                var fields = $(".phone-field-" + key);
                fields.remove();
              },
            insertStudent: function(e)
              {
                e.preventDefault();
                var form = classe.form;
                var inputs = form.find('.form-control');
                var disabled = form.find(':disabled');
                disabled.removeAttr('disabled');
                var form_data = new FormData(document.getElementById(form.attr('id')));
                form_data.append('removed', JSON.stringify(classe.removed));
                disabled.attr('disabled', 'disabled');
                $.ajax(
                  {
                    url: 'alunos/inserir-aluno',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    method: 'POST',
                    beforeSend: function()
                      {
                        inputs.removeClass('is-invalid');
                        inputs.next('.form-text').html("");
                      },
                    success: function(resposta)
                      {
                        if(resposta.success === true)
                          {
                            inputs.addClass("is-valid");
                            toastr.success('Aluno inserido com sucesso!');
                            classe.insertSuccessCallback()
                          }
                        else
                          {
                            var errorMap =
                              {
                                'pessoa-aluno' :
                                  {
                                    isMany : false,
                                    hasChild: false
                                  },
                                'enderecos': 
                                  {
                                    isMany : true,
                                    hasChild: false,
                                  },
                                'parentes':
                                  {
                                    isMany : true,
                                    hasChild: true,
                                    child:
                                      {
                                        key: 'pessoa-parente',
                                        hasChild: true,
                                        child:
                                          {
                                            key: 'endereco'
                                          }
                                      }
                                  },
                                'turmas': 
                                  {
                                    special: true
                                  }
                              };
                            var mappedFields = Object.keys(errorMap);
                            $.each(resposta.errors, function(i, item)
                              {
                                if(mappedFields.indexOf(i) === -1)
                                  {
                                  classe.showMessages("[name='" + i + "']", item);
                                  }
                                else
                                  {
                                    var rules = errorMap[i];
                                    if(typeof rules.special !== 'undefined')
                                      {
                                        var fields = classe.form.find('[name="turmas[]"]');
                                        $.each(fields, function(f, _field)
                                          {
                                            var field = $(field);
                                            if(field.val() == "")
                                              {
                                                classe.showMessages("#" + field.attr('id'), item);
                                              }
                                          });
                                      }
                                    else if(!rules.isMany)
                                      {
                                        var _break = false;
                                        $.each(item, function(ii, iitem)
                                          {
                                            if(ii == "_required")
                                              {
                                                toastr.error(iitem);
                                                return false;
                                              }
                                            else
                                              {
                                              classe.showMessages("[name='" + i + "[" + ii + "]']", iitem);
                                              }
                                          });
                                      }
                                    else
                                      {
                                        $.each(item, function(ii, iitem)
                                          {
                                            if(ii == "_required")
                                              {
                                                toastr.error(iitem);
                                                return false;
                                              }
                                            else
                                              {
                                                var tel_once = false;
                                                $.each(iitem, function(iii, iiitem)
                                                  {
                                                    classe.showMessages("[name='" + i + "[" + ii + "][" + iii + "]']", iiitem);
                                                  });
                                                if(rules.hasChild)
                                                  {
                                                    $.each(iitem[rules.child.key], function(iiii, iiiitem)
                                                      {
                                                        if((iiii === 'telefones')&&(!tel_once))
                                                          {
                                                            tel_once = true;
                                                            toastr.error('Insira ao menos um telefone para cada parente');
                                                          }

                                                        classe.showMessages("[name='" + i + "[" + ii + "][" + rules.child.key + "][" + iiii + "]']", iiiitem);
                                                      });
                                                    if(typeof rules.child.hasChild !== 'undefined')
                                                      {
                                                        $.each(iitem[rules.child.child.key], function(iiiii, iiiiitem)
                                                          {
                                                            classe.showMessages("[name='" + i + "[" + ii + "][" + rules.child.child.key + "][" + iiiii + "]']", iiiiitem);
                                                          })
                                                      }
                                                  }
                                              }
                                          });
                                      }
                                  }
                              });
                            toastr.error("Corrija os campos inválidos");
                            if(classe.form.find('.is-invalid').length > 0)
                              {
                                var first = classe.form.find('.is-invalid')[0];
                                $('html, body').animate({
                                    scrollTop: ($(first).offset().top - 90)
                                }, 600);
                              }
                          }
                      }
                  });
              },
            innerAccordions: function()
              {
                var accordion = $(this);
                var active = accordion.hasClass('active');
                setTimeout(function()
                  {
                    if(active)
                      {
                        accordion.removeClass('active');
                        classe.form.find('[data-parent-id="' + accordion.attr('id') + '"], [data-grandparent-id="' + accordion.attr('id') + '"]').slideUp();
                        classe.form.find('.accordion[data-parent-id="' + accordion.attr('id') + '"]').removeClass('active');
                      }
                    else
                      {
                        accordion.addClass('active');
                        classe.form.find('[data-parent-id="' + accordion.attr('id') + '"]:not([data-address-fields])').slideDown();
                        if(classe.form.find('[data-parent-id="' + accordion.attr('id') + '"]').find('[name$="[mesmo_endereco]"]').length > 0)
                          {
                            $.each(classe.form.find('[data-parent-id="' + accordion.attr('id') + '"], [data-grandparent-id="' + accordion.attr('id') + '"]').find('[name$="[mesmo_endereco]"]'), function(i, item)
                              {
                                var cb = $(item);
                                var address_form = $('.parent-address-fields-' + cb.data('key') + '-' + classe.key);
                                if(cb.prop('checked') === false)
                                  {
                                    address_form.slideDown();
                                  }
                              })
                          }
                      }
                  },200);
              },
            parentAddress: function()
              {
                var cb = $(this);
                var address_form = $('.parent-address-fields-' + cb.data('key') + '-' + classe.key);
                if(cb.prop('checked'))
                  {
                    address_form.slideUp();
                  }
                else
                  {
                    address_form.slideDown();
                  }
              },
            toggleInputs: function(e)
              {
                var input = $(this);
                if(!(input.attr('disabled') == "disabled"))
                  {
                    var val = input.val();
                    var name = input.attr('name');
                    var container = $('[data-parent-id="atendimento-' + classe.key + '"]');
                    var buscarServicos = e.data.buscarServicos;
                    var limparServicos = e.data.limparServicos;
                    var buscarTurmas   = e.data.buscarTurmas;
                    var limparTurmas   = e.data.limparTurmas;
                    var rules =
                      {
                        aplicarRegra: function(name)
                          {
                            var self = this;
                            var funcao = (typeof self[name] !== "undefined") ? self[name] : function(){};
                            var opcoes = (typeof self[name] !== "undefined") ? self.receberOpcoes(name, val) : null;
                            funcao(opcoes);
                          },
                        receberOpcoes: function(name, val)
                          {
                            var options = null;
                            if((name != "ano_letivo"))
                              {
                                var data =
                                {
                                  name: name,
                                  unidade: container.find('input[name="unidade"]:checked').val(),
                                  matricula: container.find('[name="matricula"]').val(),
                                  curso: container.find('[name="curso"]').val(),
                                  agrupamento: container.find('[name="agrupamento"]').val(), 
                                  nivel: container.find('[name="nivel"]').val(),
                                  turno: container.find('[name="turno"]').val(),
                                  permanencia: container.find('[name="permanencia"]').val(), 
                                  horario: container.find('[name="horario"]').val(),
                                  _csrfToken: $("[name='_csrfToken']").val(),
                                };
                                $.ajax(
                                  {
                                    url: '/alunos/buscar-opcoes',
                                    data: data,
                                    dataType: 'JSON',
                                    method: 'POST',
                                    async: false,
                                    success: function(resposta)
                                      {
                                        console.log(resposta);
                                        options = resposta;
                                      }
                                  });
                              }
                            console.log(options);
                            return options || null ;
                          },
                        ano_letivo: function()
                          {
                            if(val == "")
                              {
                                container.find('input[name="unidade"]').prop('checked', false).attr('disabled', 'disabled').addClass('disabled').trigger('change').parent('label.kt-radio').removeClass('kt-raio--solid').addClass('kt-radio--disabled');
                              }
                            else
                              {
                                container.find('input[name="unidade"]').removeAttr('disabled').removeClass('disabled').parent('label.kt-radio').addClass('kt-raio--solid').removeClass('kt-radio--disabled');
                              }
                            },
                        unidade: function(opcoes)
                          {
                            val = container.find('input[name="unidade"]:checked').val();
                            if((val == "")||(val == undefined))
                              {
                                container.find('select[name="matricula"]').addClass('disabled').attr('disabled', 'disabled').trigger('change').val("");
                                container.find('select[name="curso"]').addClass('disabled').attr('disabled', 'disabled').val("").trigger("change");
                              }
                            else
                              {
                                container.find('select[name="matricula"]').removeClass('disabled').removeAttr('disabled').trigger('change').val("");
                                var markup = "<option value=\"\">Selecione...</option>";
                                $.each(opcoes, function(i, item)
                                  {
                                    markup += "<option value=\"" + i + "\">" + item + "</option>";
                                  });
                                container.find('select[name="curso"]').removeClass('disabled').removeAttr('disabled').html(markup);
                              }
                            },
                        curso: function(opcoes)
                          {
                            if(val == "")
                              {
                                container.find('select[name="agrupamento"]').addClass('disabled').attr('disabled', 'disabled').val("").trigger("change");
                              }
                            else
                              {
                                var markup = "<option value=\"\">Selecione...</option>";
                                $.each(opcoes, function(i, item)
                                  {
                                    markup += "<option value=\"" + i + "\">" + item + "</option>";
                                  });
                                container.find('select[name="agrupamento"]').html(markup).removeClass('disabled').removeAttr('disabled');
                              }
                          },
                        agrupamento: function(opcoes)
                          {
                            var curso = container.find('select[name="curso"]').val();
                            if(val == "")
                              {
                                container.find('select[name="nivel"]').addClass('disabled').attr('disabled', 'disabled').val("").trigger("change");
                              }
                            else
                              {
                                var markup = "<option value=\"\">Selecione...</option>";
                                $.each(opcoes, function(i, item)
                                  {
                                    markup += "<option value=\"" + i + "\">" + item + "</option>";
                                  });
                                container.find('select[name="nivel"]').html(markup).removeClass('disabled').removeAttr('disabled');
                              }
                          },
                        nivel: function(opcoes)
                          {
                            if(val == "")
                              {
                                container.find('select[name="turno"]').addClass('disabled').attr('disabled', 'disabled').val("").trigger('change');
                              }
                            else
                              {
                                container.find('select[name="turno"]').removeClass('disabled').removeAttr('disabled').val("").trigger('change');
                              }
                          },
                        turno: function(opcoes)
                          {
                            if(val == "")
                              {
                                container.find('select[name="permanencia"]').html(markup).addClass('disabled').attr('disabled', 'disabled').val("").trigger('change');
                              }
                            else
                              {
                                var markup = "<option value=\"\">Selecione...</option>";
                                $.each(opcoes, function(i, item)
                                  {
                                    markup += "<option value=\"" + i + "\">" + item + "</option>";
                                  });
                                container.find('select[name="permanencia"]').html(markup).removeClass('disabled').removeAttr('disabled').val("").trigger('change');
                              }
                          },
                        permanencia: function(opcoes)
                          {
                            if(val == "")
                              {
                                container.find('select[name="horario"]').html(markup).addClass('disabled').attr('disabled', 'disabled').val("").trigger('change');
                              }
                            else
                              {
                                 var markup = "<option value=\"\">Selecione...</option>";
                                 $.each(opcoes, function(i, item)
                                   {
                                     markup += "<option value=\"" + i + "\">" + item + "</option>";
                                   });
                                container.find('select[name="horario"]').html(markup).removeClass('disabled').removeAttr('disabled').val("").trigger('change');
                              }
                          },
                      };
                    rules.aplicarRegra(name);
                    if(
                      (container.find('[name="ano_letivo"]').val() != "")&&
                      (container.find('input[name="unidade"]:checked').val() != "")&&
                      (container.find('input[name="unidade"]:checked').val() != undefined)&&
                      (container.find('[name="matricula"]').val() != "")&&
                      (container.find('[name="curso"]').val() != "")&&
                      (container.find('[name="agrupamento"]').val() != "")&&
                      (container.find('[name="nivel"]').val() != "")&&
                      (container.find('[name="turno"]').val() != "")&&
                      (container.find('[name="permanencia"]').val() != "")&&
                      (container.find('[name="horario"]').val() != "")
                    )
                      {
                        buscarServicos(buscarTurmas);
                      }
                    else
                      {
                        limparServicos();
                        limparTurmas();
                      }
                  }
              },
            buscarServicos: function(buscarTurmas)
              {
                var container = $('[data-parent-id="atendimento-' + classe.key + '"]');
                var data = 
                  {
                    ano_letivo: container.find('[name="ano_letivo"]').val(),
                    unidade: container.find('input[name="unidade"]:checked').val(),
                    matricula: container.find('[name="matricula"]').val(),
                    curso: container.find('[name="curso"]').val(),
                    agrupamento: container.find('[name="agrupamento"]').val(), 
                    nivel: container.find('[name="nivel"]').val(),
                    turno: container.find('[name="turno"]').val(),
                    permanencia: container.find('[name="permanencia"]').val(), 
                    horario: container.find('[name="horario"]').val(),
                    _csrfToken: $("[name='_csrfToken']").val(),
                    key: classe.key
                  };
                var procceed = false;
                if(classe.dados_atendimento !== null)
                  {
                    $.each(classe.dados_atendimento, function(i, item)
                      {
                        if(item != data[i])
                          {
                            procceed = true;
                          }
                      });
                  }
                else
                  {
                    procceed = true;
                  }
                if(procceed)
                  {
                    classe.dados_atendimento = data;
                    $.ajax(
                      {
                        url: '/alunos/buscar-servicos',
                        data: data,
                        dataType: 'HTML',
                        method: 'POST',
                        success: function(resposta)
                          {
                            if(resposta !== 'sem-resultados')
                              {
                                $("#servicos-" + classe.key).removeClass('active');
                                $("[data-parent-id='servicos-" + classe.key + "']").remove();
                                $(resposta).insertAfter($("#servicos-" + classe.key).next('.kt-separator'));
                                setTimeout(function()
                                  {
                                    buscarTurmas();
                                  }, 200);
                              }
                          },
                      });
                    $.ajax(
                      {
                        url: '/alunos/buscar-financeiro',
                        data: data,
                        dataType: 'HTML',
                        method: 'POST',
                        success: function(resposta)
                          {
                            if(resposta !== 'sem-resultados')
                              {
                                $("#financeiro-" + classe.key).removeClass('active');
                                $("[data-parent-id='financeiro-" + classe.key + "']").remove();
                                $(resposta).insertAfter($("#financeiro-" + classe.key).next('.kt-separator'));
                              }
                          },
                      });
                  }
              }, 
            limparServicos: function()
              {
                $("#servicos-" + classe.key).removeClass('active');
                $("[data-parent-id='servicos-" + classe.key + "']").remove();
                $("#financeiro-" + classe.key).removeClass('active');
                $("[data-parent-id='financeiro-" + classe.key + "']").remove();
              },
            buscarTurmas: function()
              {
                var container = $('[data-parent-id="atendimento-' + classe.key + '"]');
                var _data = 
                  {
                  ano_letivo: container.find('[name="ano_letivo"]').val(),
                  unidade: container.find('input[name="unidade"]:checked').val(),
                  matricula: container.find('[name="matricula"]').val(),
                  curso: container.find('[name="curso"]').val(),
                  agrupamento: container.find('[name="agrupamento"]').val(), 
                  nivel: container.find('[name="nivel"]').val(),
                  turno: container.find('[name="turno"]').val(),
                  permanencia: container.find('[name="permanencia"]').val(), 
                  horario: container.find('[name="horario"]').val(),
                  _csrfToken: $("[name='_csrfToken']").val(),
                  key: classe.key
                  };
                  var getData = function()
                    {
                      var data = _data;
                      var servicos = [];
                      $.each($('[data-parent-id="servicos-' + classe.key + '"]').find('input[type="checkbox"]'), function(i, item)
                        {
                          var $item = $(item);
                          if($item.prop('checked'))
                            {
                              servicos.push($item.val());
                            }
                        });
                      data.servicos = servicos;
                      return data;
                    };
                  $.ajax(
                    {
                      url: '/alunos/buscar-turmas',
                      data: getData(),
                      dataType: 'HTML',
                      method: 'POST',
                      success: function(resposta)
                        {
                          if(resposta !== 'sem-resultados')
                            {
                              $(".turmas-" + classe.key).remove();
                              $(resposta).insertAfter($('.campos-atendimento-' + classe.key));
                            }
                        },
                    });
              }, 
            limparTurmas: function()
              {
                $(".turmas-" + classe.key).remove();
              },
            percentageInput: function()
              {
                var val         = $(this).val();
                var valor_atual = $(this).data('valor-original');
                val = val.replace(/[^\d]+/g,'');
                if(parseInt(val) > 100)
                  {
                    val = 100;
                  }
                $(this).val(val + "%");
                $(this).prop("selectionStart", val.length);
                $(this).prop("selectionEnd", val.length);
                if(val == "")
                  {
                    val = 0;
                  }
                var valor_desconto = ((parseInt(valor_atual)*parseInt(val))/100);
                var valor_novo = (valor_atual - Math.round(valor_desconto));
                $(this).data('valor', valor_novo);
                $(this).next('.valor-financeiro').text("R$: " + number_format((valor_novo/100), 2, ",", "."));
                var total = 0;
                $.each($('[data-parent-id="financeiro-' + classe.key + '"] input'), function(i, item)
                  {
                    var data_valor = $(item).data('valor');
                    if(data_valor)
                      {
                        total = total + parseInt(data_valor);
                      }
                  });
                console.log(total);
                $("#total-financeiro-" + classe.key).text("R$: " + number_format((total/100), 2, ",", "."));
              },
            changeServicos: function()
              {
                var services = {};
                $.each($('[data-parent-id="servicos-' + classe.key + '"] input[name="servicos[]"]'), function(i, item)
                  {
                    if($(item).prop('checked'))
                      {
                        var key = $(item).val();
                        var value = ($('[data-parent-id="financeiro-' + classe.key + '"] input[name="financeiro[' + key + ']"]').length > 0) ? $('[data-parent-id="financeiro-' + classe.key + '"] input[name="financeiro[' + key + ']"]').val().replace(/[^\d]+/g,'') : "new";
                        services[key] = value;
                      }
                  });
                if(Object.keys(services).length > 0)
                  {
                    $.ajax(
                      {
                        url: '/alunos/buscar-financeiro-extra',
                        data: {_csrfToken: $("[name='_csrfToken']").val(), servicos: services, key: classe.key},
                        method: 'POST',
                        dataType: 'HTML',
                        success: function(resposta)
                          {
                            $("#financeiro-" + classe.key).removeClass('active');
                            $("[data-parent-id='financeiro-" + classe.key + "']").remove();
                            $(resposta).insertAfter($("#financeiro-" + classe.key).next('.kt-separator'));
                          }
                      });
                      $.ajax(
                        {
                          url: '/alunos/buscar-turmas-extra',
                          data: {_csrfToken: $("[name='_csrfToken']").val(), servicos: Object.keys(services), key: classe.key, aluno: classe.form.find('input[name="id"]').val(), unidade: $('[data-parent-id="atendimento-' + classe.key + '"]').find('input[name="unidade"]:checked').val()},
                          dataType: 'HTML',
                          method: 'POST',
                          success: function(resposta)
                            {
                              if(resposta !== 'sem-resultados')
                                {
                                  $(".turmas-" + classe.key).remove();
                                  $(resposta).insertAfter($('.campos-atendimento-' + classe.key));
                                }
                            },
                        });
                  }
              },
            validDay: function()
              {
                var val = $(this).val();
                if(val.charAt(0) == "0")
                  {
                    val = val.substring(1);
                  }
                if(val > 28)
                  {
                    val = 28;
                  }
                $(this).val(val);
              }
          };
      }   
    inputHandler(masks, max, event) 
      {
        var c = event.target;
        var v = c.value.replace(/\D/g, '');
        var m = c.value.length > max ? 1 : 0;
        VMasker(c).unMask();
        VMasker(c).maskPattern(masks[m]);
        c.value = VMasker.toPattern(v, masks[m]);
      }
    getAge(d1, d2)
      {
        var _months;
        var years = (d1.getMonth() > d2.getMonth()) ? ((d2.getFullYear() - d1.getFullYear()) - 1) : (d2.getFullYear() - d1.getFullYear());
        years = (years <= 0) ? 0 : years; 
        _months = years * 12;
        _months -= (d1.getMonth() > d2.getMonth()) ? (d1.getMonth() + 1) : d1.getMonth();
        _months += d2.getMonth();
        _months = _months <= 0 ? 0 : _months;
        var months = (_months % 12);
        return {months: months, years: years};
      }
    showMessages(selector, item)
      {
        var ipt = this.form.find(selector);
        var msg = "";
        $.each(item, function(rule, _msg)
          {
            msg += _msg + "</br>";
          });
        msg.slice(0, -5);
        ipt.addClass('is-invalid');
        ipt.next('.form-text').html(msg);
        ipt.next('.form-text').addClass('text-danger');
      }
  }