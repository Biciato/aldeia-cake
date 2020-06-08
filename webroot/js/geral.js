var general = 
  {
    init: function()
      {
        var self = this;
        $(document).on('click', "#btn-logout", self.logout);
        $(document).on('click', "#btn-alterar-senha", self.mostrarModalTrocaDeSenha);
        $(document).on('click', "#alterar-senha", self.alterarSenha);
        $(document).on('click', "#notifications_menu", self.lerNotificacoes);
        self.initTooltips(self.initTooltip, $('[data-toggle="tooltip"]'));
        return this;
      },
    logout: function()
      {
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.signOut();
        window.location.href = "/login/logout";
      },
    mostrarModalTrocaDeSenha: function()
      {
        $("#modal-alterar-senha").modal('show');
      },
    alterarSenha: function()
      {
        var form   = $("#form-alterar-senha");
        var inputs = form.find(".form-control"); 
        var data = form.serialize();
        $.ajax(
          {
            url: '/dashboard/alterar-senha',
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
                    toastr.success('Senha alterada com sucesso!');
                    setTimeout(function()
                      {
                        inputs.removeClass('is-valid').val("");
                        $("#modal-alterar-senha").modal("hide")
                      }, 500);
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
    lerNotificacoes: function()
      {
        if($("#unread_count").length)
          {
            var data = 
              {
                _csrfToken: $("[name='_csrfToken']").val()
              };
            $.ajax(
              {
                url: '/dashboard/ler-notificacoes',
                dataType: 'JSON',
                data: data,
                method: 'POST'
              });
            $("#unread_count").remove();
          }
      },
    initTooltip: function(el)
      {
        var skin = el.data('skin') ? 'tooltip-' + el.data('skin') : '';
        var width = el.data('width') == 'auto' ? 'tooltop-auto-width' : '';
        var triggerValue = el.data('trigger') ? el.data('trigger') : 'hover';
        var placement = el.data('placement') ? el.data('placement') : 'left';

        el.tooltip({
            trigger: triggerValue,
            template: '<div class="tooltip ' + skin + ' ' + width + '" role="tooltip">\
                <div class="arrow"></div>\
                <div class="tooltip-inner"></div>\
            </div>'
        });
      },
    initTooltips: function(init, elements_list)
      {
        elements_list.each(
          function(i, item) {
          init($(item));
          console.log($(item));
          console.log(item);
         });
      }
  };
     


$(document).ready(function()
  {
    general.init();
  });
$(document).ajaxStart(function()
  {
    $("#full-preloader").css('display', 'block');
  });
$(document).ajaxSuccess(function()
  {
    
  });
$(document).ajaxStop(function()
  {
    $("#full-preloader").css('display', 'none');
  });