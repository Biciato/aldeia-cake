class FormularioColaborador
  {
    constructor(form_id, success_callback)
      {
        this.key               = form_id;
        this.form              = $("#colaborador-form-" + form_id);
        this.address_keys      = (this.form.data('update')) ? this.form.data('enderecos') : 0;
        this.phone_keys        = (this.form.data('update')) ? this.form.data('telefones') : 0;
        this.children_data     = (this.form.data('update')) ? this.loadedChildrenData("#colaborador-form-" + form_id) : [];
        this.children_template = this.childrenTemplate(this.key);
        this.removed           =
          {
          	enderecos : []
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
        this.$image = $('#croppable-' + this.key);
        this.$cropControl = $(".cropper-controls-" + this.key);
        this.$inputImage =  $("#inputImage-" + this.key);
        this.croppedImageURL = "";
        this.uploadedImageURL = "";
        this.uploadedImageName = "";
        this.uploadedImageType = "";
        this.cropper_options = 
          {
            cropBoxResizable:true,
            viewMode: 0,
            cropBoxMovable: true,
            dragMode: 'move',
            aspectRatio: 1
          };
        this.cropper_start = false;
        this.image_data = false;
        var classe = this;
        this.colaborador_form = 
          {
            init: function()
              {
                var self = this;
                
                $(document).on('click', "#adicionar-endereco-" +  classe.key, {completeAddress: self.completeAddress}, self.addAddress);
                $(document).on('click', '.remover-endereco-' + classe.key, self.removeAddress);
                $(document).on('click', '.adicionar-telefone-' + classe.key, self.addPhone);
                $(document).on('click', '.remover-telefone-' + classe.key, self.removePhone);
                $(document).on('click', "#inserir-colaborador-" +  classe.key, self.inserirColaborador);
                $(document).on('change', "#filhos-" + classe.key, self.changeChildren);
                $(document).on('mousedown', '.modulos-checkbox-' + classe.key, self.setLandingPage);
                $(document).on('contextmenu', '.modulos-checkbox-' + classe.key, function(e){return false;});
                $(document).on('click', '.cropper-upload-' + classe.key, function(e){e.preventDefault(); classe.$inputImage.trigger('click')});
                $(document).on('change', "#inputImage-" + classe.key, self.changeCroppedImg);
                $(document).on('click',".cropper-controls-" + classe.key + ":not(.disabled)", self.croppingFunctions);
                $(document).on('click', "#avatar_" + classe.key, self.switchAvatar);

                //$(document).on('blur', '.backend-check-' + classe.key, self.backendCheck);
                $(document).on('ready', '#croppable-' + classe.key, function()
                  {
                    if(!classe.cropper_start)
                      {
                        classe.$image.cropper('setCropBoxData', {left:0, height:0, width:500, height:500});
                        $("#cropper-controls-box-" + classe.key).fadeIn();
                        classe.cropper_start = true;
                      }
                    if($("#croppable-" + classe.key).attr('src') === "/img/branco.gif")
                      {
                        $(".cropper-controls-" + classe.key + "[data-method='confirm']").addClass('disabled');
                      }
                    else
                      {
                        $(".cropper-controls-" + classe.key + "[data-method='confirm']").removeClass('disabled');
                      }
                  });
                self.initInputs();
                if(classe.form.data('update'))
                  {
                    if(typeof general !== 'undefined')
                      {
                        general.initTooltips(general.initTooltip, classe.form.find('[data-toggle="tooltip"]'));
                      }
                    var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
                    var tel = document.querySelectorAll("[id^='phone-'][id$='-" + classe.key + "']");
                    $.each(tel, function(t, _tel)
                      {
                        VMasker(_tel).maskPattern(telMask[0]);
                        _tel.addEventListener('input', classe.inputHandler.bind(undefined, telMask, 14), false);
                      });
                    VMasker($("[id^='cep-'][id$='-" + classe.key + "']")).maskPattern("99.999-999");
                    if(classe.form.find('[name="login[landing_page]"]').val() != "")
                      {
                        console.log(classe.form.find('[name="login[landing_page]"]').val());
                        classe.form.find('[name="login[modulos_acesso][]"][value="' + classe.form.find('[name="login[landing_page]"]').val() + '"]').prop('indeterminate', true);
                      }
                    var completeAddress = self.completeAddress;
                    $(document).on('blur', "[id^='cep-'][id$='-" + classe.key + "']", completeAddress);
                    VMasker($(".colaborador-child-" + classe.key).find('[name$="[data_nascimento]"]')).maskPattern('99/99/9999');
                    $(".colaborador-child-" + classe.key).find('[name$="[data_nascimento]"]').datepicker(classe.dp_config);
                    $("#filhos-" + classe.key).trigger('change');
                  }
                return this;
              },
            initInputs: function()
              {
                classe.form.find('[name^="data_"], [name^="pessoa-colaborador[data_"]').datepicker(classe.dp_config);
                VMasker(classe.form.find('[name^="data_"], [name^="pessoa-colaborador[data_"]')).maskPattern("99/99/9999");
                VMasker(classe.form.find('[name^="horario_"]')).maskPattern("99:99");
                VMasker(classe.form.find('[name="pessoa-colaborador[cpf]"]')).maskPattern('999.999.999-99');
                VMasker(classe.form.find('[name="pessoa-colaborador[rg]"]')).maskPattern('99.999.999-9');
                VMasker($('[name="salario_base"], [name="vale_transporte"]')).maskMoney({
                  precision: 2,
                  separator: ',',
                  delimiter: '.',
                  unit: false,
                  zeroCents: false
                });
              },
            croppingFunctions: function()
              {
                var btn    = $(this);
                var method = btn.data('method');
                var opt    = btn.data('option');
                var s_opt  = btn.data('second-option');
                if(
                  (
                    method == 'zoom'
                  )
                  ||
                  (
                    method == 'scaleX'
                  )
                  ||
                  (
                    method == 'scaleY'
                  )
                  ||
                  (
                    method == 'rotate'
                  )
                )
                  {
                    classe.$image.cropper(method, opt);
                    if((method == "scaleX")||(method == "scaleY"))
                      {
                        btn.data('option', (parseInt(opt)*-1));
                      } 
                  }
                else if(method == 'move')
                {
                  classe.$image.cropper(method, opt, s_opt);
                }
                else if(method == 'confirm')
                {
                  var dataURL = classe.$image.cropper('getCroppedCanvas').toDataURL();
                  var blobBin = atob(dataURL.split(',')[1]);
                  var array = [];
                  for(var i = 0; i < blobBin.length; i++) {
                      array.push(blobBin.charCodeAt(i));
                  }
                  var type = dataURL.substring(
                      dataURL.lastIndexOf(":") + 1, 
                      dataURL.lastIndexOf(";")
                  );
                  var file = new Blob([new Uint8Array(array)], {type: type});
                  classe.image_data = file;
                  if(classe.form.find('[name="remover-avatar"]').length)
                    {
                       classe.form.find('[name="remover-avatar"]').remove()
                    }
                  if(classe.croppedImageURL != "")
                    {
                      URL.revokeObjectURL(classe.croppedImageURL);
                    }
                  classe.cropper_start = false;
                  classe.croppedImageURL = URL.createObjectURL(file);
                  classe.$image.cropper('destroy').attr('src', classe.croppedImageURL).cropper(classe.cropper_options);
                  toastr.success('Imagem cortada com sucesso!');
                }
                else if(method == 'remove')
                  {
                    classe.uploadedImageName = "";
                    classe.uploadedImageURL  = "";
                    classe.uploadedImageType = "";
                    classe.$image.cropper('destroy').attr('src', '/img/branco.gif').cropper(classe.cropper_options);
                    classe.$inputImage.val('');
                    classe.form.append("<input type=\"hidden\" name=\"remover-avatar\" value=\"true\" />");
                    classe.image_data = false;
                  }
              },
            changeCroppedImg: function (e) {
              var files = this.files;
              var file;
              if (!classe.$image.data('cropper'))
                {
                  return;
                }
              if (files && files.length) {
                file = files[0];
                if (/^image\/\w+$/.test(file.type)) {
                  classe.uploadedImageName = file.name;
                  classe.uploadedImageType = file.type;
                  if (classe.uploadedImageURL) {
                    URL.revokeObjectURL(classe.uploadedImageURL);
                       }
                         classe.uploadedImageURL = URL.createObjectURL(file);
                         classe.$image.cropper('destroy').attr('src', classe.uploadedImageURL).cropper(classe.cropper_options);
                         classe.$inputImage.val('');                        
                       } else {
                        toastr.error("Por favor selecione uma imagem válida")
                      }
                  }
                },
            backendCheck: function()
              {
                var ipt = $(this);
                var data = 
                  {
                    _csrfToken: classe.form.find('[name="_csrfToken"]').val(),
                    field: ipt.data('field'),
                    val: field.val()
                  };
                $.ajax(
                  {
                    url: '/colaboradores/checar-duplicados',
                    data: data,
                    dataType: 'JSON',
                    method: 'POST',
                    success: function(resposta)
                      {
                        if(resposta.success === false)
                          {
                            ipt.addClass('is-invalid');
                            ipt.next('.form-text').text(msg);
                          }
                        else
                          {
                            ipt.removeClass('is-invalid');
                            ipt.next('.form-text').text(msg);
                          }
                      }
                  });
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
            changeChildren: function()
              {
                var field = $(this);
                var val = parseInt(field.val());
                var existing = classe.children_data;
                var _template = classe.children_template;
                $(".colaborador-child-" + classe.key).remove();
                var markup = "";
                var x;
                for(x = 0; x < val; x++)
                  {
                    var template = _template;
                    var data = (typeof existing[x] === "undefined") ? {beneficiario: "", nome: "", data_nascimento: ""} : existing[x];
                    var _selected = function()
                      {
                        var selecteds = ['', '', ''];
                        if(data.beneficiario == '')
                          {
                            return selecteds;
                          }
                        selecteds[parseInt(data.beneficiario) + 1] = 'selected="selected"';
                        return selecteds 
                      }
                    var selected = _selected();
                    template = template.replace(/__NOME__/, data.nome).replace(/__CHILD_KEY__/g, x).replace(/__DATA_NASCIMENTO__/, data.data_nascimento).replace(/__SELECTED_0__/, selected[0]).replace(/__SELECTED_1__/, selected[1]).replace(/__SELECTED_2__/, selected[2]);
                    markup = markup + template;
                  }
                $(markup).insertAfter(field.parent().parent('.row'));
                setTimeout(function()
                  {
                    VMasker($(".colaborador-child-" + classe.key).find('[name$="[data_nascimento]"]')).maskPattern('99/99/9999');
                    $(".colaborador-child-" + classe.key).find('[name$="[data_nascimento]"]').datepicker(classe.dp_config);
                  }, 50);
                console.log(markup);
              },
            addPhone: function()
              {
                classe.phone_keys++;
                var phone_keys = classe.phone_keys;
                var markup = '<div class="form-group row phone-field-' + phone_keys + '-' + classe.key + '">' +
                  '<div class="col-sm-11">' +
                      '<label>Telefone</label>' +
                      '<input class="form-control" id="phone-' + phone_keys + '-' + classe.key + '" name="pessoa-colaborador[telefones][]" type="text" />' +
                  '</div>' +
                  '<div class="col-sm-1">' +
                      '<label>&nbsp;</label>' +
                      '<a class="btn btn-danger btn-block remover-telefone-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + phone_keys + '-' + classe.key + '">Remover</a>' +
                  '</div>' +
                '</div>';
                $(markup).insertBefore("#tel-button-" + classe.key);
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
            inserirColaborador: function(e)
              {
                e.preventDefault();

                var form = classe.form;
                var inputs = form.find('.form-control');
                var form_data = new FormData(document.getElementById(form.attr('id')));
                form_data.append('removed', JSON.stringify(classe.removed));
                var file = classe.image_data;
                if(file)
                  {
                    form_data.set('avatar', file);
                  }
                $.ajax(
                  {
                    url: 'colaboradores/inserir-colaborador',
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
                            toastr.success('Colaborador inserido com sucesso!');
                            classe.insertSuccessCallback()
                          }
                        else
                          {
                            var errorMap =
                              {
                                'pessoa-colaborador' :
                                  {
                                    isMany : false,
                                    hasChild: false
                                  },
                                'enderecos': 
                                  {
                                    isMany : true,
                                    hasChild: false,
                                  },
                                'login': 
                                  {
                                    isMany : false,
                                    hasChild: false,
                                  },
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
            switchAvatar: function()
              {
                var accordion = $(this);
                var active = accordion.hasClass('active');
                if(active)
                  {
                    accordion.removeClass('active');
                    $("[data-parent-id='" + accordion.attr('id') + "']").slideUp();
                  }
                else
                  {
                    accordion.addClass('active');
                    $("[data-parent-id='" + accordion.attr('id') + "']").slideDown();
                    if(classe.cropper_start === false)
                      {
                        classe.$image.cropper(classe.cropper_options);
                      }
                  }
              },
            setLandingPage: function(e)
              {
                if(e.button == 2)
                  {
                    var cb = $(this).find('input[type="checkbox"]');
                    $(".modulos-checkbox-" + classe.key).find('input[type="checkbox"]').prop('indeterminate', false);
                    cb.prop('checked', true);
                    cb.prop('indeterminate', true);
                    classe.form.find('[name="login[landing_page]"]').val(cb.val());
                  }
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
    childrenTemplate(key)
      {
        var template = '<div class="form-group row colaborador-child-' + key + ' colaborador-child-' + key + '-__CHILD_KEY__">' +
          '<div class="col-sm-4">' +
             '<label for="dados_filhos[__CHILD_KEY__][beneficiario]">Beneficiário?</label>' +
             '<select type="text" class="form-control" name="dados_filhos[__CHILD_KEY__][beneficiario]">' +
                '<option __SELECTED_0__ value="">Selecione...</option>' +
                '<option __SELECTED_1__ value="0">Não</option>' +
                '<option __SELECTED_2__ value="1">Sim</option>' +
             '</select>' +
             '<div class="form-text"></div>' +
          '</div>' + 
          '<div class="col-sm-4">' +
             '<label for="dados_filhos[__CHILD_KEY__][nome]">Nome</label>' +
             '<input value="__NOME__" type="text" class="form-control" name="dados_filhos[__CHILD_KEY__][nome]">' +
             '<div class="form-text"></div>' +
          '</div>' + 
          '<div class="col-sm-4">' +
             '<label for="dados_filhos[__CHILD_KEY__][data_nascimento]">Data de nascimento</label>' +
             '<input value="__DATA_NASCIMENTO__" type="text" class="form-control" name="dados_filhos[__CHILD_KEY__][data_nascimento]">' +
             '<div class="form-text"></div>' +
          '</div>' + 
       '</div>'; 
        return template;
      }
    loadedChildrenData(form_id)
      {
        var data = $(form_id).find('.old-children-data').val();
        return $.parseJSON(data);
      }
  }