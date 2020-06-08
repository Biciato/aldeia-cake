<div class="row">
    <div class="col-sm-12">
        <?php if(count($dados_nf) > 0)
          {
              ?>
              <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Unidade</th>
                        <th>Data de geração</th>
                        <th>Número de RPS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dados_nf as $dados)
                        {
                            ?>
                            <tr>
                                <td>
                                    <?php echo $dados['unidade']->nome; ?>
                                </td>
                                <td>
                                    <?php echo $dados['lote']->data_criacao; ?> 
                                </td>
                                <td>
                                    <?php echo count($dados['lote']->rps); ?>
                                </td>
                            </tr>
                            <?php
                        } ?>
                </tbody>
              </table>
              <?php
          }
        else
          {
              ?>
              <p>Não existem lotes para gerar ou enviar esse mês</p>
              <?php
          } ?>
          
    </div>
</div>
<?php 
if(count($dados_nf) > 0)
  {
    ?>
    <div class="row" style="margin-top:20px">
        <div class="col-sm-12" style="text-align:right; justify-content: flex-end">
            <button class="btn btn-success" style="margin-bottom: 25px;" id="enviar-rps">Enviar lotes</button>
        </div>
    </div>
    <?php
  }
?>