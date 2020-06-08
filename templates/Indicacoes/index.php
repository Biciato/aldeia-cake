<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Faça uma indicação
			</h3>
		</div>
	</div>
	<?php echo $this->Element('form_prospects', ['data' => false, 'origem' => 2]); ?>
</div>
<?php $this->append('script');
  	echo $this->Html->script('vanilla-masker');
  ?>
<script type="text/javascript">
  var indicacao = 
    {
      init: function()
        {
          var self = this;
          $(document).on('click', "#submit-prospect-form", self.addProspect);
          self.initMasks();
          return this;
        },
      addProspect: function(e)
        {
          e.preventDefault();
          var form   = $("#prospect-form");
          var data   = form.serialize();
          var inputs = form.find(".form-control:not([type='hidden'])");
          $.ajax(
            {
              url: '/indicacoes/adicionar-indicacao',
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
                      toastr.success('Indicação enviada com sucesso!');
                      setTimeout(function()
                        {
                          inputs.removeClass('is-valid').val("");
                          $('html, body').animate(
                            {scrollTop: 0}, 200);
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
      initMasks: function()
        {
          function inputHandler(masks, max, event) {
            var c = event.target;
            var v = c.value.replace(/\D/g, '');
            var m = c.value.length > max ? 1 : 0;
            VMasker(c).unMask();
            VMasker(c).maskPattern(masks[m]);
            c.value = VMasker.toPattern(v, masks[m]);
          }
          var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
          var tel = document.querySelector('[name="telefone"]');
          VMasker(tel).maskPattern(telMask[0]);
          tel.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);
        },
    };
  $(document).ready(function()
    {
      indicacao.init();
    });
</script> 
<?php $this->end(); ?>