var lotes = 
  {
  	init: function()
  	  {
  	  	var self = this;
  	  	$(document).on('change', '[name="curso"]', self.changeCurso);
  	  	$(document).on('change', '[name="agrupamento"]', self.changeAgrupamento);
  	  	$(document).on('change', '[name="turno"]', self.changeTurno);
  	  	$(document).on('change', '[name="permanencia"]', self.changePermanencia);
        $(document).on('click', '#editar-lote', self.editarLote);
        $(document).on('change', '#edicao-lote-form select', self.boldOption);
  	  	self.aplicarMascaras();
  	  	return this;
  	  },
  	aplicarMascaras: function()
  	  {
  	  	VMasker($('[name="data_inicio"], [name="data_final"]')).maskPattern("99/99/9999");
  	  	$('[name="data_inicio"], [name="data_final"]').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation:'auto bottom',
            locale: 'pt-BR',
            language: 'pt-BR'
          });
  	  	VMasker($('[name="valor"]')).maskMoney({
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
  	  	if(
            (id != "")&&
            (id != "TODAS_OPCOES")
          )
  	  	  {
  	  	  	var options = dados.cursos[id].agrupamentos;
  	  	  	var markup = "<option value=\"\">Selecione...</option>"; 
  	  	  	$.each(options, function(i, item)
  	  	  	  {
  	  	  	  	markup += "<option value=\"" + i + "\">" + item.nome + "</option>";
  	  	  	  });
            markup += "<option value=\"TODAS_OPCOES\"><b>Todas as opções</b></option>";
  	  	  	select_agrupamentos.html(markup);
  	  	  	select_agrupamentos.removeAttr('disabled');
  	  	  	select_agrupamentos.removeClass('disabled');
  	  	  }
  	  	else
  	  	  {
            select_agrupamentos.val(id).trigger('change');
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
  	  	if(
            (id != "")&&
            (id != "TODAS_OPCOES")
          )
  	  	  {
  	  	  	var options = dados.cursos[curso].agrupamentos[id].niveis;
  	  	  	var markup = "<option value=\"\">Selecione...</option>";
  	  	  	$.each(options, function(i, item)
  	  	  	  {
  	  	  	  	markup += "<option value=\"" + i + "\">" + item + "</option>";
  	  	  	  });
            markup += "<option value=\"TODAS_OPCOES\"><b>Todas as opções</b></option>";
  	  		  select_niveis.html(markup);
  	  	  	select_niveis.removeAttr('disabled');
  	  	  	select_niveis.removeClass('disabled');
  	  	  }
  	  	else
  	  	  {
            select_niveis.val(id).trigger('change');
  	  	  	select_niveis.attr('disabled', 'disabled');
  	  	  	select_niveis.addClass('disabled');
  	  	  }
  	  },
  	changeTurno: function()
  	  {
  	  	var id = $(this).val();
  	  	var select_permanencias = $("[name='permanencia']");
  	  	select_permanencias.val("").trigger('change');
  	  	if(
            (id != "")&&
            (id != "TODAS_OPCOES")
          )
  	  	  {
  	  	  	var options = dados.turnos[id].permanencias;
  	  	  	var markup = "<option value=\"\">Selecione...</option>"; 
  	  	  	$.each(options, function(i, item)
  	  	  	  {
  	  	  	  	markup += "<option value=\"" + i + "\">" + item.nome + "</option>";
  	  	  	  });
            markup += "<option value=\"TODAS_OPCOES\"><b>Todas as opções</b></option>";
  	  	  	select_permanencias.html(markup);
  	  	  	select_permanencias.removeAttr('disabled');
  	  	  	select_permanencias.removeClass('disabled');
  	  	  }
  	  	else
  	  	  {
            select_permanencias.val(id).trigger('change');
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
  	  	if(
            (id != "")&&
            (id != "TODAS_OPCOES")
          )
  	  	  {
  	  	  	var options = dados.turnos[curso].permanencias[id].horarios;
  	  	  	var markup = "<option value=\"\">Selecione...</option>";
  	  	  	$.each(options, function(i, item)
  	  	  	  {
  	  	  	  	markup += "<option value=\"" + i + "\">" + item + "</option>";
  	  	  	  });
            markup += "<option value=\"TODAS_OPCOES\"><b>Todas as opções</b></option>";
  	  		  select_horarios.html(markup);
  	  	  	select_horarios.removeAttr('disabled');
  	  	  	select_horarios.removeClass('disabled');
  	  	  }
  	  	else
  	  	  {
            select_horarios.val(id).trigger('change');
  	  	  	select_horarios.attr('disabled', 'disabled');
  	  	  	select_horarios.addClass('disabled');
  	  	  }
  	  },
    boldOption: function()
      {
        if($(this).val() == 'TODAS_OPCOES')
          {
            $(this).css('font-weight', 'bold');
          }
        else
          {
            $(this).css('font-weight', 'normal');
          }
      },
    editarLote: function(e)
      {
        e.preventDefault();
        var form = $("#edicao-lote-form");
        var inputs = form.find('.form-control');
        var disabled = $("#edicao-lote-form").find(':input:disabled').removeAttr('disabled');
        var form_data =  $("#edicao-lote-form").serialize();
        var form_obj  = new URLSearchParams(form_data);
        inputs.removeClass('is-invalid');
        inputs.next('.form-text').html("");
        disabled.attr('disabled', 'disabled');
        var proccess = true;
        for(const [key, value] of form_obj.entries())
          {
            if(value == "")
              {
                $("[name='" + key + "']").addClass('is-invalid');
                $("[name='" + key + "']").next('.form-text').addClass('text-danger').html('Todos os campos devem estar preenchidos');
                proccess = false;
              }
          }
        if(proccess)
          {
            $.ajax(
              {
                url: 'servicos/editar-lote',
                data: form_data,
                dataType: 'JSON',
                method: 'POST',
                success: function(resposta)
                  {
                    if(resposta.success === true)
                      {
                        inputs.addClass("is-valid");
                        toastr.success('Os valores de ' + resposta.count + ' serviços foram atualizados');
                        window.location.reload();
                      }
                    else
                      {
                        toastr.error('Erro ao realizar as edições'); 
                      }
                  }
              });
          }
      },
  };
$(document).ready(function()
  {
  	lotes.init();
  });