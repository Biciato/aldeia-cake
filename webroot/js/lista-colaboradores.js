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
		  	  	$(document).on('click', ".accordion:not(.active):not([data-avatar='true'])", {manageMarkup: self.manageMarkup}, self.loadNextSession);
		  	  	$(document).on('click', ".accordion.active:not([data-avatar='true'])",  self.closeSession);
		  	  	$(document).on('click', '#do-search', self.applyFilters);
		  	  	$(document).on('submit', '#search-topbar-form', function(e){e.preventDefault(); $('#do-search').trigger('click');})
		  	  	return this;
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
		  	  	  	  	  	url: '/colaboradores/sessao-lista-colaboradores',
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
		  	applyFilters: function()
		  	  {
		  	  	var termo     = $("#search-term").val();
		  	  	$.each($(".accordion.scope-0"), function(i, item)
		  	  	  {
		  	  	  	var hide = false;
		  	  	  	var id = $(item).attr('id');
		  	  	  	var data = $(item).data('filter');
		  	  	  	data = (typeof data === "string") ? jQuery.parseJSON(data) : data;
		  	  	  	console.log(termo);
		  	  	  	console.log(data);
		  	  	  	if(termo == "")
		  	  	  	  {
		  	  	  	  	hide = false;
		  	  	  	  }
		  	  	  	else
		  	  	  	  {
		  	  	  	  	var tel_conds = (termo.replace(/\D/g, "") != "") ? (data.telefones.indexOf(termo.replace(/\D/g, "")) > -1) : false;
		  	  	  	  	hide = (!((data.nome.indexOf(termo.toLowerCase()) > -1)||(data.email.indexOf(termo.toLowerCase()) > -1)||tel_conds));
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
		  	  	var data_id = $(".accordion[data-parent-id='" + id + "']").find("[id^='colaborador-form-']").data('key');
		  	  	if(typeof window["form-" + data_id] === "undefined")
		  	  	  {
		  	  	  	var form = new FormularioColaborador(data_id, function()
		  	  	  	  {
		  	  	  	  	accordion.removeAttr('data-loaded');
		  	  	  	  	$(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").slideUp();
		  	  	  	  	setTimeout(function()
		  	  	  	  	  {
		  	  	  	  	  	$(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").remove();
		  	  	  	  	  	window.location.reload();
		  	  	  	  	  },200);
		  	  	  	  	accordion.removeClass('active');
		  	  	  	  });
		  	  	  	form.colaborador_form.init();
		  	  	  	window["form-" + data_id] = form;
		  	  	  }
		  	  },
		  }
		$(document).ready(function()
		  {
		  	interacoes.init();
		  });