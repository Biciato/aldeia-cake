$(document).ready(function()
  {
    var form = new FormularioColaborador(1, function()
      {
        setTimeout(function()
          {
            window.location.reload();
          }, 500);
      });
    form.colaborador_form.init();
  });