<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Ocorrências
			</h3>
		</div>
		
	</div>
	<div class="kt-portlet__body">
		
		<div class="row accordion scope-0" data-scope="0"  data-key="nova_ocorrencia" data-parent-key="" data-parent-scope="" id="nova_ocorrencia">
			<div class="col-sm-12">
				<h4>Nova ocorrência</h4>
			</div>	
		</div>
		<div class="kt-separator scope-0 kt-separator--space-sm"></div>
		<?php echo $this->element('ocorrencia_form', ['key' => 'main', 'accordion' => true]); ?>
		<div class="row accordion form-group no-caret scope-1" style="display:none" data-scope="1" data-key="" data-parent-key="nova_ocorrencia" data-parent-id="nova_ocorrencia">
            <div class="col-sm-12" style="text-align: right; justify-content: flex-end">
                <button class="btn btn-success" style="margin-bottom: 25px;"  id="nova-ocorrencia-main">Inserir</button>
            </div>
		</div>
		<div class="kt-separator scope-1 kt-separator--space-sm" style="display:none;"></div>
		<div class="row accordion scope-0" data-scope="0"  data-key="0" data-parent-key="" data-parent-scope="" id="pesquisa_avancada">
			<div class="col-sm-12">
				<h4>Pesquisa avançada</h4>
			</div>	
		</div>
		<div class="kt-separator scope-0 kt-separator--space-sm"></div>
		<?php echo $this->form->create(null, ['id' => 'pesquisa-avancada-ocorrencias']); ?>
			<div class="row accordion scope-1 no-caret" data-parent-key="" data-parent-id="pesquisa_avancada" style="display:none; margin-top:20px; margin-bottom:20px" data-parent-scope="" id="pesquisa_avancada_form">
				<div class="col-sm-12">
					<input type="hidden" name="unidades" value="">
					<div class="kt-checkbox-inline" data-name="unidades">
						<?php foreach($unidades as $unidade)
						{
							?>
							<label class="kt-checkbox kt-checkbox--bold">
								<input name="unidades[]"  type="checkbox"  value="<?php echo $unidade->id; ?>"> <?php echo $unidade->nome; ?>
								<span></span>
							</label>
							<?php
						}
						?>
					</div>
				</div>
			</div>
			<div class="row accordion scope-1 no-caret" data-parent-key="" data-parent-id="pesquisa_avancada" style="display:none; margin-bottom:20px" data-parent-scope="" id="pesquisa_avancada_form">
				<div class="col-sm-3">
					<label for="pessoas[]">Aluno ou colaborador</label>
					<select name="pessoas[]" class="form-control select2" multiple="multiple>
						<option value="">Selecione...</option>
						<?php foreach($pessoas as $pessoa) 
						  {
							  ?>
								<option value="<?php echo $pessoa->id; ?>"><?php echo $pessoa->nome; ?></option>
							<?php
						  }
						  ?>
					</select>
				</div>
				<div class="col-sm-3">
					<label for="data_inicial">Data inicial</label>
					<input name="data_inicial" class="form-control" />
				</div>
				<div class="col-sm-3">
					<label for="data_final">Data final</label>
					<input name="data_final" class="form-control" />
				</div>
				<div class="col-sm-3">
					<label for="tags[]">Hashtags</label>
					<select name="tags[]" class="form-control select2" multiple="multiple>
						<option value="">Selecione...</option>
						<?php foreach($tags as $tag_id => $tag) 
						  {
							?>
								<option value="<?php echo $tag_id; ?>"><?php echo $tag; ?></option>
							<?php
						  }
						?>
					</select>
				</div>
			</div>
			<div class="row accordion form-group no-caret scope-1" style="display:none" data-scope="1" data-key="" data-parent-key="pesquisa_avancada" data-parent-id="pesquisa_avancada">
				<div class="col-sm-12" style="text-align: right; justify-content: flex-end">
					<button class="btn btn-success" style="margin-bottom: 25px;"  id="realizar-pesquisa-avancada">Pesquisar</button>
				</div>
			</div>
			<div class="row accordion scope-1 no-caret" data-parent-key="" data-parent-scope="" data-parent-id="pesquisa_avancada" id="pesquisa_avancada_resultados">
					
			</div>
			<div class="kt-separator scope-1 kt-separator--space-sm" style="display:none"></div>
		<?php echo $this->form->end(); ?>
        <div class="row accordion scope-0" data-scope="0"  data-key="0" data-parent-key="" data-parent-scope="" id="ocorrencias">
			<div class="col-sm-12">
				<h4>Ocorrências</h4>
			</div>	
		</div>
		<div class="kt-separator scope-0 kt-separator--space-sm"></div>
		<?php foreach($ocorrencias as $ocorrencia)
		  {
			echo $this->element('ocorrencia_individual', ['ocorrencia' => $ocorrencia, 'scope' => 2, 'parent_id' => 'ocorrencias', 'marcar_visto' => true, 'usuario_id' => $user['id']]);
		  } 
		?>
		
	</div>
	<?php echo $this->Form->create(null, ['id' => 'token-form']); echo $this->Form->end(); ?>
