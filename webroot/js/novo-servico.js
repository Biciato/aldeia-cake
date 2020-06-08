var showMessages = function(selector, item)
  {
    var ipt = $("#servico-form").find(selector);
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
var servicos = 
  {
  	init: function()
  	  {
  	  	var self = this;
  	  	$(document).on('change', '[name="curso"]', self.changeCurso);
  	  	$(document).on('change', '[name="agrupamento"]', self.changeAgrupamento);
  	  	$(document).on('change', '[name="turno"]', self.changeTurno);
  	  	$(document).on('change', '[name="permanencia"]', self.changePermanencia);
        $(document).on('click', '#inserir-servico', self.inserirServico);
  	  	self.aplicarMascaras();
  	  	return this;
  	  },
  	aplicarMascaras: function()
  	  {
  	  	VMasker($('[name="valor[data_inicio]"], [name="valor[data_final]"]')).maskPattern("99/99/9999");
  	  	$('[name="valor[data_inicio]"], [name="valor[data_final]"]').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation:'auto bottom',
            locale: 'pt-BR',
            language: 'pt-BR'
          });
  	  	VMasker($('[name="valor[valor]"]')).maskMoney({
  	  	  precision: 2,
  	  	  separator: ',',
  	  	  delimiter: '.',
  	  	  unit: false,
  	  	  zeroCents: false
  	  	});
  	  },
  	changeCurso: function()
  	  {
  	  	var id = $(this).val();
  	  	var select_agrupamentos = $("[name='agrupamento']");
  	  	select_agrupamentos.val("").trigger('change');
  	  	if(id != "")
  	  	  {
  	  	  	var options = dados.cursos[id].agrupamentos;
  	  	  	var markup = "<option value=\"\">Selecione...</option>"; 
  	  	  	$.each(options, function(i, item)
  	  	  	  {
  	  	  	  	markup += "<option value=\"" + i + "\">" + item.nome + "</option>";
  	  	  	  });
  	  	  	select_agrupamentos.html(markup);
  	  	  	select_agrupamentos.removeAttr('disabled');
  	  	  	select_agrupamentos.removeClass('disabled');
  	  	  }
  	  	else
  	  	  {
  	  	  	select_agrupamentos.attr('disabled', 'disabled');
  	  	  	select_agrupamentos.addClass('disabled');
  	  	  }
  	  },
  	changeAgrupamento: function()
  	  {
  	  	var id    = $(this).val();
  	  	var curso = $('[name="curso"]').val();
  	  	var select_niveis = $('[name="nivel"]');
  	  	select_niveis.val("");
  	  	if(id != "")
  	  	  {
  	  	  	var options = dados.cursos[curso].agrupamentos[id].niveis;
  	  	  	var markup = "<option value=\"\">Selecione...</option>";
  	  	  	$.each(options, function(i, item)
  	  	  	  {
  	  	  	  	markup += "<option value=\"" + i + "\">" + item + "</option>";
  	  	  	  });
  	  		  select_niveis.html(markup);
  	  	  	select_niveis.removeAttr('disabled');
  	  	  	select_niveis.removeClass('disabled');
  	  	  }
  	  	else
  	  	  {
  	  	  	select_niveis.attr('disabled', 'disabled');
  	  	  	select_niveis.addClass('disabled');
  	  	  }
  	  },
  	changeTurno: function()
  	  {
  	  	var id = $(this).val();
  	  	var select_permanencias = $("[name='permanencia']");
  	  	select_permanencias.val("").trigger('change');
  	  	if(id != "")
  	  	  {
  	  	  	var options = dados.turnos[id].permanencias;
  	  	  	var markup = "<option value=\"\">Selecione...</option>"; 
  	  	  	$.each(options, function(i, item)
  	  	  	  {
  	  	  	  	markup += "<option value=\"" + i + "\">" + item.nome + "</option>";
  	  	  	  });
  	  	  	select_permanencias.html(markup);
  	  	  	select_permanencias.removeAttr('disabled');
  	  	  	select_permanencias.removeClass('disabled');
  	  	  }
  	  	else
  	  	  {
  	  	  	select_permanencias.attr('disabled', 'disabled');
  	  	  	select_permanencias.addClass('disabled');
  	  	  }
  	  },
  	changePermanencia: function()
  	  {
  	  	var id    = $(this).val();
  	  	var curso = $('[name="turno"]').val();
  	  	var select_horarios = $('[name="horario"]');
  	  	select_horarios.val("");
  	  	if(id != "")
  	  	  {
  	  	  	var options = dados.turnos[curso].permanencias[id].horarios;
  	  	  	var markup = "<option value=\"\">Selecione...</option>";
  	  	  	$.each(options, function(i, item)
  	  	  	  {
  	  	  	  	markup += "<option value=\"" + i + "\">" + item + "</option>";
  	  	  	  });
  	  		  select_horarios.html(markup);
  	  	  	select_horarios.removeAttr('disabled');
  	  	  	select_horarios.removeClass('disabled');
  	  	  }
  	  	else
  	  	  {
  	  	  	select_horarios.attr('disabled', 'disabled');
  	  	  	select_horarios.addClass('disabled');
  	  	  }
  	  },
    inserirServico: function(e)
      {
        e.preventDefault();
        var form = $("#servico-form");
        var inputs = form.find('.form-control');
        var form_data = new FormData(document.getElementById("servico-form"));
        $.ajax(
          {
            url: 'servicos/inserir-servico',
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
                    toastr.success('Serviço inserido com sucesso!');
                    window.location.reload();
                  }
                else
                  {
                    var errorMap =
                      {
                        'valor' :
                          {
                            isMany : false,
                            hasChild: false
                          },
                      };
                    var mappedFields = Object.keys(errorMap);
                    $.each(resposta.errors, function(i, item)
                      {
                        if(mappedFields.indexOf(i) === -1)
                          {
                          showMessages("[name='" + i + "']", item);
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
                                      showMessages("[name='" + i + "[" + ii + "]']", iitem);
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
                                            showMessages("[name='" + i + "[" + ii + "][" + iii + "]']", iiitem);
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

                                                showMessages("[name='" + i + "[" + ii + "][" + rules.child.key + "][" + iiii + "]']", iiiitem);
                                              });
                                            if(typeof rules.child.hasChild !== 'undefined')
                                              {
                                                $.each(iitem[rules.child.child.key], function(iiiii, iiiiitem)
                                                  {
                                                    showMessages("[name='" + i + "[" + ii + "][" + rules.child.child.key + "][" + iiiii + "]']", iiiiitem);
                                                  })
                                              }
                                          }
                                      }
                                  });
                              }
                          }
                      });
                    toastr.error("Corrija os campos inválidos");
                    if($("#servico-form").find('.is-invalid').length > 0)
                      {
                        var first = $("#servico-form").find('.is-invalid')[0];
                        $('html, body').animate({
                            scrollTop: ($(first).offset().top - 90)
                        }, 600);
                      }
                  }
              }
          });
      },
  };
$(document).ready(function()
  {
  	servicos.init();
  });