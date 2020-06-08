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
var prospects = 
  {
  	init: function()
  	  {
  	  	var self = this;
  	  	$(document).on('click', ".accordion:not(.active, .disabled-accordion)", {manageMarkup: self.manageMarkup}, self.loadNextSession);
  	  	$(document).on('click', ".accordion.active:not(.disabled-accordion)",  self.closeSession);
  	  	$(document).on('change', ".filter_input",  self.clearAll);
  	  	$(document).on('click', '.clear-daterange', self.clearDateRange);
  	  	$(document).on('click', '.apply-daterange', self.applyDateRange);
  	  	$(document).on('keypress', '[name="primeiro_atendimento"]', function(e){e.preventDefault(); return false;});
  	  	this.initInputs();
  	  	return this;
  	  },
  	initInputs: function()
  	  {
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
  	  	var accordion          = $(this);
  	  	var key                = accordion.data('key');
  	  	var scope              = accordion.data('scope');
  	  	var parent_key         = accordion.data('parent-key');
  	  	var parent_scope       = accordion.data('parent-scope');
  	  	var id                 = accordion.attr('id');
  	  	var prospect_filter    = $(".filter_input[name='prospect']").val();
  	  	var unidade_filter     = $(".filter_input[name='unidade']").val();
  	  	var agrupamento_filter = $(".filter_input[name='agrupamento']").val();
  	  	var status_filter      = $(".filter_input[name='status']").val();
  	  	var atendimento_filter = $(".filter_input[name='primeiro_atendimento']").val();
  	  	if((scope != 5)&&(scope != 6))
  	  	  {
  	  	  	var token = $("[name='_csrfToken']").val();
  	  	  	var manageMarkup = e.data.manageMarkup;
  	  	  	var loaded = $(this).attr('data-loaded');
  	  	  	if(typeof loaded === 'undefined')
  	  	  	  {
  	  	  	  	$.ajax(
  	  	  	  	  {
  	  	  	  	  	url: '/prospects/sessao-lista-prospect',
  	  	  	  	  	data: {key: key, scope: scope, parent_key:parent_key, parent_scope: parent_scope ,_csrfToken: token, id: id, prospect_filter: prospect_filter, unidade_filter: unidade_filter, agrupamento_filter: agrupamento_filter, atendimento_filter: atendimento_filter, status_filter: status_filter},
  	  	  	  	  	dataType: 'HTML',
  	  	  	  	  	method: 'POST',
  	  	  	  	  	success: function(resposta)
  	  	  	  	  	  {
  	  	  	  	  	  	if(resposta !== 'sem-resultados')
  	  	  	  	  	  	  {
  	  	  	  	  	  	  	manageMarkup(scope, key, parent_scope, parent_key, id, resposta);
  	  	  	  	  	  	  }
  	  	  	  	  	  	else
  	  	  	  	  	  	  {
  	  	  	  	  	  	  	toastr.warning("Não foram encontrados prospects nessa categoria");
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
  	manageMarkup: function(scope, key, parent_scope, parent_key, id, markup)
  	  {
  	  	var accordion = $("#" + id);
  	  	var separator = accordion.next('.kt-separator');
  	  	accordion.attr('data-loaded', 1);
  	  	$(markup).insertAfter(separator);
  	  	$(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").slideDown();
  	  	accordion.addClass('active');
  	  	if((scope == 4)||(scope == 3))
  	  	  {
  	  	  	var data_id = $(".accordion[data-parent-id='" + id + "']").find("[id^='prospect-form-']").data('key');
  	  	  	if(typeof window["form-" + data_id] === "undefined")
  	  	  	  {
  	  	  	  	var form = new FormularioProspect(data_id, function()
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
  	  	  	  	form.prospect_form.init();
  	  	  	  	window["form-" + data_id] = form;
  	  	  	  }
  	  	  }
        setTimeout(function()
          {
            if($('.hidden-prospect-message').length > 0)
              {
                toastr.success($('.hidden-prospect-message').text());
                $('.hidden-prospect-message').remove();
              }
          },300);
  	  },
  	clearAll: function()
  	  {
  	  	var accordions = $('.accordion[data-scope="0"]');
  	  	accordions.removeClass('active');
  	  	accordions.removeAttr('data-loaded');
  	  	$(".accordion:not([data-scope='0']), .accordion:not([data-scope='0']) + .kt-separator").remove();
        toastr.success('Listagem atualizada!');
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
  	  },
    /*
    function Typer(callback)
    {
        var srcText = 'EXAMPLE ';
        var i = 0;
        var result = srcText[i];
        var interval = setInterval(function() {
            if(i == srcText.length - 1) {
                clearInterval(interval);
                callback();
                return;
            }
            i++;
            result += srcText[i].replace("\n", "<br />");
            $("#message").html(result);
        },
        100);
        return true;


    }

    function playBGM () {
        alert("Play BGM function");
        $('#bgm').get(0).play();
    }
    */
  }
$(document).ready(function()
  {
  	prospects.init();
  });