</div>
<?php
$this->append('css'); 
	echo $this->Html->css('jquery.atwho.min');
	echo $this->Html->css('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css');
$this->end();
$this->append('script'); 
	echo $this->Html->script('vanilla-masker');
	echo $this->Html->script('jquery.caret.min');
	echo $this->Html->script('jquery.atwho.min');
	echo $this->Html->script('class-formulario-ocorrencia');
	echo $this->Html->script('datepicker-pt-br');
	echo $this->Html->script('https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js');
	?>
	<script type="text/javascript">
	var lista_pessoas = <?php echo json_encode($lista_pessoas); ?>;
	var lista_tags    =  <?php echo json_encode($lista_tags); ?>;
	var dp_config     = 
          {
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation:'auto bottom',
            locale: 'pt-BR',
            language: 'pt-BR'
          };
	var fechar = function _fechar($item)
      {
        var id   = $item.attr('id');
        var children = $("[data-parent-id='" + id + "'], [data-parent-id='" + id + "'] + .kt-separator, [data-grandparent-id='" + id + "'], [data-grandparent-id='" + id + "'] + .kt-separator");
        if(children.length)
          {
            $.each(children, function(i, item)
              {
                _fechar($(item));
                $(item).slideUp();
                $(item).next('.kt-separator').slideUp();
              });
          }
        $item.removeClass('active');
      };
	var ocorrencias = 
	  {
		init: function()
		  {
			var self = this;
			$(document).on('click', '.accordion', self.carregarProximaSessao);
			$(document).on('click', '.marcar-como-visto', self.marcarVisto);
			$(document).on('click', '.inserir-comentario', self.buscaFormComentario);
			$(document).on('click', '#realizar-pesquisa-avancada', self.pesquisaAvancada);
			$('.select2').select2({width: '100%'});
			$("#pesquisa-avancada-ocorrencias [name^='data_']").datepicker(dp_config);
			var form = new FormularioOcorrencia('main', function(){toastr.success('Ocorrência inserida com sucesso!'); window.location.reload();}, lista_pessoas, lista_tags);
			form.ocorrencia_form.init();
			return this;
		  },
		carregarProximaSessao: function()
		  {
			var accordion = $(this);
		    if(accordion.hasClass('active'))
			  {
				fechar(accordion);
			  }
		    else
			  {
			    $("[data-parent-id='" + accordion.attr('id') + "']").slideDown();
			    $("[data-parent-id='" + accordion.attr('id') + "']").next('.kt-separator').slideDown();
			    $("[data-parent-id='" + accordion.attr('id') + "']").find('[name="tipo_circular"]:checked').trigger('change');
			    accordion.addClass('active');
			  }
		  },
		marcarVisto: function()
		  {
			var id = $(this).data('id');
			var token = $('[name="_csrfToken"]').val();
			$.ajax(
			  {
				url: 'ocorrencias/marcar-lido',
				data: {id: id, _csrfToken: token},
				dataType: 'JSON',
				method: 'POST',
				success: function(resposta)
				  {
					if(resposta.success === true)
					  {
						toastr.success('Ocorrência marcada como vista');
					  }
					else
					  {
						toastr.error('Erro ao marcar a ocorrência');
					  }
				  }
			  });
		  },
		buscaFormComentario: function()
		  {
			var $this = $(this);
			$.ajax(
				{
				url: '/ocorrencias/busca-form-comentarios',
				data: {_csrfToken: $('[name="_csrfToken"]').val(), id: $this.data('id')},
				method: 'POST',
				dataType: 'HTML',
				success: function(resposta)
					{
					var div = $this.parent().parent();
					$(resposta).insertAfter(div);
					var form = new FormularioOcorrencia($this.data('id'), function(){toastr.success('Ocorrência inserida com sucesso!'); window.location.reload();}, lista_pessoas, lista_tags);
					form.ocorrencia_form.init();
					$this.removeClass('inserir-comentario');
					}
				});
		  },
		pesquisaAvancada: function(e)
		  {
			e.preventDefault();
			var data = $("#pesquisa-avancada-ocorrencias").serialize();
			$.ajax(
			  {
				url: '/ocorrencias/pesquisa-avancada',
				data: data,
				method: 'POST',
				dataType: 'HTML',
				success: function(resposta)
				  {
					$("#pesquisa_avancada_resultados").html(resposta);
				  }
			  });
		  }
	  };
	$(document).ready(function()
	  {
		ocorrencias.init();
	  });
	</script>
	<?php 
$this->end(); ?>