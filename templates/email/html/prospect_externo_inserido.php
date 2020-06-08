<?php 
extract($data);
extract($config);
?>
    <p>
    	<b>Dados do prospect:</b> <br/>
    	<b>Responsável: </b> <?php echo $parentePessoa->nome; ?> (<?php echo $parentescos[$parenteEntity->parentesco]; ?>) <br/>
    	<b>Telefone: </b> <?php echo $parentePessoa->telefones_array[0]; ?> <br/>
    	<b>Email: </b> <?php echo $parentePessoa->email; ?> <br/>
    	<b>Aluno: </b> <?php echo $pessoa->nome; ?> <br/>
    	<b>Data de nascimento: </b> <?php echo $pessoa->data_nascimento_formatada; ?> <br/>
    	<b>Unidade da visita: </b> <?php echo $unidades[$prospect->unidade]; ?> <br/>
    	<b>Data e horário da visita: </b> <?php echo $interacao->data_completa; ?> <br/>
    	<?php if($prospect->como_conheceu)
    	  {
    	  	?>
    	  	<b>Como conheceu: </b> <?php echo $meios_conhecimento[$prospect->como_conheceu]; ?> <br/>
    	  	<?php
    	  }
    	?>
    </p>