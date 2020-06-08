<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Notas fiscais
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
        <div style="display:none;">
            <?php echo $this->Form->create(null); echo $this->Form->end(); ?>
        </div>	
		<?php foreach($unidades as $unidade)
		  {
              $totais_unidade = [];
		  	?>
		  	<div  class="row accordion scope-0"  data-key="<?php echo $unidade->id; ?>" data-parent-key="" id="unidade_<?php echo $unidade->id; ?>">
		  		<div class="col-sm-12">
		  			<h4>
		  				<?php echo $unidade->nome; ?>
                        <span class="end total-marcado-<?php echo $unidade->id; ?>">
                           
                        </span>
		  			</h4>
		  		</div>	
		  	</div>
		  	<div class="kt-separator scope-0 kt-separator--space-sm"></div>
            <div data-parent="unidade-<?php echo $unidade->id; ?>" style="display:none;" class="row row-head-tabela-notas">
                <div class="col-sm-3">
                    <span id="total-alunos-<?php echo $unidade->id; ?>">
                        Total de alunos: 
                    </span>
                </div>
                <div class="col-sm-3">
                    <span id="alunos-marcados-<?php echo $unidade->id; ?>">
                        Alunos marcados: 
                    </span>
                </div>
                <div class="col-sm-3">
                    <span id="total-<?php echo $unidade->id; ?>">
                        Total: 
                    </span>
                </div>
                <div class="col-sm-3">
                    <span id="total-marcado-<?php echo $unidade->id; ?>">
                        Total marcado: 
                    </span>
                </div>
            </div>
            <div  data-parent="unidade-<?php echo $unidade->id; ?>" style="display:none;" class="kt-separator scope-0 kt-separator--space-sm" ></div>
            <div data-parent="unidade-<?php echo $unidade->id; ?>" style="display:none;" class="row row-tabela-notas">
                <div class="col-sm-12">
                    <table class="table table-condensed tabela-unidade-nota tabela-unidade-<?php echo $unidade->id; ?>" data-unidade="<?php echo $unidade->id; ?>">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <?php foreach($unidade->servicos as $k => $servico)
                                  {
                                      $totais_unidade[$k] = 0;
                                      ?>
                                        <th><?php echo $servico; ?></th>
                                      <?php
                                  } ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php foreach($unidade->alunos as $aluno)
                             {
                                 ?>
                                 <tr class="tr-aluno" data-aluno="<?php echo $aluno->id; ?>">
                                    <td>
                                        <input data-unidade="<?php echo $unidade->id; ?>" data-aluno="<?php echo $aluno->id; ?>" <?php echo ($aluno->emite_nota_fiscal) ? "checked=\"checked\"" : ""; ?> type="checkbox" class="checkbox-aluno-unidade-<?php echo $unidade->id; ?> checkbox-aluno-<?php echo $aluno->id; ?> checkbox-aluno"/> <?php echo $aluno->pessoa->nome; ?>
                                    </td>
                                    <?php $total = 0; 
                                    foreach(array_keys($unidade->servicos) as $servico) 
                                        {
                                            ?>
                                            <td class="servicos-unidade-<?php echo $unidade->id; ?> servicos-aluno-<?php echo $aluno->id; ?>" data-servico="<?php echo $servico; ?>" data-valor="<?php echo (isset($aluno->valores_servico[$servico])) ? $aluno->valores_servico[$servico] : 0; ?>">
                                            <?php
                                            if(isset($aluno->valores_servico[$servico]))
                                              {
                                                echo $this->Grana->formatar($aluno->valores_servico[$servico]);
                                                $total += $aluno->valores_servico[$servico];
                                                $totais_unidade[$servico] += $aluno->valores_servico[$servico];
                                              }
                                            ?>
                                            </td>
                                            <?php
                                        }
                                    ?>
                                    <td class="totais-unidade-<?php echo $unidade->id; ?> totais-aluno-<?php echo $aluno->id; ?>" data-valor="<?php echo $total; ?>"><?php echo $this->Grana->formatar($total); ?></td>
                                 </tr>
                                 <?php
                             } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Totais:</td>
                                <?php foreach($unidade->servicos as $k => $servico)
                                  {
                                      ?>
                                        <td><?php echo $this->Grana->formatar($totais_unidade[$k]); ?></td>
                                      <?php
                                  } 
                                ?>
                                <td>
                                  <?php echo $this->Grana->formatar(array_sum($totais_unidade)); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
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
    	var controle = 
          {
            init: function()
              {
                var self = this;
                self.initTabelas(self.atualizarTabela);
                $(document).on('change', '.checkbox-aluno', {atualizarTabela: self.atualizarTabela}, self.atualizaAluno);
                $(document).on('click', '.accordion', self.sanfona);
                return this;
              },
            initTabelas: function(atualizarTabela)
              {
                var unidades = $.find('.tabela-unidade-nota');
                $.each(unidades, function(i, item)
                  {
                      var $item = $(item);
                      atualizarTabela($item.data('unidade'));
                  });  
              },
            atualizarTabela: function(unidade)
              {
                var formataGrana = function(valor)
                  {
                    return number_format((valor/100), 2, ",", ".");
                  };
                var tabela = $('.tabela-unidade-' + unidade);
                var totais = {};
                var total = 0;
                var total_alunos = 0;
                var total_marcado = 
                  {
                    total: 0,
                    percentual: 0
                  };
                var alunos_marcados = 
                  {
                    total: 0,
                    percentual: 0
                  };
                tabela.find('.tr-aluno').each(function(i, item)
                  {
                    var aluno = $(item).data('aluno');
                    var cb = $('.checkbox-aluno-' + aluno);
                    var somar_marcados = cb.prop('checked');
                    total_alunos++;
                    if(somar_marcados)
                      {
                        alunos_marcados.total++;
                      }
                    $(item).find('.servicos-unidade-' + unidade).each(function(i,iitem)
                      {
                        var $item = $(iitem);
                        var valor = parseInt($item.data('valor'));
                        if(somar_marcados)
                          {
                            total_marcado.total += valor;
                          }
                        total += valor;
                      });
                  });
                total_marcado.percentual   = (!isNaN(Math.round((100*total_marcado.total)/total))) ? Math.round((100*total_marcado.total)/total) : 0;
                alunos_marcados.percentual = (!isNaN(Math.round((100*alunos_marcados.total)/total_alunos))) ? Math.round((100*alunos_marcados.total)/total_alunos): 0;
                $("#total-alunos-" + unidade).text('Total de alunos: ' + total_alunos);
                $("#alunos-marcados-" + unidade).text('Alunos marcados: ' + alunos_marcados.total + " (" + alunos_marcados.percentual + "%)");
                $("#total-" + unidade).text('Total : ' + formataGrana(total));
                $("#total-marcado-" + unidade).text('Total marcado: ' + formataGrana(total_marcado.total) + " (" + total_marcado.percentual + "%)");
                $(".total-marcado-" + unidade).text(formataGrana(total_marcado.total));
              },
            atualizaAluno: function(e)
              {
                var cb = $(this);
                var atualizarTabela = e.data.atualizarTabela;
                var data = 
                  {
                    ativar: cb.prop('checked'),
                    aluno: cb.data('aluno'),
                    _csrfToken: $("[name='_csrfToken']").val()
                  };
                $.ajax({
                    url: '/financeiro/atualizar-aluno-nota-fiscal',
                    data: data,
                    method: 'POST',
                    async: true,
                    dataType: 'JSON',
                    success: function(resposta)
                      {
                        if(resposta.success === true)
                          {
                            toastr.success('Aluno atualizado com sucesso!');
                            atualizarTabela(cb.data('unidade'));
                          }
                      }
                })
              },
            sanfona: function()
              {
                var sanfona = $(this);
                sanfona.toggleClass('active');
                if(sanfona.hasClass('active'))
                  {
                    $('[data-parent="unidade-' + sanfona.data('key') + '"]').slideDown();
                  }
                else
                  {
                    $('[data-parent="unidade-' + sanfona.data('key') + '"]').slideUp();
                  }
              }
          };
        $(document).ready(function()
          {
            controle.init();
          });
    </script>
    <?php
$this->end(); ?>
