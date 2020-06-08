<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Gerar boletos
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
		<?php echo $this->Form->create(null, ['id' => 'form-boletos', 'url' => false]); ?>
			<div class="row form-group">
				<div class="col-sm-12">
					<label for="tipo">Tipo</label>
					<select name="tipo" id="tipo-boleto" class="form-control">
						<option value="">Selecione...</option>
						<option value="cota-mensal">Cota mensal de anuidade escolar</option>
						<option value="cota-composicao">Cota de composição</option>
					</select>
					<div class="form-text"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h6>Cota mensal de anuidade escolar</h6>
				</div>
			</div>
			<div class="row form-group" data-categoria="cota-mensal">
				<div class="col-sm-6">
					<label for="mes">Mês</label>
					<select name="mes" class="form-control disabled" disabled="disabled" name="mes">
						<option value="">Selecione...</option>
						<?php 
							$meses = [null, 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
							for($i = 1; $i <= 12; $i++)
							  {
							  	?>
							  		<option value="<?php echo $i; ?>"><?php echo $meses[$i]; ?></option>
							  	<?php
							  }
						?>
					</select>
					<div class="form-text"></div>
				</div>
				<div class="col-sm-6">
					<label for="mes">Ano</label>
					<select name="ano" class="form-control disabled" disabled="disabled" name="ano">
						<option value="">Selecione...</option>
						<?php 
							$dt    = new DateTime();
							$anos  = [$dt->format('Y')];
							$dt->modify('+1 year');
							$anos[1] = $dt->format('Y');
						?>
						<option value="<?php echo $anos[0]; ?>"><?php echo $anos[0]; ?></option>
						<option value="<?php echo $anos[1]; ?>"><?php echo $anos[1]; ?></option>
					</select>
					<div class="form-text"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h6>Cota de composição</h6>
				</div>
			</div>
			<div class="row form-group" data-categoria="cota-composicao">
				<div class="col-sm-6">
					<label for="aluno">Aluno</label>
					<select class="form-control disabled" disabled="disabled" name="aluno_id" id="alunos-select">
						<option value="">Selecione...</option>
					</select>
					<div class="form-text"></div>
				</div>
				<div class="col-sm-3">
					<label for="vencimento">Vencimento</label>
					<input type="text" name="vencimento" class="form-control disabled" disabled="disabled">
					<div class="form-text"></div>
				</div>
				<div class="col-sm-3">
					<label for="valor">Valor</label>
					<input type="text" name="valor" class="form-control disabled" disabled="disabled">
					<div class="form-text"></div>
				</div>
			</div>
			<div class="form-group row" data-categoria="cota-composicao">
				<div class="col-sm-12">
					<label for="motivo">Motivo</label>
					<textarea class="form-control disabled" disabled="disabled" name="motivo" style="height:166px"></textarea>
					<div class="form-text"></div>
				</div>
			</div>
		<?php echo $this->Form->end(); ?>
	</div>
	<div style="text-align:right; justify-content:flex-end" class="kt-footer">
		<button class="btn btn-success" id="gerar-boletos">Salvar</button>
	</div>
</div>
<?php $this->append('css'); 
	echo $this->Html->css('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css');
	$this->end();
	$this->append('script'); 
	echo $this->Html->script('vanilla-masker');
    echo $this->Html->script('datepicker-pt-br');
    echo $this->Html->script('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js');
    ?>
    <script type="text/javascript">
    	var dp_config = 
          {
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation:'auto bottom',
            locale: 'pt-BR',
            language: 'pt-BR'
          };
    	var boletos = 
    	  {
    	  	init: function()
    	  	  {
    	  	  	var self = this;
    	  	  	$(document).on('change', "#tipo-boleto", self.trocarTipo);
    	  	  	self.initInputs(self.formatarAluno, self.formatarSelecao);
    	  	  	$(document).on('click', '#gerar-boletos', {validar: self.validar}, self.gerarBoletos);
    	  	  	return this;
    	  	  },
    	  	initInputs: function(formatarAluno, formatarSelecao)
    	  	  {
    	  	  	$("[name='vencimento']").datepicker(dp_config);
    	  	  	VMasker($('[name="valor"]')).maskMoney({
                  precision: 2,
                  separator: ',',
                  delimiter: '.',
                  unit: false,
                  zeroCents: false
                });
                $('#alunos-select').select2({
                  ajax: {
                    url: '/alunos/opcoes-select',
                    dataType: 'JSON',
                    delay: 320,
				    data: function (params) {
				      return {
				        q: params.term
				      };
				    },
				    cache: true
				  },
				  placeholder: 'Aluno',
				  minimumInputLength: 2,
				  templateResult: formatarAluno,
				  templateSelection: formatarSelecao
                });
    	  	  },
    	  	trocarTipo: function()
    	  	  {
    	  	  	var val = $(this).val();
	  	  	  	$.each($("[data-categoria]"), function(i, item)
	  	  	  	  {
	  	  	  	  	var categoria = $(item).data('categoria');
	  	  	  	  	if((categoria != val)||(val == ""))
	  	  	  	  	  {
	  	  	  	  	  	$(item).find('input, select, textarea').addClass('disabled').attr('disabled', 'disabled');
	  	  	  	  	  	$(item).find('input, textarea').val("");
	  	  	  	  	  }
	  	  	  	  	else
	  	  	  	  	  {
	  	  	  	  	  	$(item).find('input, select, textarea').removeClass('disabled').removeAttr('disabled');
	  	  	  	  	  }
	  	  	  	  });
    	  	  },
    	  	formatarAluno: function(aluno)
    	  	  {
    	  	  	if(aluno.loading)
    	  	  	  {
    	  	  	  	return aluno.text;
    	  	  	  }
    	  	  	var $caixa = $(
    	  	  		"<div class='select2-result-aluno clearfix'>" +
    	  	  		 	"<div>" + aluno.pessoa.nome  + "</div>" +
    	  	  		"</div>"
    	  	  	);
    	  	  	return $caixa;
    	  	  },
    	  	formatarSelecao: function(aluno)
    	  	  {
    	  	  	if(typeof aluno.pessoa !== 'undefined')
    	  	  	  {
    	  	  	  	return aluno.pessoa.nome;
    	  	  	  }
    	  	  	return "Selecione...";
    	  	  },
    	  	validar: function()
    	  	  {
    	  	  	var fields = $("#form-boletos input, #form-boletos select, #form-boletos textarea");
    	  	  	var proceder = true;
    	  	  	$.each(fields, function(i, item)
    	  	  	  {
    	  	  	  	var $item = $(item);
    	  	  	  	if((!$item.hasClass('disabled'))&&($item.val() == ""))
    	  	  	  	  {
    	  	  	  	  	$item.siblings('.form-text').addClass("text-danger").text("Todos os campos devem estar preenchidos");
    	  	  	  	  	proceder = false;
    	  	  	  	  }
    	  	  	  	else
    	  	  	  	  {
    	  	  	  	  	$item.siblings('.form-text').text("");
    	  	  	  	  }
    	  	  	  });
    	  	  	return proceder;
    	  	  },
    	  	gerarBoletos: function(e)
    	  	  {
    	  	  	if(!e.data.validar())
    	  	  	  {
    	  	  	  	return false;
    	  	  	  }
    	  	  	var data = $("#form-boletos").serialize();
    	  	  	$.ajax(
    	  	  	  {
    	  	  	  	url: '/financeiro/processar-boletos',
    	  	  	  	data: data,
    	  	  	  	method: 'POST',
    	  	  	  	dataType: 'JSON',
    	  	  	  	success: function(resposta)
    	  	  	  	  {
    	  	  	  	  	if(resposta.success === true)
    	  	  	  	  	  {
    	  	  	  	  	  	var mensagem = (typeof resposta.quantidade === "undefined") ? resposta.quantidade + ' boletos gerados com sucesso!' : "Boleto gerado com sucesso!";
    	  	  	  	  	  	toastr.success(mensagem);
    	  	  	  	  	  	$("#form-boletos").find("input:not([type='hidden']), select, textarea").val("").trigger("change");
    	  	  	  	  	  }
    	  	  	  	  	else
    	  	  	  	  	  {
    	  	  	  	  	  	toastr.error(resposta.mensagem);
    	  	  	  	  	  }
    	  	  	  	  }
    	  	  	  });
    	  	  }
    	  };
    	$(document).ready(function()
    	  {
    	  	boletos.init();
    	  });
    </script>
    <?php
$this->end(); ?>
