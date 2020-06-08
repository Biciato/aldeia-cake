class FormularioProspect
  {
    constructor(form_id, success_callback)
      {
        this.key              = form_id;
        this.form             = $("#prospect-form-" + form_id);
        this.address_keys     = (this.form.data('update')) ? this.form.data('enderecos') : 0;
        this.parent_keys      = (this.form.data('update')) ? this.form.data('parentes') : 0;
        this.phone_keys       = (this.form.data('update')) ? this.form.data('telefones') : 0;
        this.inseraction_keys = (this.form.data('interacoes')) ? this.form.data('interacoes') : 0;
        this.removed          =
          {
          	parentes  : [],
          	enderecos : [],
          	interacoes: []
          };
        this.dp_config     = 
          {
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation:'auto bottom',
            locale: 'pt-BR',
            language: 'pt-BR'
          };
        this.insertSuccessCallback = success_callback;
        var classe = this;
        this.prospect_form = 
          {
            init: function()
              {
                var self = this;
                $(document).on('click', "#adicionar-endereco-" +  classe.key, {completeAddress: self.completeAddress}, self.addAddress);
                $(document).on('click', "#adicionar-parente-" +  classe.key,  self.addParent);
                $(document).on('click', "#adicionar-interacao-" +  classe.key,  self.addInteraction);
                $(document).on('click', '.remover-endereco-' + classe.key, self.removeAddress);
                $(document).on('click', '.remover-parente-' + classe.key,  self.removeParent);
                $(document).on('click', '.remover-interacao-' + classe.key,  self.removeinteraction);
                $(document).on('click', '.adicionar-telefone-' + classe.key, self.addPhone);
                $(document).on('click', '.remover-telefone-' + classe.key, self.removePhone);
                $(document).on('change', '#birthdate-' +  classe.key, self.updateAge);
                $(document).on('change', '#prospect-form-' + classe.key + ' [name="irmao_ja_matriculado"]', self.showBrotherField);
                $(document).on('change', '#prospect-form-' + classe.key + ' [name="acompanhamento_sistematico"]', self.showCheckboxes);
                $(document).on('change', '#actual-input-' +  classe.key, {updateAge: self.updateAge}, self.switchBirthDate);
                $(document).on('click', "#inserir-prospect-" +  classe.key, self.insertProspect);
                $(document).on('click', ".remover-arquivo-" +  classe.key, self.removeFile);
                if($(".interaction-forms-" + classe.key).length > 0)
                  {
                    $(document).on('click', ".interaction-forms-" + classe.key, self.toggleInteractionForm);
                  }
                self.initInputs();
                if(classe.form.data('update'))
                  {
                    self.switchBirthDate({data: {updateAge: self.updateAge}});
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
                  VMasker($("[id^='interaction-date-'][id$='-" + classe.key + "']")).maskPattern("99/99/9999");
                  classe.form.find("[id^='interaction-date-'][id$='-" + classe.key + "']").datepicker(classe.dp_config);
                  VMasker($("[id^='interaction-time-'][id$='-" + classe.key + "']")).maskPattern("99:99");
                  }
                return this;
              },
            initInputs: function()
              {
                classe.form.find('#birthdate-' + classe.key + ', [name="data_primeiro_atendimento"]').datepicker(classe.dp_config);
                VMasker(classe.form.find('#birthdate-' + classe.key + ', [name="data_primeiro_atendimento"]')).maskPattern("99/99/9999");
              },
            switchBirthDate: function(e)
              {
                var updateAge = e.data.updateAge;
                setTimeout(function(){
                  if($("#actual-input-" + classe.key).prop('checked'))
                    {
                      $("#birthdate-" + classe.key).removeAttr('disabled');
                      setTimeout(function()
                        {
                          updateAge();
                        }, 200);
                    }
                  else
                    {
                      $("#birthdate-" + classe.key).attr('disabled' , 'disabled');
                       $("#idade-atual-" + classe.key + ", #idade-ano-que-vem-" + classe.key + ", #idade-esse-ano-" + classe.key).val("Não nascido");
                    }
                  }, 200);
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
                console.log(fields);
                $.ajax(
                  {
                    url: "https://api.postmon.com.br/v1/cep/" + cep,
                    type: "GET",
                    dataType: 'JSON',
                    success: function(resposta)
                      {
                        fields.find('[name="enderecos[' + key + '][cidade]"]').val(resposta.cidade);
                        fields.find('[name="enderecos[' + key + '][bairro]"]').val(resposta.bairro);
                        fields.find('[name="enderecos[' + key + '][logradouro]"]').val(resposta.logradouro);
                        fields.find('[name="enderecos[' + key + '][estado]"]').val(resposta.estado);
                        fields.find('[name="enderecos[' + key + '][numero]"]').focus();
                      },
                  }
                );
              },
            addAddress: function(e)
              {
                classe.address_keys++;
                var address_keys = classe.address_keys;
                var markup = '<div class="form-group row address-fields-' + address_keys + '-' + classe.key + '" >' +
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
                '<div class="form-group row address-fields-' + address_keys + '-' + classe.key + '">' +
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
                '<div class="form-group row address-fields-' + address_keys + '-' + classe.key + '">' +
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
                '<div class="form-group row address-fields-' + address_keys + '-' + classe.key + '">' +
                '<div class="col-sm-12">' +
                  '<label for="cep">&nbsp;</label>' +
                  '<a class="btn btn-danger remover-endereco-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + address_keys + '">Remover endereço</a>' +
                '</div>' + 
                '</div>' +
                '<div class="kt-separator kt-separator--space-sm  kt-separator--border-dashed address-fields-' + address_keys + '-' + classe.key + '">' + 
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
                var markup = '<div class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                      '<div class="col-sm-3">' +
                        '<label>Parentesco</label>' +
                        '<select class="form-control" name="parentes[' + parent_keys + '][parentesco]">' +
                          options_parentescos +
                        '</select>' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-5">' +
                        '<label>Nome</label>' +
                        '<input type="text" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][nome]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-4">' +
                        '<label>Ocupação</label>' +
                        '<input type="text" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][ocupacao]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                    '</div>' +
                    '<div class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                      '<div class="col-sm-6">' +
                        '<label>Email</label>' +
                        '<input type="text" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][email]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-5">' +
                        '<label>CPF</label>' +
                        '<input type="text" id="cpf-' + parent_keys + '-' + classe.key + '" class="form-control" name="parentes[' + parent_keys + '][pessoa-parente][cpf]">' +
                        '<div class="form-text"></div>' +
                      '</div>' +
                      '<div class="col-sm-1">' +
                        '<label>Notificações</label>' +
                        '<span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success kt-switch--lg">' +
                          '<label>' +
                            '<input type="hidden" name="parentes[' + parent_keys + '][atribuicoes]" value="">' +
                            '<input type="checkbox" name="parentes[' + parent_keys + '][atribuicoes]" value="[2]">' +
                            '<span></span>' +
                          '</label>' +
                        '</span>' +
                      '</div>' +
                    '</div>' +
                    '<div class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row">' +
                      '<div class="col-sm-12">' +
                        '<label>Telefones</label>' +
                        '<input type="hidden" name="parentes[' + parent_keys + '][pessoa-parente][telefones]" />'+
                      '</div>' +
                    '</div>' +
                    '<div class="form-group parent-fields-' + parent_keys + '-' + classe.key + ' row" id="parents-tel-button-' + parent_keys + '">' +
                      '<div class="col-sm-12">' +
                        '<label>&nbsp;</label>' +
                        '<a href="javascript:void(0)" class="btn btn-success btn-icon adicionar-telefone-' + classe.key + '" data-parent="' + parent_keys + '">' +
                          '<i class="fa fa-plus"></i>' + 
                        '</a>' +
                      '</div>' +
                    '</div>' +
                    '<div class="form-group row parent-fields-' + parent_keys + '-' + classe.key + '" id="remove-parent-button-' + parent_keys + '-' + classe.key + '">' + 
                      '<div class="col-sm-12">' +
                        '<label for="cep">&nbsp;</label>' +
                        '<a class="btn btn-danger remover-parente-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + parent_keys + '">Remover parente</a>' +
                      '</div>' +
                    '</div>' + 
                      '<div class="kt-separator kt-separator--space-sm  kt-separator--border-dashed parent-fields-' + parent_keys + '-' + classe.key + '">' + 
                    '</div>';
                $(markup).insertBefore("#parents-button-" + classe.key);
                VMasker(document.querySelector("#cpf-" + parent_keys  + "-" + classe.key)).maskPattern("999.999.999.99");
              },
            addInteraction: function(e)
              {
                classe.inseraction_keys++;
                var inseraction_keys = classe.inseraction_keys;
                var markup =  '<div class="form-group interaction-fields-' + inseraction_keys + '-' + classe.key + ' row">' +
                  '<div class="col-sm-6">' +
                    '<label>Descrição</label>' +
                    '<select type="text" class="form-control" name="interacoes[' + inseraction_keys + '][tipo]">' +
                        options_tipo_interacao +
                    '</select>' +
                    '<div class="form-text"></div>' +
                  '</div>' +
                  '<div class="col-sm-6">' +
                    '<label>Título</label>' +
                    '<input type="text" class="form-control" name="interacoes[' + inseraction_keys + '][titulo]">' +
                    '<div class="form-text"></div>' +
                  '</div>' +
                '</div>'+
                '<div class="form-group interaction-fields-' + inseraction_keys + '-' + classe.key + ' row">' +
                  '<div class="col-sm-3">' +
                    '<label>Responsável</label>' +
                    '<select type="text" class="form-control" name="interacoes[' + inseraction_keys + '][responsavel]">' +
                        options_responsaveis +
                    '</select>' +
                    '<div class="form-text"></div>' +
                  '</div>' +
                  '<div class="col-sm-3">' +
                    '<label>Arquivo</label>' +
                    '<input type="file" class="form-control" name="interacoes[' + inseraction_keys + '][arquivo]">' +
                    '<div class="form-text"></div>' +
                  '</div>' +
                  '<div class="col-sm-3">' +
                    '<label>Data</label>' +
                    '<input type="text" class="form-control" id="interaction-date-' + inseraction_keys + '-' + classe.key + '" name="interacoes[' + inseraction_keys + '][data]">' +
                    '<div class="form-text"></div>' +
                  '</div>' +
                  '<div class="col-sm-3">' +
                    '<label>Hora</label>' +
                     '<input type="text" id="interaction-time-' + inseraction_keys + '-' + classe.key + '" class="form-control" name="interacoes[' + inseraction_keys + '][hora]">' +
                     '<div class="form-text"></div>' +
                  '</div>' +
                '</div>'+
                '<div class="form-group interaction-fields-' + inseraction_keys + '-' + classe.key + ' row">' +
                  '<div class="col-sm-12">' +
                    '<label>Mensagem</label>' +
                    '<textarea style="height:150px" class="form-control" name="interacoes[' + inseraction_keys + '][mensagem]"></textarea>' +
                  '</div>' +
                '</div>'+
                '<div class="form-group interaction-fields-' + inseraction_keys + '-' + classe.key + ' row">' +
                  '<div class="col-sm-12">' +
                    '<label>Observação</label>' +
                    '<textarea style="height:150px" class="form-control" name="interacoes[' + inseraction_keys + '][observacao]"></textarea>' +
                  '</div>' +
                '</div>'+
                '<div class="form-group row interaction-fields-' + inseraction_keys + '-' + classe.key + '" id="remove-interaction-button-' + inseraction_keys + '">' + 
                  '<div class="col-sm-12">' +
                    '<label for="cep">&nbsp;</label>' +
                    '<a class="btn btn-danger remover-interacao-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + inseraction_keys + '">Remover agendamento</a>' +
                  '</div>' +
                '</div>' + 
                '<div class="kt-separator kt-separator--space-sm  kt-separator--border-dashed interaction-fields-' + inseraction_keys + '-' + classe.key + '"></div>';
                $(markup).insertBefore("#interactions-button-" + classe.key);
                $('#interaction-date-' + inseraction_keys + '-' + classe.key).datepicker(classe.dp_config);
                VMasker(document.querySelector('#interaction-date-' + inseraction_keys + '-' + classe.key)).maskPattern("99/99/9999");
                VMasker(document.querySelector('#interaction-time-' + inseraction_keys + '-' + classe.key)).maskPattern("99:99");
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
            removeinteraction: function(e)
              {
                var btn = $(this);
                var key = btn.data('key');
                if(btn.attr('data-id'))
                  {
                  	classe.removed.interacoes.push(btn.data('id'));
                  }
                var fields = $(".interaction-fields-" + key + "-" + classe.key);
                fields.remove();
                if($(".interaction-forms-" + classe.key).length > 0)
                  {
                  	$(".interaction-forms-" + classe.key + "[data-key='" + key + "']").parent().parent().remove();
                  }
              },
            addPhone: function()
              {
                classe.phone_keys++;
                var phone_keys = classe.phone_keys;
                var key = $(this).data('parent');
                var markup = '<div class="form-group row parent-fields-' + key + '-' + classe.key + ' phone-field-' + phone_keys + '-' + classe.key + '">' +
                  '<div class="col-sm-11">' +
                      '<label>Telefone</label>' +
                      '<input class="form-control" id="phone-' + phone_keys + '-' + classe.key + '" name="parentes[' + key + '][pessoa-parente][telefones][]" type="text" />' +
                  '</div>' +
                  '<div class="col-sm-1">' +
                      '<label>&nbsp;</label>' +
                      '<a class="btn btn-danger btn-block remover-telefone-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + phone_keys + '-' + classe.key + '">Remover</a>' +
                  '</div>' +
                '</div>';
                $(markup).insertBefore("#remove-parent-button-" + key + "-" + classe.key);
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
            showBrotherField: function()
              {
                var val = $(this).val();
                if(val == 1)
                  {
                    $("#brother-selector-" + classe.key + ", #brother-parents-" + classe.key).removeClass('kt-hidden');
                  }
                else
                  {
                    $("#brother-selector-" + classe.key + ", #brother-parents-" + classe.key).addClass('kt-hidden');
                  }
              },
            showCheckboxes: function()
              {
                var val = $(this).val();
                if(val == 1)
                  {
                    $("#checkboxes-" + classe.key).removeClass('kt-hidden');
                  }
                else
                  {
                    $("#checkboxes-" + classe.key).addClass('kt-hidden');
                    $.each($("#checkboxes-" + classe.key).find('input[type="checkbox"]'), function(i, item)
                      {
                        $(item).prop('checked', false);
                      });
                  }
              },
            insertProspect: function(e)
              {
                e.preventDefault();
                var form = classe.form;
                var inputs = form.find('.form-control');
                var form_data = new FormData(document.getElementById(form.attr('id')));
                form_data.append('removed', JSON.stringify(classe.removed));
                $.ajax(
                  {
                    url: 'prospects/inserir-prospect',
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
                            toastr.success('Prospect inserido com sucesso!');
                            classe.insertSuccessCallback()
                          }
                        else
                          {
                            var errorMap =
                              {
                                'pessoa-prospect' :
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
                                        key: 'pessoa-parente'
                                      }
                                  },
                                'interacoes':
                                  {
                                    isMany: true,
                                    hasChild:false
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
                                            
                                    if(!rules.isMany)
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
            removeFile: function()
              {
                var id = $(this).data('id');
                var index = $(this).data('index');
                $("#dropdown-" + id).parent().remove();
                classe.form.append("<input type=\"hidden\" name=\"interacoes[" + index + "][remover-arquivo]\" value=\"true\" />");
              },
    		  toggleInteractionForm: function()
    		    {
    		    	var widget = $(this);
    		    	var id = widget.data('key');
              var interaction_form = $("#form-interaction-" + classe.key + "-" + id);
    		    	setTimeout(function()
    		    	   {
    		    	   	if(!widget.hasClass('active'))
    		    	   	  {
    		    	   	 	  widget.addClass('active');
    		    	   	    interaction_form.css("display", "block");
                      interaction_form.next('.kt-separator').css("display", "block");
    		    	   	  }
    		    	   	else
    		    	   	  {
    		    	   	    interaction_form.css("display", "none");
                      interaction_form.next('.kt-separator').css("display", "none");
    		    	   		  widget.removeClass('active');
    		    	   	  }
    		    	   	}, 200);
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