		var close = function _close($item)
		  {
		  	var id   = $item.attr('id');
		  	var children = $(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator");
		  	if(children.length)
		  	  {
		  	  	$.each(children, function(i, item)
		  	  		{
		  	  			_close($(item));
		  	  			$(item).slideUp();
		  	  			$(item).next('.kt-separator').slideUp();
		  	  		});
		  	  }
		  	$item.removeClass('active');
		  }
		var dp_config = 
		  {
		    format: 'dd/mm/yyyy',
		    autoclose: true,
		    orientation:'auto bottom',
		    locale: 'pt-BR',
		    language: 'pt-BR'
		  };
		var interacoes = 
		  {
		  	init: function()
		  	  {
		  	  	var self = this;
		  	  	$(document).on('click', ".accordion:not(.active)", {manageMarkup: self.manageMarkup}, self.loadNextSession);
		  	  	$(document).on('click', ".accordion.active",  self.closeSession);
		  	  	$(document).on('click', '.inserir-interacao', self.insertInteraction);
		  	  	$(document).on('click', '.remover-arquivo', self.removeFile);
		  	  	$(document).on('change', '.filter_input', self.applyFilters);
		  	  	$(document).on('click', '.clear-daterange', self.clearDateRange);
		  	  	$(document).on('click', '.apply-daterange', self.applyDateRange);
		  	  	$(document).on('keypress', '[name="primeiro_atendimento"]', function(e){e.preventDefault(); return false;});
		  	  	self.initInputs();
		  	  	return this;
		  	  },
		  	initInputs: function()
		  	  {
		  	  	var _form = $('#filtro_interacoes');
		  	  	var timeInput = _form.find("[name='hora']");
		  	  	VMasker(timeInput).maskPattern('99:99');
		  	  	$('#date_range').daterangepicker({
		  	  	    buttonClasses: ' btn',
		  	  	    applyClass: 'btn-primary apply-daterange',
		  	  	    cancelClass: 'btn-secondary clear-daterange',
		  	  	    format: 'dd/mm/yyyy',
		  	  	    autoclose: true,
		  	  	    orientation:'auto bottom',
		  	  	    locale: {
			            format: "DD/MM/YYYY",
			            separator: " - ",
			            applyLabel: "Aplicar",
			            cancelLabel: "Limpar",
			            fromLabel: "De",
			            toLabel: "Até",
			            daysOfWeek: [
			                "D",
			                "S",
			                "T",
			                "Q",
			                "Q",
			                "S",
			                "S"
			            ],
			            monthNames: [
			                "Janeiro",
			                "Fevereiro",
			                "Março",
			                "Abril",
			                "Maio",
			                "Junho",
			                "Julho",
			                "Agosto",
			                "Setembro",
			                "Outubro",
			                "Novembro",
			                "Dezembro"
			            ],
			            firstDay: 1
			        }
		  	  	}, function(start, end, label) {
		  	  	    $('#date_range .form-control').val( start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
		  	  	});
		  	  },
		  	loadNextSession: function(e)
		  	  {
		  	  	var accordion    = $(this);
		  	  	var key          = accordion.data('key');
		  	  	var parent_key   = accordion.data('parent-key');
		  	  	var id           = accordion.attr('id');
	  	  	  	if(parent_key == "")
	  	  	  	  {
	  	  	  	  	var token = $("[name='_csrfToken']").val();
		  	  	  	var manageMarkup = e.data.manageMarkup;
		  	  	  	var loaded = $(this).attr('data-loaded');
		  	  	  	if(typeof loaded === 'undefined')
	    	  	  	  {
		  	  	  	  	$.ajax(
		  	  	  	  	  {
		  	  	  	  	  	url: '/prospects/sessao-lista-interacoes',
		  	  	  	  	  	data: {key: key,  parent_key:parent_key, _csrfToken: token, id: id},
		  	  	  	  	  	dataType: 'HTML',
		  	  	  	  	  	method: 'POST',
		  	  	  	  	  	success: function(resposta)
		  	  	  	  	  	  {
		  	  	  	  	  	  	if(resposta !== 'sem-resultados')
		  	  	  	  	  	  	  {
		  	  	  	  	  	  	  	manageMarkup(key, parent_key, id, resposta);
		  	  	  	  	  	  	  }
		  	  	  	  	  	  	else
		  	  	  	  	  	  	  {
		  	  	  	  	  	  	  	toastr.warning("Não foram encontrados dados");
		  	  	  	  	  	  	  }
		  	  	  	  	  	  }
		  	  	  	  	  });
		  	  	  	  }
		  	  	  	else
		  	  	  	  {
		  	  	  	  	$(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").slideDown();
		  	  	  		accordion.addClass('active');
		  	  	  	  }
	  	  	  	  }
		  	  },
		  	closeSession: function(e)
		  	  {
		  	  	close($(this));
		  	  },
		  	manageMarkup: function( key,  parent_key, id, markup)
		  	  {
		  	  	var accordion = $("#" + id);
		  	  	var separator = accordion.next('.kt-separator');
		  	  	accordion.attr('data-loaded', 1);
		  	  	$(markup).insertAfter(separator);
		  	  	$(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").slideDown();
		  	  	accordion.addClass('active');
		  	  	setTimeout(function()
		  	  	  {
		  	  	  	var _form = $('#interactions-form-' + key);
		  	  	  	var dateInput = _form.find("[id^='interaction-date-']");
		  	  	  	var timeInput = _form.find("[id^='interaction-time-']");
		  	  	  	VMasker(dateInput).maskPattern('99/99/9999');
		  	  	  	$(dateInput).datepicker(dp_config);
		  	  	  	VMasker(timeInput).maskPattern('99:99');
		  	  	  }, 200);
		  	  },
		  	insertInteraction: function(e)
		  	  {
		  	  	e.preventDefault();
		  	  	var id = $(this).data('id');
		  	  	var form = $("#interactions-form-" + id);
		  	  	var data = form.serialize();
		  	  	$.ajax(
		  	  	  {
		  	  	  	url: '/prospects/inserir-interacao',
		  	  	  	method: 'POST',
		  	  	  	data: new FormData(document.getElementById("interactions-form-" + id)),
                    processData: false,
                    contentType: false,
		  	  	  	success: function(resposta)
		  	  	  	  {
		  	  	  	  	if(resposta.success === true)
		  	  	  	  	  {
		  	  	  	  	  	toastr.success('Interação editada com sucesso!');
		  	  	  	  	  	window.location.reload();
		  	  	  	  	  }
		  	  	  	  	else
		  	  	  	  	  {
		  	  	  	  	  	$.each(resposta.errors, function(i, item)
		  	  	              {
		  	  	                form.find("[name='" + i + "']").addClass('is-invalid');
		  	  	                var _msg = "";
		  	  	                $.each(item, function(k, msg)
		  	  	                  {
		  	  	                    _msg = msg
		  	  	                  });
		  	  	                form.find("[name='" + i + "']").next('.form-text').addClass('is-invalid').text(_msg);
		  	  	              });
		  	  	  	  	  }
		  	  	  	  },
		  	  	  	beforeSend: function()
		  	  	  	  {
		  	  	  	  	form.find('.form-control').removeClass('is-invalid');
		  	  	  	  	form.find('.form-text').text("");
		  	  	  	  }
		  	  	  });
		  	  },
		  	removeFile: function()
		  	  {
		  	  	var id = $(this).data('id');
		  	  	$("#dropdown-" + id).parent().remove();
		  	  	$("#interactions-form-" + id).append("<input type=\"hidden\" name=\"remover-arquivo\" value=\"true\" />")
		  	  },
		  	applyFilters: function()
		  	  {
		  	  	var filters   = $("#filtro_interacoes").serializeArray();
		  	  	var active    = false;
		  	  	$.each(filters, function(i, item)
		  	  	  {
		  	  	  	if((item.value != "")&&(item.name != "_csrfToken")&&(item.name != "_method"))
		  	  	  	  {
		  	  	  	  	if(typeof active === "boolean")
		  	  	  	  	  {
		  	  	  	  	  	active = {};
		  	  	  	  	  }
		  	  	  	  	active[item.name] = item.value;
		  	  	  	  }
		  	  	  });
		  	  	var filtering = 
		  	  	  {
		  	  	  	prospect: function(value, data)
		  	  	  	  {
		  	  	  	  	var checarParentes = function(v, d)
		  	  	  	  	  {
		  	  	  	  	  	var checked = false;
		  	  	  	  	  	$.each(d.prospect.parentes, function(i, parente)
		  	  	  	  	  	  {
		  	  	  	  	  	  	console.log(parente);
		  	  	  	  	  	  	var tel = parente.telefones.join(' ');
		  	  	  	  	  	  	if(
		  	  	  	  	  	  	  (parente.nome.toLowerCase().indexOf(v.toLowerCase()) > -1)
		  	  	  	  	  	  	  ||
		  	  	  	  	  	  	  (tel.indexOf(v) > -1)
		  	  	  	  	  	  	  ||
		  	  	  	  	  	  	  (parente.email.toLowerCase().indexOf(v.toLowerCase()) > -1)
		  	  	  	  	  	  	)
		  	  	  	  	  	  	  {
		  	  	  	  	  	  	  	checked = true;
		  	  	  	  	  	  	  }
		  	  	  	  	  	  });
		  	  	  	  	  	return checked;
		  	  	  	  	  }
		  	  	  	  	if(
		  	  	  	  		(data.prospect.nome.toLowerCase().indexOf(value.toLowerCase()) > -1)
		  	  	  	  		||
		  	  	  	  		(checarParentes(value, data))
		  	  	  	  	)
		  	  	  	  	  {
		  	  	  	  	  	return true;
		  	  	  	  	  }
		  	  	  	  	return false;
		  	  	  	  },
		  	  	  	unidade: function(value, data)
		  	  	  	  {
		  	  	  	  	return (data.unidade == value);
		  	  	  	  },
		  	  	  	agrupamento: function(value, data)
		  	  	  	  {
		  	  	  	  	if(!data.agrupamento)
		  	  	  	  	  {
		  	  	  	  	  	return (value == "-1"); 
		  	  	  	  	  }
		  	  	  	  	return (data.agrupamento == value);
		  	  	  	  },
		  	  	  	primeiro_atendimento: function(value, data)
		  	  	  	  {
		  	  	  	  	if(data.primeiro_atendimento)
		  	  	  	  	  {
		  	  	  	  	  	var dates = value.split(" - ");
		  	  	  	  	  	var range = 
		  	  	  	  	  	  {
		  	  	  	  	  	  	from: new Date(dates[0].split('/').reverse().join('-')),
		  	  	  	  	  	  	to:   new Date(dates[1].split('/').reverse().join('-'))
		  	  	  	  	  	  };
		  	  	  	  	  	var prospect_date = new Date(data.primeiro_atendimento);
		  	  	  	  	  	return (
		  	  	  	  	      	(prospect_date.getTime() >= range.from.getTime())
		  	  	  	  	      	&&
		  	  	  	  	      	(prospect_date.getTime() <= range.to.getTime())
		  	  	  	  	      );
		  	  	  	  	  }
		  	  	  	  	return false;
		  	  	  	  },
		  	  	  	status: function(value, data)
		  	  	  	  {
		  	  	  	  	return (data.status == value);
		  	  	  	  },
		  	  	  	responsavel: function(value, data)
		  	  	  	  {
		  	  	  	  	return (data.responsavel == value);
		  	  	  	  }
		  	  	  };
		  	  	
		  	  	$.each($(".accordion.scope-0"), function(i, item)
		  	  	  {
		  	  	  	var hide = false;
		  	  	  	var id = $(item).attr('id');
		  	  	  	if(active !== false)
		  	  	  	  {
		  	  	  	  	var data = $(item).data('filter');
		  	  	  	  	data = (typeof data === "string") ? jQuery.parseJSON(data) : data;
		  	  	  	  	console.log(data);
		  	  	  	  	console.log(active);
		  	  	  	  	$.each(active, function(filter_name, filter_value)
		  	  	  	  	  {
		  	  	  	  	  	var rule = filtering[filter_name];
		  	  	  	  	  	console.log(rule(filter_value, data));
							if(!rule(filter_value, data))
							  {
							  	hide = true;
							  }		  	  	  	  	  	
		  	  	  	  	  });
		  	  	  	  }
		  	  	  	if(hide)
		  	  	  	  {
		  	  	  	  	$("#" + id + ", #" + id + " + .kt-separator").addClass("filter-hidden");
		  	  	  	  	$(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").addClass("filter-hidden");
		  	  	  	  }
		  	  	  	else
		  	  	  	  {
		  	  	  	  	$("#" + id + ", #" + id + " + .kt-separator").removeClass("filter-hidden");
		  	  	  	  	$(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").removeClass("filter-hidden");
		  	  	  	  }
		  	  	  });
		  	  },
		  	clearDateRange: function(e)
		  	  {
		  	  	$("#date_range").find('.form-control').val('');
		  	  	$("#date_range").find('.form-control').trigger('change');
		  	  },
		  	applyDateRange: function(e)
		  	  {
		  	  	setTimeout(function()
		  	  	  {
		  	  	  	$("#date_range").find('.form-control').trigger('change');
		  	  	  }, 100);
		  	  }
		  }
		$(document).ready(function()
		  {
		  	interacoes.init();
		  });