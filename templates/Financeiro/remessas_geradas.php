<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Remessas geradas
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
		<div class="row">
			<table class="table table-condensed table-remessa table-hover">
				<thead>
					<tr>
						<th>Unidade</th>
						<th>Data da geração do arquivo</th>
						<th>Nº de boletos</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($remessas as $remessa)
					  {
						?>
							<tr>
								<td><?php echo $remessa->unidade->nome; ?></td>
								<td><?php echo $remessa->data_criacao->format('d/m/Y H:i:s'); ?></td>
								<td><?php echo $remessa->quantidade_boletos; ?></td>
								<td align="right">
									<a class="btn btn-info btn-xs btn-icon baixar-remessa" href="javascript:void(0)" data-nome-arquivo="<?php echo $remessa->unidade->sigla_empresa . "_" . $remessa->data_criacao->format('Y_m_d') . "_" . $remessa->id . ".rem"; ?>" data-conteudo-arquivo="<?php echo base64_encode($remessa->conteudo_arquivo); ?>" >
										<i class="fa fa-download"></i>
									</a>
								</td>
							</tr>
						<?php
					  }
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php 
	$this->append('script'); 
    ?>
    <script type="text/javascript">
		var remessas = 
		  {
			init: function()
			  {
				var self = this;
				$(document).on('click', ".baixar-remessa", self.baixarRemessa);
				return this;
			  },
			baixarRemessa: function()
			  {
				var btn = $(this);
				var fileUrl = "data:text/plain;base64," + btn.data('conteudo-arquivo');
				fetch(fileUrl)
				.then(response => response.blob())
				.then(blob => {
					var link = window.document.createElement("a");
					link.href = window.URL.createObjectURL(blob, { type: 'text/plain' });
					link.download = btn.data('nome-arquivo');
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);
				});
			  }
		  };
    	$(document).ready(function()
    	  {
    	  	remessas.init();
    	  });
    </script>
    <?php
$this->end(); ?>
