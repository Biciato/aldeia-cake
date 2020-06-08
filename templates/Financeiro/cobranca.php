<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Cobrança
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
		<?php foreach($unidades as $unidade)
		  {
		  	?>
		  	<div  class="row accordion scope-0" data-scope="0" data-key="<?php echo $unidade->id; ?>" data-parent-key="" data-loaded="1" id="unidade_<?php echo $unidade->id; ?>">
		  		<div class="col-sm-12">
		  			<h4>
		  				<?php echo $unidade->nome; ?>
		  			</h4>
		  		</div>	
		  	</div>
            <div class="kt-separator scope-0 kt-separator--space-sm"></div>            
            <div style="display:none;"  class="row accordion scope-1" data-scope="1" data-key="ativas_<?php echo $unidade->id; ?>" data-parent-id="unidade_<?php echo $unidade->id; ?>" data-parent-key="<?php echo $unidade->id; ?>" id="ativas_unidade_<?php echo $unidade->id; ?>">
		  		<div class="col-sm-12">
		  			<h4>
		  				Matriculas ativas
		  			</h4>
		  		</div>	
		  	</div>
            <div style="display:none;" class="kt-separator scope-1 kt-separator--space-sm"></div>
            <div style="display:none;"  class="row accordion scope-1" data-scope="1" data-key="inativas_<?php echo $unidade->id; ?>" data-parent-id="unidade_<?php echo $unidade->id; ?>" data-parent-key="<?php echo $unidade->id; ?>" id="inativas_unidade_<?php echo $unidade->id; ?>">
		  		<div class="col-sm-12">
		  			<h4>
		  				Matriculas inativas
		  			</h4>
		  		</div>	
		  	</div>
            <div style="display:none;" class="kt-separator scope-1 kt-separator--space-sm"></div>
		  	<?php
		  } ?>
	</div>
</div>
<div style="display:none">
<?php echo $this->Form->create(null, ['url' => false]);
echo $this->Form->end(); ?>
</div>
<?php
	$this->append('script'); 
    ?>
    <script type="text/javascript">
        function number_format(number, decimals, dec_point, thousands_point) {

        if (number == null || !isFinite(number)) {
            throw new TypeError("number is not valid");
        }

        if (!decimals) {
            var len = number.toString().split('.').length;
            decimals = len > 1 ? len : 0;
        }

        if (!dec_point) {
            dec_point = '.';
        }

        if (!thousands_point) {
            thousands_point = ',';
        }

        number = parseFloat(number).toFixed(decimals);

        number = number.replace(".", dec_point);

        var splitNum = number.split(dec_point);
        splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
        number = splitNum.join(dec_point);

        return number;
        };
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
       var cobranca = 
          {
            init: function()
              {
                var self = this;
                $(document).on('click', ".accordion:not(.active):not(.inner-accordion)", {manageMarkup: self.manageMarkup}, self.loadNextSession);
  	  	        $(document).on('click', ".accordion.active:not(.inner-accordion)",  self.closeSession);
                $(document).on('click', ".enviar-cobranca", self.enviarCobranca);
                return this;
              },
            loadNextSession: function(e)
              {
                var accordion    = $(this);
                var key          = accordion.data('key');
                var parent_key   = accordion.data('parent-key');
                var id           = accordion.attr('id');
                var scope        = accordion.data('scope');
                var token = $("[name='_csrfToken']").val();
                var manageMarkup = e.data.manageMarkup;
                var loaded = $(this).attr('data-loaded');
                var non_loadable = $(this).attr('data-non-loadable');
                if((typeof loaded === 'undefined')&&(!non_loadable))
                  {
                    $.ajax(
                      {
                        url: '/financeiro/sessao-lista-cobranca',
                        data: {key: key,  parent_key: parent_key, _csrfToken: token, id: id,  scope: scope},
                        dataType: 'HTML',
                        method: 'POST',
                        success: function(resposta)
                          {
                            if(resposta !== 'sem-resultados')
                              {
                                manageMarkup(key, parent_key, id, resposta);
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
                },
            closeSession: function(e)
              {
                close($(this));
              },
            manageMarkup: function( key,  parent_key, id, markup)
              {
                var accordion = $("#" + id);
                var separator = accordion.next('.kt-separator');
                accordion.attr('data-loaded', 1);
                $(markup).insertAfter(separator);
                $(".accordion[data-parent-id='" + id + "'], .accordion[data-parent-id='" + id + "'] + .kt-separator").slideDown();
                accordion.addClass('active');
                var data_id = $(".accordion[data-parent-id='" + id + "']").find("[id^='aluno-form-']").data('key');
              },
            enviarCobranca: function()
              {
                var aluno = $(this).data('aluno');
                var token = $("[name='_csrfToken']").val();
                var data = 
                  {
                    aluno: aluno,
                    _csrfToken: token,
                  };
                $.ajax(
                  {
                    url: '/financeiro/enviar-cobranca',
                    data: data,
                    dataType: 'JSON',
                    method: 'POST',
                    success: function(resposta)
                      {
                        if(resposta.success === true)
                          {
                            $.ajax(
                              {
                                url: '/financeiro/atualizar-tabela-cobrancas',
                                data: data,
                                datatype: 'HTML',
                                method: 'POST',
                                success: function(resp)
                                  {
                                    toastr.success('Cobrança enviada com sucesso!');
                                    $("#cobrancas-enviadas-aluno-" + aluno).html(resp);
                                  }
                              })
                          }
                      }
                  })
              }
          };
        $(document).ready(function()
          {
            cobranca.init();
          });
    </script>
    <?php
$this->end(); ?>
