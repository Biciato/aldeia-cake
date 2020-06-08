<?php 
	if($blocks !== "aluno")
	  {
	  	if(count($blocks))
	  	  {
	  	  	foreach($blocks as $block_data)
	  	  	  {
	  	  	  	extract($block_data);
	  	  	  	?>
	  	  	  	<div style="display: none;" class="row accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>">
	  	  	  		<div class="col-sm-12">
	  	  	  			<h4><?php echo $nome; ?>
						<?php if(isset($dados_boletos))
						  {
							  ?>
								<span class="end">
									<?php echo $this->Grana->formatar($dados_boletos['valor'], 2, ',', '.', 'R$ '); ?> &nbsp;
									<?php $today = new \DateTime();
									$interval = $dados_boletos['maior_atraso']->diff($today); 
									echo $interval->format('%r%a dias'); ?>
								</span>
							  <?php
							unset($dados_boletos);  
						  }
						?>
						</h4>
	  	  	  		</div>
	  	  	  	</div>
	  	  	  	<div style="display: none;"  class="kt-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
	  	  	  	<?php
	  	  	  }
	  	  }
	  	else
	  	  {
	  	  	echo "sem-resultados";
	  	  }
	  }
	else
	  {
	  	$unique = uniqid();
	  	?>
	  	<div data-non-loadable="1" style="display: none;" class="row accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>" data-unique="<?php echo $unique; ?>">
	  		<div class="col-sm-12">
				<table class="table table-condensed tabela-boletos-vencidos">
					<thead>
						<tr>
							<th>Data de vencimento</th>	
							<th>Valor</th>
							<th>Dias em Atraso</th>
							<th>Valor Atualizado</th>
							<th>Boleto</th>
						</tr>
					</thead>
					<tbody>
						<?php if(@count($aluno->pessoa->boletos_vencidos) < 1)
						  {
							  ?>
							  <tr>
							  	<td colspan="5">Não foram encontrados boletos vencidos</td>
							  </tr>
							  <?php
						  }
						else
						  {
							foreach($aluno->pessoa->boletos_vencidos as $boleto)
							  {
								?>
									<tr>
								  		<td><?php echo $boleto->data_vencimento_formatada; ?></td>
								  		<td><?php echo $this->Grana->formatar($boleto->valor_sem_desconto); ?></td>
								  		<td><?php echo $boleto->dias_atrasados; ?></td>
								  		<td><?php echo $this->Grana->formatar($boleto->valor_atualizado); ?></td>
								  		<td>
										    <a target="_blank" href="/financeiro/boleto/<?php echo $boleto->id; ?>">
												Imprimir
											</a>
										</td>
							 		</tr>
								<?php
							  }
						  } ?>
					</tbody>
			    </table>
			</div>
			<div class="col-sm-12">
				<div class="col-sm-12" style="text-align:right; justify-content: flex-end">
					<button class="btn btn-success enviar-cobranca"  style="margin-bottom: 25px;" data-aluno="<?php echo $aluno->id; ?>">Enviar cobrança</button>
				</div>
			</div>
			<div class="col-sm-12">
				<table class="table table-condensed tabela-boletos-vencidos">
					<thead>
						<tr>
							<th>Data da Cobrança</th>	
							<th>Assunto</th>
							<th>Lido</th>
						</tr>
					</thead>
					<tbody class="cobrancas-enviadas" id="cobrancas-enviadas-aluno-<?php echo $aluno->id; ?>" data-aluno="<?php echo $aluno->id; ?>">
						<?php if(@count($aluno->pessoa->cobrancas) < 1)
						  {
							  ?>
							  <tr>
							  	<td colspan="3">Não foram encontradas cobranças enviadas</td>
							  </tr>
							  <?php
						  }
						else
						  {
							foreach($aluno->pessoa->cobrancas as $cobranca)
							  {
								?>
									<tr>
								  		<td><?php echo $cobranca->data_envio->format('d/m/Y'); ?></td>
								  		<td><?php echo $cobranca->assunto; ?></td>
								  		<td><?php echo ($cobranca->lida) ? 'Sim' : 'Não'; ?></td>
							 		</tr>
								<?php
							  }
						  } ?>
					</tbody>
					
			    </table>
			</div>
	  	</div>
	  	<div style="display: none;"  class="kt-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
	  	<?php
	  	
	  }
?>