		var carregarDocumentos = function()
		  	  {
		  	  	var _csrfToken = $("[name='_csrfToken']").val();
		  	  	$.ajax(
		  	  	  {
		  	  	  	url: '/documentos/carregarDocumentos',
		  	  	  	data: {_csrfToken: _csrfToken},
		  	  	  	dataType: 'HTML',
		  	  	  	method: 'POST',
		  	  	  	success: function(resposta)
		  	  	  	  {
		  	  	  	  	$("#documentos-notification").html(resposta);
		  	  	  	  	setTimeout(function()
		  	  	  	  	  {
		  	  	  	  	  	if($('.sumario').length)
		  	  	  	  	  	  {
		  	  	  	  	  	  	$('.sumario').sortable(
		  	  	  	  	  	  	  {
		  	  	  	  	  	  	  	update: ordenarSumario
		  	  	  	  	  	  	  });
		  	  	  	  	  	  }
		  	  	  	  	  }, 200);
		  	  	  	  }
		  	  	  });
		  	  };
		var ordenarSumario    = function(event, ui)
		  	  {
		  	  	var _csrfToken = $("[name='_csrfToken']").val();
		  	  	var data      = {_csrfToken: _csrfToken};
		  	  	$('.sumario').find('.item-sumario').each(function(key, item)
		  	  	  {
		  	  	  	var documento_id = $(item).data('id');
		  	  	  	data[key] = documento_id;
		  	  	  });
		  	  	$.ajax(
		  	  	  {
		  	  	  	url: '/documentos/ordenarDocumentos',
		  	  	  	data: data,
		  	  	  	dataType: 'JSON',
		  	  	  	method: 'POST',
		  	  	  	success: function(resposta)
		  	  	  	  {
		  	  	  	  	if(resposta.success === true)
		  	  	  	  	  {
		  	  	  	  	  	toastr.success('Ordenação salva com sucesso!');
		  	  	  	  	  	carregarDocumentos();
		  	  	  	  	  }
		  	  	  	  	else
		  	  	  	  	  {
		  	  	  	  	  	toastr.error('Erro ao salvar a ordenação!');
		  	  	  	  	  }
		  	  	  	  }
		  	  	  });
		  	  }
		var documentos = 
		  {
		  	init: function()
		  	  {
		  	  	var self = this;
		  	  	$(document).on('click', "#adicionar-novo-doc", self.portletDocumentShow);
		  	  	$(document).on('click', "#cancelar-adicao-documento", self.portletDocumentHide);
		  	  	$(document).on('click', "#adicionar-documento", {loadDocuments: self.loadDocuments}, self.addDocument);
		  	  	if($('.sumario').length)
		  	  	  {
		  	  	  	$('.sumario').sortable(
		  	  	  	  {
		  	  	  	  	update: ordenarSumario
		  	  	  	  });
		  	  	  }
		  	  	return this;
		  	  },
		  	portletDocumentShow: function()
		  	  {
		  	  	$("#portlet-adicionar-documento").removeClass('kt-hidden');
		  	  	setTimeout(function()
		  	  	  {
		  	  	  	$("#portlet-adicionar-documento").find('[name="nome"]').focus();
		  	  	  }, 700);
		  	  	$('html, body').animate({
                    scrollTop: ($("#portlet-adicionar-documento").offset().top - 90)
                }, 600);
		  	  },
		  	portletDocumentHide: function()
		  	  {
		  	  	$("#portlet-adicionar-documento").addClass('kt-hidden');
		  	  	$('html, body').animate(
		  	  		{scrollTop: 0}, 200);
		  	  },
		  	addDocument: function(e)
		  	  {
		  	  	var form   = $("#form-adicionar-documento");
		  	  	var inputs = form.find(".form-control"); 
		  	  	var data = form.serialize();
		  	  	var loadDocuments = e.data.loadDocuments;
		  	  	$.ajax(
		  	  	  {
		  	  	    url: '/documentos/adicionar-documento',
		  	  	    data: data,
		  	  	    dataType: 'JSON',
		  	  	    method: 'POST',
		  	  	    beforeSend: function()
		  	  	      {
		  	  	        inputs.removeClass('is-invalid');
		  	  	      },
		  	  	    success: function(resposta)
		  	  	      {
		  	  	        if(resposta.success === true)
		  	  	          {
		  	  	            inputs.addClass("is-valid");
		  	  	            toastr.success('Documento adicionado com sucesso!');
		  	  	            setTimeout(function()
		  	  	              {
		  	  	                inputs.removeClass('is-valid').val("");
		  	  	                $("#portlet-adicionar-documento").addClass('kt-hidden');
		  	  	                $('html, body').animate(
		  	  	                	{scrollTop: 0}, 200);
		  	  	              }, 500);
		  	  	            loadDocuments();
		  	  	          }
		  	  	        else
		  	  	          {
		  	  	            $.each(resposta.errors, function(i, item)
		  	  	              {
		  	  	                form.find("[name='" + i + "']").addClass('is-invalid');
		  	  	                $.each(item, function(k, msg)
		  	  	                  {
		  	  	                    toastr.error(msg);
		  	  	                  })
		  	  	              });
		  	  	          }
		  	  	      } 
		  	  	  });
		  	  },
		  	loadDocuments: carregarDocumentos
		  }
		$(document).ready(function()
		  {
		  	documentos.init();
		  });