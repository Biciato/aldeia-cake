<?php 
	if($blocks !== "rps")
      {
        if(count($blocks))
          {
              foreach($blocks as $block_data)
                {
                    extract($block_data);
                    ?>
                    <div style="display: none;" class="row accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>">
                        <div class="col-sm-12">
                            <h4><?php echo $nome; ?> <?php 
                            if(isset($block_data['nf_lancada']))
                              {
                                if($nf_lancada)
                                  {
                                      ?>
                                       <i class="fa fa-check"></i>
                                      <?php
                                  }
                              } ?></h4>
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
        $dados = $rps->conteudo_rps;  
        $emissao = explode("T", $dados['DataEmissao']);
        
        ?>
	  	<div data-non-loadable="1" style="display: none;" class="row accordion scope-<?php echo $scope; ?>" data-scope="<?php echo $scope; ?>" data-key="<?php echo $key; ?>" data-parent-key="<?php echo $parent_key; ?>" data-parent-scope="<?php echo $parent_scope; ?>" id="<?php echo $unique; ?>" data-parent-id="<?php echo $parent_id; ?>" data-unique="<?php echo $unique; ?>">
	  		<div class="col-sm-6">
                <h5>Recibo provisório de serviço</h5>
                <p>Número: <?php echo $rps->numero_sequencial; ?></p>                
                <p>Emissão: <?php echo @implode('/', array_reverse(explode('-', $emissao[0]))) . " " . @$emissao[1]; ?></p>
                <p>Valor dos serviços: <?php echo $this->Grana->formatar((float)$dados['Servico']['Valores']['ValorServicos']); ?></p>
                <p>Discriminação: <?php echo $dados['Servico']['Discriminacao']; ?></p>
                <p>Prestador: <?php 
                    echo $dados['Prestador']['Cnpj']; 
                    ?></p>                
                <p>Tomador: <?php echo $dados['Tomador']['IdentificacaoTomador']['CpfCnpj']['Cpf'] . ' ' . $dados['Tomador']['RazaoSocial'] . ' ' . $dados['Tomador']['Endereco']['Endereco'] . ' ' . $dados['Tomador']['Endereco']['Numero'] . ' ' . $dados['Tomador']['Endereco']['Complemento'] . ' ' . $dados['Tomador']['Endereco']['Bairro'] . ' ' . $dados['Tomador']['Endereco']['Uf'];  ?></p>
			</div>
			<div class="col-sm-6">
                <h5>Nota fiscal</h5>
                <?php if($rps->nota_fiscal)
                  {
                    $dados = json_decode($rps->nota_fiscal->dados_da_nota, true);
                    $emissao = explode("T", $dados['DataEmissao']);
                    ?>
                    <p>Número: <?php echo $dados['Numero']; ?></p>
                    <p>Código: <?php echo $dados['CodigoVerificacao']; ?></p>
                    <p>Emissão: <?php echo @implode('/', array_reverse(explode('-', $emissao[0]))) . " " . @$emissao[1]; ?></p>
                    <p>Valor dos serviços: <?php echo $this->Grana->formatar((float)$dados['Servico']['Valores']['ValorServicos']); ?></p>
                    <p>Valor do crédito: <?php echo $this->Grana->formatar((float)$dados['ValorCredito']); ?></p>
                    <p>Prestador: <?php echo  $dados['PrestadorServico']['IdentificacaoPrestador']['Cnpj'] . ' ' . $dados['PrestadorServico']['RazaoSocial'] .   ' ' . $dados['PrestadorServico']['Endereco']['Endereco'] . ' ' . $dados['PrestadorServico']['Endereco']['Numero'] . ' ' . $dados['PrestadorServico']['Endereco']['Bairro'] . ' ' . $dados['PrestadorServico']['Endereco']['Uf'] ?></p>
                    <p>Tomador: <?php echo $dados['TomadorServico']['IdentificacaoTomador']['CpfCnpj']['Cpf'] . ' ' . $dados['TomadorServico']['RazaoSocial'] . ' ' . $dados['TomadorServico']['Endereco']['Endereco'] . ' ' . $dados['TomadorServico']['Endereco']['Numero'] . ' ' . $dados['TomadorServico']['Endereco']['Complemento'] . ' ' . $dados['TomadorServico']['Endereco']['Bairro'] . ' ' . $dados['TomadorServico']['Endereco']['Uf'];  ?></p>
                    <?php
                  }
                else
                  {
                      ?>
                      <p>A nota fiscal para esse RPS ainda não foi emitida</p>
                      <?php
                  } ?>
            </div>
	  	</div>
	  	<div style="display: none;"  class="kt-separator scope-<?php echo $scope; ?> kt-separator--space-sm"></div>
	  	<?php
	  	
	  }
?>