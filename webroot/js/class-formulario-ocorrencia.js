class FormularioOcorrencia
{
    constructor(form_id, success_callback, lista_pessoas, lista_tags)
      {
        this.key                = form_id;
        this.form               = $("#ocorrencia-form-" + form_id);
        this.insertSuccessCallback = success_callback;
        var classe = this;
        this.ocorrencia_form = 
          {
            init: function()
              {
                var self = this;
                $(document).on('click', '#ocorrencia-form-' + classe.key + ' .btn[data-tipo]', self.trocaTipo);
                $(document).on('click', '#nova-ocorrencia-' + classe.key, self.inserirOcorrencia);    
                self.ativarMencoes("#ocorrencia-form-" + classe.key + " textarea[name='texto']");
                return this;
              },
            ativarMencoes: function(selector)
              {
                $(selector).atwho(
                  {
                    at: "@",
                    acceptSpaceBar: true,
                    data: lista_pessoas
                  });
                $(selector).atwho(
                  {
                    at: "#",
                    acceptSpaceBar: true,
                    data: lista_tags
                  });
              },
            inserirOcorrencia: function(e)
              {
                e.preventDefault();
                $.ajax(
                  {
                    url: '/ocorrencias/inserir',
                    data: new FormData(document.getElementById("ocorrencia-form-" + classe.key)),
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    success: function(resposta)
                      {
                        if(resposta.success === true)
                          {
                            success_callback();
                          }
                        else
                          {
                            toastr.error(resposta.mensagem);
                          }
                      }
                  })
              },
            trocaTipo: function()
              {
                var tipo = $(this).data('tipo');
                $("input[name='tipo']").val(tipo);
                $('.btn[data-tipo]').removeClass('active');
                $('.btn[data-tipo="' + tipo + '"]').addClass('active');
              },
          };
      }   
}