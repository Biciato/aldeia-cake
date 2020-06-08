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
		var carregarCapitulos = function()
		  	  {
		  	  	var _csrfToken = $("[name='_csrfToken']").val();
		  	  	$.ajax(
		  	  	  {
		  	  	  	url: '/documentos/carregarCapitulos',
		  	  	  	data: {_csrfToken: _csrfToken, dados: dados},
		  		dataType: 'HTML',
		  	 	method: 'POST',
		   		success: function(resposta)
		   	      {
		   	  		$("#capitulos-notification").html(resposta);
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
		  	  	var documento = dados.documento;
		  	  	var _csrfToken = $("[name='_csrfToken']").val();
		  	  	var data      = {_csrfToken: _csrfToken};
		  	  	$('.sumario').find('.item-sumario').each(function(key, item)
		  	  	  {
		  	  	  	var capitulo_id = $(item).data('id');
		  	  	  	data[key] = capitulo_id;
		  	  	  });
		  	  	$.ajax(
		  	  	  {
		  	  	  	url: '/documentos/ordenar',
		  	  	  	data: data,
		  	  	  	dataType: 'JSON',
		  	  	  	method: 'POST',
		  	  	  	success: function(resposta)
		  	  	  	  {
		  	  	  	  	if(resposta.success === true)
		  	  	  	  	  {
		  	  	  	  	  	toastr.success('Ordenação salva com sucesso!');
		  	  	  	  	  	carregarCapitulos();
		  	  	  	  	  }
		  	  	  	  	else
		  	  	  	  	  {
		  	  	  	  	  	toastr.error('Erro ao salvar a ordenação!');
		  	  	  	  	  }
		  	  	  	  }
		  	  	  });
		  	  }
		var capitulos = 
		  {
		  	init: function()
		  	  {
		  	  	var self = this;
		  	  	$(document).on('click', "#adicionar-novo-capitulo", self.portletAddChapterShow);
		  	  	$(document).on('click', "#cancelar-adicao-capitulo", self.portletAddChapterHide);
		  	  	$(document).on('click', "#cancelar-edicao", self.portletEditHide);
		  	  	$(document).on('click', "#editar-capitulo", self.portletEditShow);
		  	  	$(document).on('click', "#editar", {editChapter: self.editChapter, editDocument: self.editDocument}, self.edit);
		  	  	$(document).on('click', "#excluir-capitulo", self.showAlert);
		  	  	$(document).on('click', "#exclude", self.exclude);
		  	  	$(document).on('click', "#adicionar-capitulo", {loadChapters: self.loadChapters}, self.addChapter);
		  	  	$(document).on('click', ".accordion:not(.active)", self.loadNextSession);
		  	  	$(document).on('click', ".accordion.active",  self.closeSession);
		  	  	CKEDITOR.config.height = 1000;
		  	  	CKEDITOR.config.entities_latin = false;
		  	  	var editor = CKEDITOR.replace('editor',
		  	  	  {
		  	  		removePlugins: 'scayt,wsc,liststyle,tableselection,tabletools,tableresize,contextmenu',
      				disableNativeSpellChecker: false
      			  });
		  	  	CKFinder.setupCKEditor(editor);
		  	  	if($("#editor1").length > 0)
		  	  	  {
		  	  	  	var editor1 = CKEDITOR.replace('editor1', 
			  	  	  {
			  	  		removePlugins: 'scayt,wsc,liststyle,tableselection,tabletools,tableresize,contextmenu',
	                    disableNativeSpellChecker: false
	                  });
			  	  	CKFinder.setupCKEditor(editor1);
		  	  	  }
		  	  	if($('.sumario').length)
		  	  	  {
		  	  	  	$('.sumario').sortable(
		  	  	  	  {
		  	  	  	  	update: self.sortSummary
		  	  	  	  });
		  	  	  }
		  	  	return this;
		  	  },
		  	loadNextSession: function(e)
		  	  {
		  	  	var accordion    = $(this);
		  	  	var key          = accordion.data('key');
		  	  	var parent_key   = accordion.data('parent-key');
		  	  	var id           = accordion.attr('id');
		  	  	console.log($(this));
		  	  	$(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").slideDown();
		  	  	accordion.addClass('active');
		  	  },
		  	closeSession: function(e)
		  	  {
		  	  	close($(this));
		  	  },
		  	portletAddChapterShow: function()
		  	  {
		  	  	$("#portlet-adicionar-capitulo").removeClass('kt-hidden');
		  	  	setTimeout(function()
		  	  	  {
		  	  	  	$("#portlet-adicionar-capitulo").find('[name="nome"]').focus();
		  	  	  }, 700);
		  	  	$("#portlet-editar").addClass('kt-hidden');
		  	  	$('html, body').animate({
                    scrollTop: ($("#portlet-adicionar-capitulo").offset().top - 90)
                }, 600);
		  	  },
		  	portletEditShow: function()
		  	  {
		  	  	$("#portlet-editar").removeClass('kt-hidden');
		  	  	setTimeout(function()
		  	  	  {
		  	  	  	$("#portlet-editar").find('[name="nome"]').focus();
		  	  	  }, 700);
		  	  	$("#portlet-adicionar-capitulo").addClass('kt-hidden');
		  	  	$('html, body').animate({
                    scrollTop: ($("#portlet-editar").offset().top - 90)
                }, 600);
		  	  },
		  	portletAddChapterHide: function()
		  	  {
		  	  	$("#portlet-adicionar-capitulo").addClass('kt-hidden');
		  	  	$('html, body').animate(
		  	  		{scrollTop: 0}, 200);
		  	  },
		  	portletEditHide: function()
		  	  {
		  	  	$("#portlet-editar").addClass('kt-hidden');
		  	  	$('html, body').animate(
		  	  		{scrollTop: 0}, 200);
		  	  },
		  	addChapter: function(e)
		  	  {
		  	  	var form   = $("#form-adicionar-capitulo");
		  	  	var inputs = form.find(".form-control:not([type='hidden'])");
		  	  	var loadChapters = e.data.loadChapters;
		  	  	var data = form.serialize();
		  	  	data = data.replace('conteudo=&', 'conteudo=' + encodeURI(CKEDITOR.instances.editor.getData().replace(/&/g, '%26')) + '&');
		  	  	$.ajax(
		  	  	  {
		  	  	    url: '/documentos/adicionar-capitulo',
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
		  	  	            toastr.success('Capítulo adicionado com sucesso!');
		  	  	            setTimeout(function()
		  	  	              {
		  	  	                inputs.removeClass('is-valid').val("");
		  	  	                CKEDITOR.instances.editor.setData('');
		  	  	                $("#portlet-adicionar-capitulo").addClass('kt-hidden');
		  	  	                $('html, body').animate(
		  	  	                	{scrollTop: 0}, 200);
		  	  	              }, 500);
		  	  	            loadChapters();
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
		  	loadChapters: carregarCapitulos,
		  	showAlert: function()
		  	  {
		  	  	toastr.warning(
		  	  	"<div class=\"toastTitle\">Tem certeza de que deseja excluír esse capítulo?</div><div class=\"toastMessage\" style=\"text-align:right\"><a class=\"btn btn-danger\">Não</a>&nbsp;&nbsp;&nbsp;<a class=\"btn btn-success\" id=\"exclude\">Sim</a></div>"
		  	  	);
		  	  },
		  	exclude:function()
		  	  {
		  	  	var _csrfToken = $("[name='_csrfToken']").val();
		  	  	$.ajax(
		  	  	  {
		  	  	  	url: '/documentos/excluir',
		  	  	  	data: {_csrfToken: _csrfToken, dados: dados},
		  	  	  	dataType: 'JSON',
		  	  	  	method: 'POST',
		  	  	  	success: function(resposta)
		  	  	  	  {
		  	  	  	  	if(resposta.success === true)
		  	  	  	  	  {
		  	  	  	  	  	toastr.success('Capítulo excluído com sucesso!');
		  	  	  	  	  	setTimeout(function()
		  	  	  	  	  	  {
		  	  	  	  	  	  	window.location.href = dados.url_anterior;
		  	  	  	  	  	  }, 600);
		  	  	  	  	  }
		  	  	  	  }
		  	  	  });
		  	  },
		  	edit: function(e)
		  	  {
		  	  	var loadChapters = e.data.loadChapters;
		  	  	var _function    = (dados.tipo === 'documento') ? e.data.editDocument : e.data.editChapter;
		  	  	_function(loadChapters);
		  	  },
		  	editChapter: function(loadChapters)
		  	  {
		  	  	var form   = $("#form-editar-capitulo");
		  	  	form.find('textarea').val("");
		  	  	var inputs = form.find(".form-control:not([type='hidden'])"); 
		  	  	var data = form.serialize();
		  	  	data = data.replace('conteudo=&', 'conteudo=' + encodeURI(CKEDITOR.instances.editor1.getData().replace(/&/g, '%26')) + '&');
		  	  	$.ajax(
		  	  	  {
		  	  	    url: '/documentos/editar-capitulo',
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
		  	  	            toastr.success('Capítulo editado com sucesso!');
		  	  	            window.location.reload();
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
		  	editDocument: function(loadChapters)
		  	  {
		  	  	var form   = $("#form-editar-documento");
		  	  	var inputs = form.find(".form-control:not([type='hidden'])"); 
		  	  	var data = form.serialize();
		  	  	$.ajax(
		  	  	  {
		  	  	    url: '/documentos/editar-documento',
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
		  	  	            toastr.success('Documento editado com sucesso!');
		  	  	            setTimeout(function()
		  	  	              {
		  	  	                inputs.removeClass('is-valid').val("");
		  	  	                $("#portlet-editar").addClass('kt-hidden');
		  	  	                window.location.reload();
		  	  	              }, 500);
		  	  	            loadChapters();
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
		  	sortSummary: ordenarSumario
		  }
		$(document).ready(function()
		  {
		  	capitulos.init();
		  });