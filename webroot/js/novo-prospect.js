$(document).ready(function()
  {
    var form = new FormularioProspect(1, function()
      {
        setTimeout(function()
          {
            window.location.reload();
          }, 500);
      });
    form.prospect_form.init();
  });