<?php echo $this->Form->create(null, ['url' => false, 'id' => $unique]); ?>
<?php $total = (count($boletos_entrada) + count($boletos_baixa)); ?>
<div class="row">
    <div class="col-sm-12">
    <input name="<?php echo $unique ?>" type="hidden" value="">
    <input name="<?php echo $unique ?>[unidade]" type="hidden" value="<?php echo $unidade; ?>">
    <?php if(count($boletos_entrada))
      {
          ?>
          <p><b>Entradas de título</b></p>
          <table class="table table-condensed table-remessa">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Aluno
                        </th>
                        <th>
                            Tipo
                        </th>
                        <th>
                            Vencimento
                        </th>
                        <th class="valor">
                            Valor
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($boletos_entrada as $boleto)
                  {
                    ?>
                        <tr>
                            <td><?php echo $boleto->id; ?></td>
                            <td><?php echo $boleto->pessoa->nome; ?></td>
                            <td><?php echo $tipos[$boleto->tipo_boleto]['sigla']; ?></td>
                            <td><?php echo $boleto->data_vencimento; ?></td>
                            <td><?php echo $boleto->valor_formatado; ?>
                            <input name="<?php echo $unique ?>[entrada][]" type="hidden" value="<?php echo $boleto->id; ?>">
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php
                  }
                ?>
                </tbody>
          </table>
     <?php }
     if(count($boletos_baixa))
      {
          ?>
          <p><b>Pedido de baixa</b></p>
          <table class="table table-condensed table-remessa">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Aluno
                        </th>
                        <th>
                            Tipo
                        </th>
                        <th>
                            Vencimento
                        </th>
                        <th class="valor">
                            Valor
                        </th>
                        <th>
                            Valor pago
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($boletos_baixa as $boleto)
                  {
                    ?>
                        <tr>
                            <td><?php echo $boleto->id; ?></td>
                            <td><?php echo $boleto->pessoa->nome; ?></td>
                            <td><?php echo $tipos[$boleto->tipo_boleto]['sigla']; ?></td>
                            <td><?php echo $boleto->data_vencimento; ?></td>
                            <td><?php echo $boleto->valor_formatado; ?></td>
                            <td><?php echo $boleto->valor_liquido_recebido_formatado; ?>
                            <input name="<?php echo $unique ?>[baixa][]" type="hidden" value="<?php echo $boleto->id; ?>">
                            </td>
                        </tr>
                    <?php
                  }
                ?>
                </tbody>
          </table>
     <?php } 
     if($total === 0)
       {
         ?>
         <p>Não existem boletos pendentes para remessa nessa unidade</p>
         <?php
       }?>
    </div>
</div>
<?php if($total > 0)
      {
          ?>
            <div class="row" style="margin-top:20px">
                <div class="col-sm-12" style="text-align:right; justify-content: flex-end">
                    <button class="btn btn-success gerar-remessa" data-fields="<?php echo $unique; ?>" style="margin-bottom: 25px;" >Gerar remessa</button>
                </div>
            </div>
          <?php
      }?>
<?php echo $this->Form->end(); ?>