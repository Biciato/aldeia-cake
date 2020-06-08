<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Gerar notas fiscais
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body" id="content-wrapper">	
      	<div class="row" id="nf-row">
            <div class="col-sm-12">
                <p>Clique no botão abaixo para gerar os RPS</p>
            </div>
        </div>
        <div class="row" style="margin-top:20px">
            <div class="col-sm-12" style="text-align:right; justify-content: flex-end">
                <button class="btn btn-success" style="margin-bottom: 25px;" id="gerar-rps">Gerar RPS</button>
            </div>
        </div>
	</div>
    <div style="display:none">
        <?php echo $this->Form->create(null, ['url' => false]); echo $this->Form->end(); ?>
    </div>
</div>
<?php
	$this->append('script'); 
    ?>
    <script type="text/javascript">
    	var notas_fiscais = 
          {
            init: function()
              {
                var self = this;
                $(document).on('click', '#gerar-rps', self.gerarRPS);
                $(document).on('click', '#enviar-rps', self.enviarLotes)
                return this;
              },
            gerarRPS: function()
              {
                var token = $("[name='_csrfToken']").val();
                $.ajax(
                  {
                    url: '/financeiro/gerar-rps',
                    data: {_csrfToken: token},
                    dataType: 'HTML',
                    method: 'POST',
                    success: function(resposta)
                      {
                        $("#content-wrapper").html(resposta);
                      }
                  });
              },
            enviarLotes: function()
              {
                var token = $("[name='_csrfToken']").val();
                $.ajax(
                  {
                    url: '/financeiro/enviar-lotes',
                    data: {_csrfToken: token},
                    dataType: 'JSON',
                    method: 'POST',
                    success: function(resposta)
                      {
                        if(resposta.success === true)
                          {
                            toastr.success('Lotes enviados com sucesso!');
                            window.location.reload();
                          }
                        else
                          {
                            toastr.error('Erro ao enviar os lotes');
                          }
                      }
                  });
              }
          };
    	$(document).ready(function()
    	  {
              notas_fiscais.init();
    	  });
    </script>
    <?php
$this->end(); ?>
