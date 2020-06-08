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
var servicos = 
  {
  	init: function()
  	  {
  	  	var self = this;
  	  	$(document).on('click', ".accordion:not(.active, .disabled-accordion)", {manageMarkup: self.manageMarkup}, self.loadNextSession);
  	  	$(document).on('click', ".accordion.active:not(.disabled-accordion)",  self.closeSession);
        $(document).on('click', ".inserir-valor", self.inserirValor);
  	  	return this;
  	  },
  	loadNextSession: function(e)
  	  {
  	  	var accordion          = $(this);
  	  	var key                = accordion.data('key');
  	  	var scope              = accordion.data('scope');
  	  	var parent_key         = accordion.data('parent-key');
  	  	var parent_scope       = accordion.data('parent-scope');
        var path               = accordion.data('path');
  	  	var id                 = accordion.attr('id');
  	  	if((scope != 8))
  	  	  {
  	  	  	var token = $("[name='_csrfToken']").val();
  	  	  	var manageMarkup = e.data.manageMarkup;
  	  	  	var loaded = $(this).attr('data-loaded');
  	  	  	if(typeof loaded === 'undefined')
  	  	  	  {
  	  	  	  	$.ajax(
  	  	  	  	  {
  	  	  	  	  	url: '/servicos/sessao-lista-servicos',
  	  	  	  	  	data: {key: key, scope: scope, parent_key:parent_key, parent_scope: parent_scope ,_csrfToken: token, id: id, path: path},
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
  	  	  	  	  	  	  	toastr.warning("Não foram encontrados servicos nessa categoria");
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
        setTimeout(function()
          {
            if($('.hidden-prospect-message').length > 0)
              {
                toastr.success($('.hidden-prospect-message').text());
                $('.hidden-prospect-message').remove();
              }
            $.each($("form[id^='servicos_']"), function(i, item)
              {
                var data_id = $(item).data('id');
                if((typeof window["form-" + data_id] === "undefined"))
                  {
                    var form = 
                      {
                        init: function()
                          {
                            var self = this;
                            VMasker($("#servicos_" + data_id).find('input[name="valor"]')).maskMoney({
                              precision: 2,
                              separator: ',',
                              delimiter: '.',
                              unit: false,
                              zeroCents: false
                            });
                            $("#servicos_" + data_id).find('input[name^="data_"]').datepicker({
                                format: 'dd/mm/yyyy',
                                autoclose: true,
                                orientation:'auto bottom',
                                locale: 'pt-BR',
                                language: 'pt-BR'
                              });
                            return this;
                          }
                      };
                    form.init();
                    window["form-" + id] = form;
                  }
              });
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
    inserirValor: function(e)
      {
        e.preventDefault();
        var id = $(this).data('key');
        var form = $("#servicos_" + id);
        var data = form.serialize();
        console.log(data);
        $.ajax(
          {
            url: '/servicos/inserir-valor',
            data: data,
            method: 'POST',
            dataType: 'JSON',
            beforeSend: function()
              {
                form.find('input').removeClass('is-invalid');
                form.find(".form-text").html("");
              },
            success: function(resposta)
              {
                if(resposta.success === true)
                  {
                    form.find('input').addClass("is-valid");
                    toastr.success('Novo valor inserido com sucesso!');
                    window.location.reload();
                  }
                else
                  {
                    $.each(resposta.errors, function(i, item)
                      {
                        form.find("[name='" + i + "']").addClass('is-invalid');
                        $.each(item, function(k, msg)
                          {
                            form.find("[name='" + i + "']").next('.form-text').html(msg);
                          })
                      });
                  }
              },

          });
      }
  }
$(document).ready(function()
  {
  	servicos.init();
  });