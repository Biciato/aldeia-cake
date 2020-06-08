$(document).ready(function()
  {
    var form = new FormularioAluno(1, function()
      {
        setTimeout(function()
          {
            window.location.reload();
          }, 500);
      });
    form.aluno_form.init();
  });