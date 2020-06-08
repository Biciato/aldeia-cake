<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				Circulares enviadas
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
        <?php echo $this->Form->create(null, 
          [
            'id' => 'form-circular',
            'url' => false
          ]); 
        foreach($lotes as $lote)
          {
            ?>
            <div class="row accordion scope-0" data-scope="0" data-key="<?php echo $lote->id; ?>" data-parent-scope="" id="lote-<?php echo $lote->id; ?>">
                <div class="col-sm-12">
                    <h4><?php echo $lote->data_criacao->format('d/y/Y') ?> - <?php echo $lote->titulo; ?><span class="end"><?php echo count($lote->alunos_array); ?> alunos</span></h4>
                </div>	
            </div>
            <div class="kt-separator scope-0 kt-separator--space-sm"></div>
            <?php 
            $dados_leitura = [];
            $dados_leitura_alunos = [];
            $dados_media = 
              [
                'total' => 0,
                'lidas' => 0,
                'total_percentual' => 0
              ];
            foreach($lote->circulares as $circular)
              {
                if(!isset($dados_leitura[$circular->turma]))
                  {
                    $dados_leitura[$circular->turma] = 
                      [
                        'total' => 0,
                        'lidas' => 0
                      ];
                  }
                $dados_leitura[$circular->turma]['total']++;
                $dados_media['total']++;
                if($circular->lido)
                  {
                    $dados_leitura[$circular->turma]['lidas']++;
                    $dados_leitura_alunos[] = $circular->aluno;
                    $dados_media['lidas']++;
                  }
              }
            foreach($dados_leitura as &$dados)
              {
                $dados['percentual'] = (($dados['lidas']*100)/$dados['total']);
                $dados_media['total_percentual'] += $dados['percentual'];
              }
            $dados_media['media'] = ceil(($dados_media['lidas']/count($dados_leitura)));
            $dados_media['percentual'] = ($dados_media['total_percentual']/count($dados_leitura));
            ?>
            <div style="display:none" class="row accordion scope-1" data-scope="1" data-key="<?php echo $lote->id; ?>-turmas" data-parent-id="lote-<?php echo $lote->id; ?>" data-parent-scope="" id="lote-<?php echo $lote->id; ?>-turmas">
                <div class="col-sm-12">
                    <h4>Turmas <span class="end">Em média (<?php echo $dados_media['media']; ?>) <?php echo number_format($dados_media['percentual'], 2, ",", ""); ?>% lido</span></h4>
                </div>	
            </div>
            <div style="display:none" class="kt-separator scope-1 kt-separator--space-sm"></div> 
            <?php foreach($lote->turmas_entities as $turma)
              {
                ?>
                    <div style="display:none" class="row accordion scope-2 no-caret" data-scope="2" data-key="circular" data-parent-id="lote-<?php echo $lote->id; ?>-turmas" data-parent-scope="" id="lote-<?php echo $lote->id; ?>-turma-<?php echo $turma->id; ?>">
                        <div class="col-sm-12">
                            <h4><?php echo $turma->Unidade->nome . ' - ' . $turma->nome . ' - ' . $turma->Servico->nome; ?> <span class="end">(<?php echo $dados_leitura[$turma->id]['lidas']; ?>) <?php echo number_format($dados_leitura[$turma->id]['percentual'], 2, ",", ""); ?>% lido</span></h4>
                        </div>	
                    </div>
                    <div style="display:none" class="kt-separator scope-2 kt-separator--space-sm"></div> 
                <?php
              } 
            ?>
            <div style="display:none" class="row accordion scope-1" data-scope="1" data-key="circular" data-parent-id="lote-<?php echo $lote->id; ?>" data-parent-scope="" id="lote-<?php echo $lote->id; ?>-alunos">
                <div class="col-sm-12">
                    <h4>Alunos</h4>
                </div>	
            </div>
            <div style="display:none" class="kt-separator scope-1 kt-separator--space-sm"></div>     
            <?php foreach($lote->alunos_entities as $aluno)
              {
                ?>
                    <div style="display:none" class="row accordion scope-2 no-caret" data-scope="2" data-key="circular" data-parent-id="lote-<?php echo $lote->id; ?>-alunos" data-parent-scope="" id="lote-<?php echo $lote->id; ?>-aluno-<?php echo $aluno->id; ?>">
                        <div class="col-sm-12">
                            <h4><?php echo $aluno->pessoa->nome; ?> <span class="end"><?php if(in_array($aluno->id, $dados_leitura_alunos)){ ?> <i class="fa fa-check"></i> <?php } ?></span></h4>
                        </div>	
                    </div>
                    <div style="display:none" class="kt-separator scope-2 kt-separator--space-sm"></div>
                <?php 
              } 
            ?>
            <div style="display:none" class="row accordion scope-2 no-caret" data-scope="2" data-key="circular" data-parent-id="lote-<?php echo $lote->id; ?>-alunos" data-parent-scope="" id="lote-<?php echo $lote->id; ?>-alunos-acoes">
                <?php if($lote->tipo_circular == 'arquivo_pdf')
                  {
                    ?>
                        <div class="col-sm-12" style="margin-top:40px">
                            <a class="btn btn-success" href="<?php echo $this->Url->build('/', ['fullBase' => true]); ?>/circulares/<?php echo $lote->arquivo; ?>" target="_blank">Abrir circular</a>
                        </div>
                    <?php
                  }
                else
                  {
                    ?>
                    <div class="col-sm-12" style="margin-top:40px">
                        <div  style="font-family: verdana; background:  #FFF; margin:0; padding:0; font-size: 15px">
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
                                    <td align="center" valign="top">
                                        <?php echo $lote->texto; ?>
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
                    <?php
                  } 
                  ?>
                <div class="col-sm-12" style="margin-bottom:40px; margin-top:40px">
                  <a class="btn btn-success reenviar" href="javascript:void(0)" data-lote="<?php echo $lote->id; ?>">Reenviar para os que não leram</a>
                </div>	
            </div>
            <div style="display:none" class="kt-separator scope-2 kt-separator--space-sm"></div>
            <?php
          }
        echo $this->Form->end(); ?>
	</div>
</div>
<?php $this->append('script');
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
        var enviadas = 
          {
            init: function()
              {
                var self = this;
                $(document).on('click', '.accordion', self.carregarProximaSessao);  
                $(document).on('click', '.reenviar', self.reenviar);
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
            reenviar: function()
              {
                var id = $(this).data('lote');
                var token = $('input[name="_csrfToken"]').val();
                $.ajax(
                  {
                    url: '/circulares/reenviar',
                    data: {_csrfToken: token, id: id},
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(resposta)
                      {
                        if(resposta.success)
                          {
                            toastr.success('Circulares reenviadas com sucesso!');
                          }
                        else
                          {
                            toastr.error('Erro ao reenviar as circulares!');
                          }
                      }
                  });
              }
          };
        $(document).ready(function()
          {
            enviadas.init();
          });
    </script>
    <?php
$this->end();
?>