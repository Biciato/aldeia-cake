<?php echo $this->Form->create(null, ['id' => 'boleto-individual', 'url' => false]); ?>
    <div class="row form-group">
        <div class="col-sm-6">
            <label for="codigo">Código de Barras ou Linha Digitável</label>
            <input class="form-control" type="text" disabled="disabled" value="<?php echo $codigo; ?>">
            <div class="form-text"></div>
        </div>
        <div class="col-sm-6">
            <label for="codigo">Unidade</label>
            <input class="form-control" type="text" disabled="disabled" value="<?php echo $boleto->unidade->nome . " | " .  $boleto->unidade->razao_social; ?>">
            <div class="form-text"></div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-4">
            <label for="codigo">Aluno</label>
            <input class="form-control" type="text" disabled="disabled" value="<?php echo $boleto->pessoa->nome; ?>">
            <div class="form-text"></div>
        </div>
        <div class="col-sm-4">
            <label for="codigo">Valor</label>
            <input class="form-control" type="text" disabled="disabled" value="<?php echo number_format(($boleto->valor_com_desconto/100), 2, ',', '.'); ?>">
            <div class="form-text"></div>
        </div>
        <div class="col-sm-4">
            <label for="codigo">Vencimento</label>
            <input class="form-control" type="text" disabled="disabled" value="<?php echo $boleto->data_vencimento->format('d/m/Y'); ?>">
            <div class="form-text"></div>
        </div>
    </div>
    <div class="row form-group">
       <div class="col-sm-6">
            <label for="codigo">Valor</label>
            <input class="form-control" type="text" name="valor" id="valor_baixa" value="<?php echo number_format(($boleto->valor_atualizado/100), 2, ',', '.'); ?>">
            <div class="form-text"></div>
        </div> 
        <div class="col-sm-6">
            <label for="codigo">Data de pagamento</label>
            <input class="form-control" type="text" name="data_liquidacao" id="data_liquidacao_baixa" value="<?php echo date('d/m/Y'); ?>">
            <div class="form-text"></div>
        </div>
        <input type="hidden" name="id" value="<?php echo $boleto->id; ?>">
        <input type="hidden" name="confirmar_baixa" value="true">
    </div>
    <div class="row" style="margin-top:20px">
        <div class="col-sm-12" style="text-align:right; justify-content: flex-end">
            <button class="btn btn-success" style="margin-bottom: 25px;" id="dar-baixa">Dar baixa</button>
        </div>
    </div>
<?php echo $this->Form->end(); ?>