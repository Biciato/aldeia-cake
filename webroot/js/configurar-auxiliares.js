	var auxiliar = $("[name='auxiliar']").val();
	var form = $("#aux_form");
    var addMasks = function()
      {
        $.each(form.find('[data-mask]'), function(i, item)
          {
            VMasker($(item)).maskPattern($(item).data('mask'));
            $(item).removeAttr('data-mask');
          })
      };
	var auxiliares = 
	  {
	  	init: function()
	  	  {
	  	  	var self = this;
	  	  	$(document).on('click', '#adicionar-auxiliares', self.addAux);
	  	  	$(document).on('click', '#inserir-auxiliares', self.insert);
	  	  	$(document).on('click', '.remover-auxiliar', self.removeAux);
	  	  	$("[data-switch=true]").bootstrapSwitch();
            addMasks();
            self.initOrdering();
	  	  	return this;
	  	  },
	  	initOrdering: function()
	  	  {
	  	  	$('.aux-separator-row > div.col-sm-12').sortable();
	  	  },
	  	addAux: function()
	  	  {
 	  	    aux_keys++;
  	  	    var markup = form_template.replace(/__COUNTER__/g, aux_keys);
  	  	    $(markup).insertBefore("#add-auxiliar-box");
            setTimeout(function()
              {
                addMasks();
            }, 200);
	  	  },
	    removeAux: function()
	      {
	        var key = $(this).data('key');
	        var fields = $(".aux-fields-" + key);
	        fields.remove();
	      },
	    insert: function(e)
	      {
	      	e.preventDefault();
	      	var data = form.serialize();
	      	var ordem = [];
	      	$.each($('.auxiliar-individual'), function(i, item)
	      	  {
	      	  	ordem.push($(item).data('id')); 
	      	  });
	      	data = data + "&ordem=" + ordem.join('-');
	      	$.ajax(
	      	  {
	      	  	url: '/configuracao/inserir-auxiliares',
	      	  	data: data,
	      	  	dataType: 'JSON',
	      	  	method: 'POST',
	      	  	beforeSend: function()
	      	  	  {
	      	  	    form.find('.form-control').removeClass('is-invalid');
	      	  	    form.find('.form-control, .kt-checkbox-inline').next('.form-text').html("");
	      	  	  },
	      	  	success: function(resposta)
	      	  	  {
	      	  	  	if(resposta.success === true)
	      	  	  	  {
	      	  	  	  	form.find('.form-control').addClass('is-valid');
	      	  	  	  	form.find('.kt-checkbox').removeClass('kt-checkbox--brand').addClass('kt-checkbox--success')
	      	  	  	  	setTimeout(function()
	      	  	  	  	  {
	      	  	  	  	  	window.location.reload();
	      	  	  	  	  }, 300);
	      	  	  	  }
	      	  	  	else
	      	  	  	  {
	      	  	  	  	$.each(resposta.errors, function(i, item)
	      	  	  	  	  {
	      	  	  	  	  	$.each(item, function(ii, iitem)
	      	  	  	  	  	  {
	      	  	  	  	  	  	var tgt = form.find('[name$="[' + i + '][' + ii + ']"]:not([type="hidden"])');
	      	  	  	  	  	  	tgt = (tgt.length > 0) ? tgt : form.find(".kt-checkbox-inline[data-key='" + i + "'][data-name='" + ii + "']");
	      	  	  	  	  	  	var msg = "";
	      	  	  	  	  	  	$.each(iitem, function(rule, _msg)
	      	  	  	  	  	  	  {
	      	  	  	  	  	  	    msg += _msg + "</br>";
	      	  	  	  	  	  	  });
	      	  	  	  	  	  	msg.slice(0, -5);
	      	  	  	  	  	  	tgt.addClass('is-invalid');
	      	  	  	  	  	  	tgt.next('.form-text').html(msg);
	      	  	  	  	  	  	tgt.next('.form-text').addClass('text-danger');
	      	  	  	  	  	  })
	      	  	  	  	  })
	      	  	  	  }
	      	  	  }
	      	  });
	      }
	  };
	$(document).ready(function()
	  {
	  	auxiliares.init();
	  })