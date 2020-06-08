<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Baixar boleto individualmente
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body" id="form-wrapper">	
      	<?php echo $this->Form->create(null, ['id' => 'boleto-individual', 'url' => false]); ?>
            <div class="row form-group">
                <div class="col-sm-12">
                    <label for="codigo">Código de Barras ou Linha Digitável</label>
                    <input class="form-control" type="text" name="codigo">
                    <div class="form-text"></div>
                </div>
            </div>
            <div class="row" style="margin-top:20px">
                <div class="col-sm-12" style="text-align:right; justify-content: flex-end">
                    <button class="btn btn-success" style="margin-bottom: 25px;" id="dar-baixa">Dar baixa</button>
                </div>
            </div>
        <?php echo $this->Form->end(); ?>
	</div>
</div>
<?php
	$this->append('script'); 
	echo $this->Html->script('vanilla-masker');
    echo $this->Html->script('datepicker-pt-br');
    echo $this->Html->script('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js');
    ?>
    <script type="text/javascript">
        var testJSON = function(text)
          {
            if(typeof text!=="string")
              {
                return false;
              }
            try
              {
                JSON.parse(text);
                return true;
              }
            catch (error)
              {
                return false;
              }
          };
    	var boleto_individual = 
    	  {
    	  	init: function()
    	  	  {
    	  	  	var self = this;
    	  	  	$(document).on('click', '#dar-baixa', self.baixarBoleto);
    	  	  	return this;
    	  	  },
    	  	baixarBoleto: function(e)
    	  	  {
                e.preventDefault();
    	  	  	var data = $("#boleto-individual").serialize();
    	  	  	$.ajax({
    	  	  		url: '/financeiro/baixa-boleto',
    	  	  		data: data,
    	  	  		method: 'POST',
                dataType: 'text',
    	  	  		async: true,
                success: function(resposta)
    	  	  		  {
    	  	  		  	if(testJSON(resposta))
                          {
                            resposta = JSON.parse(resposta);
                            if(resposta.success === true)
                              {
                                toastr.success('Boleto baixado com sucesso!');
                                window.location.reload();
                              }
                            else
                              {
                                toastr.error(resposta.error);
                              }
                          }
                        else
                          {
                            $("#form-wrapper").html(resposta);
                            setTimeout(function()
                              {
                                var dp_config = 
                                  {
                                    format: 'dd/mm/yyyy',
                                    autoclose: true,
                                    orientation:'auto bottom',
                                    locale: 'pt-BR',
                                    language: 'pt-BR'
                                  };
                                $("#data_liquidacao_baixa").datepicker(dp_config);
                                VMasker($('#valor_baixa')).maskMoney({
                                    precision: 2,
                                    separator: ',',
                                    delimiter: '.',
                                    unit: false,
                                    zeroCents: false
                                  }, 500);
                              });
                          }
    	  	  		  }
    	  	  	});
    	  	  }
    	  };
    	$(document).ready(function()
    	  {
    	  	boleto_individual.init();
    	  });
    </script>
    <?php
$this->end(); ?>
