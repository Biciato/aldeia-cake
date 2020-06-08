<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Nova circular
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
        <?php echo $this->Form->create(null, 
          [
            'id' => 'form-circular',
            'url' => false
          ]); ?>
            <div class="row accordion scope-0" data-scope="0" data-key="circular" data-parent-key="" data-parent-scope="" id="sanfona-circular">
                <div class="col-sm-12">
                    <h4>Circular</h4>
                </div>	
            </div>
            <div class="kt-separator scope-0 kt-separator--space-sm"></div>
            <div class="row form-group" style="margin-top:20px; display:none;" data-parent-id="sanfona-circular">
                <div class="col-sm-12">
                    <label for="titulo">Título</label>
                    <input type="text" name="titulo" class="form-control" />
                    <div class="form-text"></div>
                </div>
            </div>
            <div class="row form-group" style="display:none;" data-parent-id="sanfona-circular">
                <div class="col-sm-12">
                    <label for="tipo_circular">Tipo de circular</label>
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--solid">
                            <input type="radio" name="tipo_circular" value="arquivo_pdf"> Arquivo PDF<span></span>
                        </label>
                        <label class="kt-radio kt-radio--solid">
                            <input type="radio" name="tipo_circular" value="texto_html">Texto HTML<span></span>
                        </label>
                    </div>
                    <div class="form-text"></div>
                </div>
            </div>
            <div class="row form-group" style="display:none;" data-parent-radio="arquivo_pdf" data-grandparent-id="sanfona-circular">
                <div class="col-sm-12">
                    <label for="arquivo">Arquivo</label>
                    <input type="file" accept="application/pdf" name="arquivo" class="form-control" />
                    <div class="form-text"></div>
                </div>
            </div>
            <div class="row form-group" style="display:none;" data-parent-radio="texto_html" data-grandparent-id="sanfona-circular">
                <div class="col-sm-12">
                    <label for="conteudo_html">Conteúdo HTML</label>
                    <textarea class="form-control" id="editor" name="conteudo_html" style="height:600px">
                        
                    </textarea>  
                    <div class="form-text"></div>          
                </div>
            </div>
            <div class="row" style="display:none;" data-parent-radio="texto_html" data-grandparent-id="sanfona-circular"> 
                <div class="col-sm-12">
                    <label>Preview</label>
                    <div id="email-preview"  style="font-family: verdana; background:  #FFF; margin:0; padding:0; font-size: 15px">
                        <table border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; background: white;" width="100%">
                            <tr>
                                <td align="center" height="50" style="background:#EF824E;" valign="top">&nbsp;</td>
                            </tr>
                        
                            <tr>
                                <td align="center" valign="top">
                                    <img style="height: auto; width:150px; margin-top: 20px" width="150" src="https://sige.aigen.com.br/img/logo.png" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top" id="conteudo">
                                   
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="right">
                                    <p style="margin-bottom: 20px; margin-top: 20px"><?php echo date('d/m/Y H:i:s'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" height="50" style="background:#EF824E;" valign="top">&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="kt-separator scope-0 kt-separator--space-sm"  style="display:none;" ></div>
            <div class="row accordion scope-0" data-scope="0" data-key="unidades" data-parent-key="" data-parent-scope="" id="sanfona-unidades">
                <div class="col-sm-12">
                    <h4>Unidades</h4>
                </div>	
            </div>
            <div class="kt-separator scope-0 kt-separator--space-sm"></div>
            <div class="row form-group" data-parent-id="sanfona-unidades" style="display:none;">
                <div class="col-sm-12">
                    <label for="tipo_circular">Tipo de circular</label>
                        <div class="kt-checkbox-inline">
                            <input type="hidden" name="unidades" value=""/>
                            <?php 
                                foreach($unidades as $unidade)
                                  {
                                    ?>
                                    <label class="kt-checkbox kt-checkbox--solid">
                                        <input type="checkbox" name="unidades[]" value="<?php echo $unidade->id; ?>"> <?php echo $unidade->nome; ?><span></span>
                                    </label>
                                    <?php
                                  }
                            ?>
                        </div>
                    <div class="form-text"></div>
                </div>
            </div>
            <div class="kt-separator scope-0 kt-separator--space-sm"  style="display:none;"></div>
            <div class="row accordion scope-0" data-scope="0" data-key="turmas" data-parent-key="" data-parent-scope="" id="sanfona-turmas">
                <div class="col-sm-12">
                    <h4>Turmas</h4>
                </div>	
            </div>
            <div class="kt-separator scope-0 kt-separator--space-sm" id="marcador-turmas"></div>
            <div style="text-align:right; justify-content: flex-end" class="kt-footer">
              <button class="btn btn-success" id="enviar-circular">Enviar</button>
            </div>
        <?php echo $this->Form->end(); ?>
	</div>
</div>
<?php $this->append('script');
	echo $this->Html->script('https://code.jquery.com/ui/1.10.4/jquery-ui.js');
	echo $this->Html->script('ckeditor');
    echo $this->Html->script('ckfinder/ckfinder');
    ?>
    <script type="text/javascript">
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
          }
        var circulares = 
          {
            init: function()
              {
                var self = this;
                CKEDITOR.config.height = 500;
		  	  	    CKEDITOR.config.entities_latin = false;
                var editor = CKEDITOR.replace('editor',
                  {
                  removePlugins: 'scayt,wsc,liststyle,tableselection,tabletools,tableresize,contextmenu',
                  disableNativeSpellChecker: false
                  });
                CKFinder.setupCKEditor(editor);
                CKEDITOR.instances.editor.on('change', function() 
                  { 
                    var data = CKEDITOR.instances.editor.getData();
                    $("#conteudo").html(data);
                  });
                $(document).on('click', '.accordion', self.carregarProximaSessao);
                $(document).on('change', '[name="tipo_circular"]', self.gerenciarCampos);
                $(document).on('change', '[name="unidades[]"]', self.gerenciarTurmas);
                $(document).on('change', '[name="unidades_circular"]', self.checkboxesUnidade);
                $(document).on('change', '[name="turmas[]"]', self.checkboxesTurmas);
                $(document).on('change', '[name="alunos[]"]', self.checkboxesAlunos);
                $(document).on('click', '#enviar-circular', self.enviarCircular);
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
            gerenciarCampos: function()
              {
                var val = $(this).val();
                var fields = $('.row[data-parent-radio="texto_html"], .row[data-parent-radio="arquivo_pdf"]');
                $.each(fields, function(i, item)
                  {
                    var $item = $(item);
                    if($item.data('parent-radio') == val)
                      {
                        $item.slideDown();
                      }
                    else
                      {
                        $item.slideUp();
                      }
                  });
              },
            gerenciarTurmas: function()
              {
                var _unidades = $('[name="unidades[]"]');
                var unidades = [];
                $.each(_unidades, function(i, item)
                  {
                    var $item = $(item);
                    if($item.prop('checked') === true)
                      {
                        unidades.push($item.val());
                      }
                  });
                var token = $('[name="_csrfToken"]').val();
                if(unidades.length > 0)
                  {
                    $.ajax(
                      {
                        url: '/circulares/buscar-turmas',
                        data: {_csrfToken: token, unidades: unidades},
                        method: 'POST',
                        dataType: 'HTML',
                        success: function(resposta)
                          {
                            $('.dados_turmas').next('.kt-separator').remove(); 
                            $('.dados_turmas').remove(); 
                            $(resposta).insertAfter("#marcador-turmas");
                            if($("#sanfona-turmas").hasClass('active'))
                              {
                                $("#sanfona-turmas").removeClass('active');
                              }
                            $("#sanfona-turmas").trigger('click');
                          }
                      });
                  }
                else
                  {
                    $('.dados_turmas').next('.kt-separator').remove(); 
                    $('.dados_turmas').remove(); 
                  }
              },
            checkboxesUnidade: function()
              {
                var cb = $(this);
                var unidade = cb.data('unidade');
                var checked = cb.prop('checked');
                $(":checkbox.cb_escolaridade[data-unidade='" + unidade + "'], :checkbox.cb_servico_escolaridade[data-unidade='" + unidade + "']").prop('checked', checked);
              },
            checkboxesTurmas: function()
              {
                var cb = $(this);
                var unidade = cb.data('unidade');
                var servico = cb.data('servico');
                var checked = cb.prop('checked');
                $(":checkbox.cb_aluno[data-servico='" + servico + "'][data-unidade='" + unidade + "']").prop('checked', checked);
              },
            checkboxesAlunos: function()
              {
                var cb = $(this);
                var unidade = cb.data('unidade');
                var servico = cb.data('servico');
                var cbs = $(":checkbox.cb_aluno[data-servico='" + servico + "'][data-unidade='" + unidade + "']:checked");
                $(":checkbox.cb_servico_escolaridade[data-unidade='" + unidade + "']").prop('checked', (cbs.length > 0));
              },
            enviarCircular: function(e)
              {
                e.preventDefault();
                var form_data = new FormData(document.getElementById('form-circular'));
                if(form_data.get('tipo_circular') == 'texto_html')
                  {
                    form_data.set('conteudo_html', CKEDITOR.instances.editor.getData());
                  }
                $.ajax(
                  {
                    url: '/circulares/enviar',
                    data: form_data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    method: 'POST',
                    success: function(resposta)
                      {
                        if(resposta.success === true)
                          {
                            toastr.success('Circular enviada com sucesso!');
                            window.location.reload();
                          }
                        else
                          {
                            $.each(resposta.errors, (i, item) => toastr.error(item[Object.keys(item)[0]]));
                          }
                      }
                  });
              }
          };
        $(document).ready(function()
          {
            circulares.init();
          });
    </script>
    <?php
$this->end();
$this->append('css'); 
	echo $this->Html->css('"https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css');
$this->end(); ?>