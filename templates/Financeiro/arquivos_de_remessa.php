<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Arquivos de remessa
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">	
		<?php foreach($unidades as $unidade)
		  {
		  	?>
		  	<div  class="row accordion scope-0"  data-key="<?php echo $unidade->id; ?>" data-parent-key="" id="unidade_<?php echo $unidade->id; ?>">
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
<?php
	$this->append('script'); 
	echo $this->Html->script('vanilla-masker');
    echo $this->Html->script('datepicker-pt-br');
    echo $this->Html->script('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js');
    ?>
    <script type="text/javascript">
    	var arquivos_de_remessa = 
    	  {
    	  	init: function()
    	  	  {
    	  	  	var self = this;
    	  	  	$(document).on('click', '.accordion:not(.active)', self.loadSession);
				$(document).on('click', '.gerar-remessa', self.gerarRemessa);
    	  	  	return this;
    	  	  },
    	  	loadSession: function()
    	  	  {
    	  	  	var accordion = $(this);
    	  	  	if(!accordion.data('loaded'))
				  {
					var data = {unidade: accordion.data('key'), _csrfToken: $('[name="_csrfToken"]').val()};
					$.ajax({
						url: '/financeiro/verificar-arquivos-remessa',
						data: data,
						dataType: 'HTML',
						method: 'POST',
						success: function(resposta)
						{
							$(resposta).insertAfter(accordion.next('.kt-separator'));
							accordion.addClass('active');
						}
					});
				  }
    	  	  },
			gerarRemessa: function(e)
			  {
				  e.preventDefault();
				  var btn = $(this);
				  var unique = $(this).data('fields');
				  var data = $('#' + unique).serialize();
				  $.ajax(
				    {
						url: '/financeiro/gerar-arquivo-remessa',
						data: data,
						dataType: 'JSON',
						method: 'POST',
						success: function(resposta)
						  {
							  if(resposta.success === true)
							    {
									toastr.success('Arquivo de remessas gerado com sucesso!');
									var fileUrl = "data:text/plain;base64," + resposta.conteudo_arquivo;
									fetch(fileUrl)
										.then(response => response.blob())
										.then(blob => {
											var link = window.document.createElement("a");
											link.href = window.URL.createObjectURL(blob, { type: 'text/plain' });
											link.download = resposta.nome_arquivo;
											document.body.appendChild(link);
											link.click();
											document.body.removeChild(link);
										});
									window.location.reload();
								}
							  else
							    {
									toastr.error('Erro ao gerar o arquivo de remessas');
								}
						  }
					});
			  }
    	  };
    	$(document).ready(function()
    	  {
    	  	arquivos_de_remessa.init();
    	  });
    </script>
    <?php
$this->end(); ?>
