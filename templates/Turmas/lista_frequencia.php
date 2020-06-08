<?php
	$meses = array
	(
		1  => 'Janeiro',
		2  => 'Fevereiro',
		3  => 'Março',
		4  => 'Abril',
		5  => 'Maio',
		6  => 'Junho',
		7  => 'Julho',
		8  => 'Agosto',
		9  => 'Setembro',
		10 => 'Outubro',
		11 => 'Novembro',
		12 => 'Dezembro'
	);


?>

<html>
	<head>
		<title>Controle de Frequência</title>
		<style type="text/css">
			img
			{
				width           : 60px;
				float           : left;
			}

			.cabecalho
			{
				font-family     : Verdana, Arial, Helvetica, sans-serif;
				font-size       : 22px;
				color           : #666666;
				text-decoration : none;
				font-weight     : bold;
				font-style      : italic;
				float           : left;
				margin          : 0px;
				padding-left    : 20px;
                padding-top     : 15px;
			}

			.turma
			{
				font-family     : Verdana, Arial, Helvetica, sans-serif;
				font-size       : 18px;
				color           : #666666;
				text-decoration : none;
				font-style      : italic;
				margin          : 0px;
				padding-top     : 20px;
			}

			.orientador
			{
				font-family     : Verdana, Arial, Helvetica, sans-serif;
				font-size       : 18px;
				color           : #666666;
				text-decoration : none;
				font-style      : italic;
				margin          : 0px;
				padding-top     : 5px;
			}

			.impressao
			{
				font-family     : Verdana, Arial, Helvetica, sans-serif;
				font-size       : 9px;
				color           : #666666;
				text-decoration : none;
				margin          : 0px;
				padding-top     : 10px;
				padding-bottom  : 20px;
			}

			table, tr, td
			{
				padding         : 0px;
				margin          : 0px;
				border          : 0px;
				border-collapse : collapse;
			}

			td
			{
				padding         : 2px;
			}

			.nome_aluno
			{
				font-family     : Verdana, Arial, Helvetica, sans-serif;
				font-size       : 9px;
				color           : #666666;
				text-decoration : none;
				font-weight     : bold;
				border-bottom   : 1px solid black;
			}

			.matricula_aluno
			{
				font-family     : Verdana, Arial, Helvetica, sans-serif;
				font-size       : 9px;
				color           : #666666;
				text-decoration : none;
				border-bottom   : 1px solid black;
				width           : 40px;
			}

			.ordem_aluno
			{
				font-family     : Verdana, Arial, Helvetica, sans-serif;
				font-size       : 9px;
				color           : #666666;
				text-decoration : none;
				border-bottom   : 1px solid black;
				width           : 25px;
			}

			.numero_dia
			{
				font-family     : Verdana, Arial, Helvetica, sans-serif;
				font-size       : 9px;
				color           : #666666;
				text-decoration : none;
				border-bottom   : 1px solid black;
				width           : 20px;
			}

			.clear
			{
				clear           : both;
			}

			.par
			{
				background-color: #cccccc;
			}
		</style>
	</head>
	<body>
		<img src="/img/logotipo.png">
		<h1 class="cabecalho">Formulário para Controle de Frequência | <?php echo $meses[$mes]; ?> de <?php echo $ano; ?></h1>
		<div class="clear"></div>
		<h2 class="turma">Turma: <?php echo str_pad($turma->nome, 3, '0', STR_PAD_LEFT); ?> <?php echo ($turma->Servico->nome == 'Hotelaria') ? 'Servico Creche' : $turma->Servico->nome; ?></h2>
		<?php
            $lista_colaboradores = [];
			foreach($colaboradores as $colaborador) {
				$lista_colaboradores[] = $colaborador->pessoa->nome;
			}
		?>
		<h2 class="orientador">Orientador(es): <?php echo implode(', ', $lista_colaboradores); ?></h2>
		<h4 class="impressao">Data da impressão: <?php echo date('d/m/Y H:i:s'); ?></h4>

		<table>
			<tr>
				<td class="ordem_aluno">&nbsp;</td>
				<td class="matricula_aluno">&nbsp;</td>
				<td class="nome_aluno">&nbsp;</td>
				<?php
					for($i = 1; $i <= date('t', strtotime(date('Y').'-'.$mes.'-01')); $i++)
					{
						$cl = 'impar';

						if($i % 2 === 0)
						{
							$cl = 'par';
						}

						echo '<td class="numero_dia '.$cl.'" align="center">';
							echo $i;
						echo '</td>';
					}
				?>
			</tr>
			<?php
				foreach($alunos as $key => $aluno)
				{
					echo '<tr>';

						echo '<td class="ordem_aluno">';
							echo ($key + 1);
						echo '</td>';

						echo '<td class="matricula_aluno">';
							echo '(';

								echo str_pad($aluno->matricula, 3, '0', STR_PAD_LEFT);

							echo ')';
						echo '</td>';

						echo '<td class="nome_aluno">';
							echo mb_strtoupper($aluno->pessoa->nome);
						echo '</td>';

						get_dias($mes);

					echo '</tr>';
				}
			?>
		</table>
	</body>
</html>

<?php
	function get_dias($mes)
	{
		for($i = 1; $i <= date('t', strtotime(date('Y').'-'.$mes.'-01')); $i++)
		{
			$cl = 'impar';

			if($i % 2 === 0)
			{
				$cl = 'par';
			}

			echo '<td class="numero_dia '.$cl.'">';
				echo '&nbsp;';
			echo '</td>';
		}
	}
?>
