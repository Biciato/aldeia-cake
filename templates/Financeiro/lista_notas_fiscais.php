<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Lotes de notas fiscais
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
        <div style="display:none;">
            <?php echo $this->Form->create(null); echo $this->Form->end(); ?>
        </div>	
		<?php foreach($unidades as $unidade)
		  {
		  	?>
		  	<div  class="row accordion scope-0" data-scope="0" data-key="<?php echo $unidade->id; ?>" data-parent-key=""  id="unidade_<?php echo $unidade->id; ?>">
		  		<div class="col-sm-12">
		  			<h4>
		  				<?php echo $unidade->nome; ?>
		  			</h4>
		  		</div>	
		  	</div>
            <div class="kt-separator scope-0 kt-separator--space-sm"></div>
		  	<?php
		  } ?>
	</div>
</div>
<div style="display:none">
<?php echo $this->Form->create(null, ['url' => false]);
echo $this->Form->end(); ?>
</div>
<?php $this->append('script');
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
        var notas_fiscais = 
          {
            init: function()
              {
                var self = this;
                $(document).on('click', ".accordion:not(.active):not(.inner-accordion)", {manageMarkup: self.manageMarkup}, self.loadNextSession);
  	  	        $(document).on('click', ".accordion.active:not(.inner-accordion)",  self.closeSession);
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
                        url: '/financeiro/sessao-lista-notas',
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
          };
        $(document).ready(function()
          {
            notas_fiscais.init();
          });
    </script>
    <?php
$this->end(); 
?>