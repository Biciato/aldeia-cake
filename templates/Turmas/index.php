<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
			  Turmas
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
  <?php foreach($anos as $ano)
      {
        ?>
        <div class="row accordion scope-0" data-scope="0" data-key="<?php echo $ano->ano_letivo; ?>" data-parent-key="" data-parent-scope="" id="ano_letivo_<?php echo $ano->ano_letivo; ?>">
          <div class="col-sm-12">
            <h4><?php echo $ano->ano_letivo; ?><span class="end"><?php echo count($alunos_ano[$ano->ano_letivo]); ?></span></h4>
          </div>	
        </div>
        <div class="kt-separator scope-0 kt-separator--space-sm"></div>
        <?php
      }
		?>
	</div>
</div>
<?php  
    $this->append('css'); 
    echo $this->Html->css('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css');
    $this->end();
    $this->append('script');
    echo $this->Html->script('class-formulario-turma');
    echo $this->Html->script('vanilla-masker');
    echo $this->Html->script('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js');
  ?>
    <script type="text/javascript">
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
        var lista_turmas = 
          {
            init: function()
              {
                var self = this;
                $(document).on('click', ".accordion:not(.active):not(.inner-accordion)", {manageMarkup: self.manageMarkup}, self.loadNextSession);
                $(document).on('click', ".accordion.active:not(.inner-accordion)",  self.closeSession);
                $(document).on('click', ".gerar-frequencia", self.gerarFrequencia);
                return this;
              },
            loadNextSession: function(e)
              {
                var accordion    = $(this);
                var scope        = accordion.data('scope');
                if(scope != 4)
                  {
                    var key          = accordion.data('key');
                    var parent_key   = accordion.data('parent-key');
                    var id           = accordion.attr('id');
                    var token = $("[name='_csrfToken']").val();
                    var manageMarkup = e.data.manageMarkup;
                    var loaded = $(this).attr('data-loaded');
                    var non_loadable = $(this).attr('data-non-loadable');
                    if((typeof loaded === 'undefined')&&(!non_loadable))
                      {
                        $.ajax(
                          {
                            url: '/turmas/sessao-lista-turmas',
                            data: {key: key,  parent_key: parent_key, _csrfToken: token, id: id,  scope: scope},
                            dataType: 'HTML',
                            method: 'POST',
                            success: function(resposta)
                              {
                                if(resposta !== 'sem-resultados')
                                  {
                                    var carregar_form = (scope == 3);
                                    manageMarkup(key, parent_key, id, carregar_form, resposta);
                                  }
                                else
                                  {
                                    toastr.warning("Não foram encontrados dados");
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
            manageMarkup: function( key,  parent_key, id, load_form, markup)
              {
                var accordion = $("#" + id);
                var separator = accordion.next('.kt-separator');
                accordion.attr('data-loaded', 1);
                $(markup).insertAfter(separator);
                $(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").slideDown();
                accordion.addClass('active');
                var data_id = $(".accordion[data-parent-id='" + id + "']").find("[id^='aluno-form-']").data('key');
                if(
                  (load_form)&&
                  (typeof window['form-turmas-' + key] === 'undefined')
                )
                  {
                    window['form-turmas-' + key] = new FormularioTurma(key, function()
                    {
                        window.location.reload();
                    });
                    window['form-turmas-' + key].turma_form.init();
                  }
              },
            gerarFrequencia: function()
              {
                var btn = $(this);
                var turma = btn.data('turma');
                var mes = $("#mes_frequencia_" + turma).val();
                if(mes == "")
                  {
                    toastr.error('Selecione um mês para gerar a lista de frequência');
                  }
                else
                  {
                    window.open('/turmas/lista-frequencia/' + btn.data('ano') + '/' + mes + '/' + turma);
                  }
              }
          };
        $(document).ready(function()
          {
            lista_turmas.init();
          });
    </script>
  <?php
$this->end(); ?>