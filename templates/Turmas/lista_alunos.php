<html>
	<head>
		<title>
			<?php
				echo $turma->Unidade->nome;
				echo ' - ';
				echo $turma->ano_letivo;
				echo ' - ';
				echo 'Turma ';
				echo ($turma->Servico->nome == 'Hotelaria') ? 'Sistema Creche' : $turma->Servico->nome;
				echo ' ';
				echo $turma->nome;
			?>
		</title>
		
		<style type="text/css">
			@page 
			{
				size: A4;
				margin: 0px;
				margin-top: 0mm;
				margin-bottom: 0mm;
			}
	
			body	 
			{
				background-color: #FFFFFF;
				border: solid 0px black;
				padding:10.5mm;
				margin:0px;
				font-family: "Calibri";
				font-size  : 12px;
				width: 210mm;
				height: auto;
			}
			
			*
			{
				box-sizing: border-box;
			}
			
			.page-break	
			{ 
				display: block; page-break-before: always; 
				padding-top: 10.5mm;
			}
			
			.aluno
			{
				width: 59mm;
				float: left;
				margin: 4mm 2mm;
				height: 30mm;
			}
			
			.aluno1, .aluno2
			{
				margin-right: 5mm;
			}
			
			.clear
			{
				clear : both;
			}
			
			.agrupamento_nivel
			{
				float        : left;
				font-weight  : bold;
			}
			
			.turno_permanencia_horario
			{
			    float        : right;
			}
			
			.matricula
			{
				float: left;
				width: 30px;
			}
			
			.nome
			{
				float       : left;
				width       : 134px;
				font-weight : bold;
			}
			
			.data
			{
				float: right;
			}
			
			.titulo
			{
				font-size: 18px;
				height: 16mm;
			}
			
			.subtitulo
			{
				font-size : 16px;
			}
			
			.idade_atual
			{
				float     : left; 
				font-size : 11px;
			}
			
			.idade_marco_ano_atual
			{
				float      : right;
				font-size  : 11px;
			}
			
			.sigla_turma
			{
				float: left;
				width: 20px;
				font-weight: bold;
			}
			
			.numero_turma
			{
				float       : left;
				font-weight : bold;
				width       : 30px;
			}
			
			.detalhes_turma
			{
			    float: left;
			}
		</style>
		
	</head>
	<body>
	
		<div class="titulo">
			<?php 
				echo $turma->Unidade->nome;
				echo ' - ';
				echo $turma->ano_letivo;
				echo ' - ';
				echo 'Turma ';
				echo ($turma->Servico->nome == 'Hotelaria') ? 'Sistema Creche' : $turma->Servico->nome;
				echo ' ';
				echo $turma->nome;
			?>
			
			<div class="subtitulo">
				<?php
					echo 'Data da impressão: ' . date('d/m/Y H:i:s');
				?>
				
				<?php 
					echo '<div class="clear"></div>';
					echo 'Página 1 de '. ceil(count($alunos)/21);
				?>
			</div>
		</div>
		
		<div class="clear"></div>
		
		<?php
			$contador       = 0;
			$contador_total = 0;
			$pagina         = 1;
			foreach($alunos as $aluno)
			{
				$contador       += 1;
				$contador_total += 1;
				?>
				<div class="aluno aluno'<?php echo $contador; ?>'">
			
					<div class="matricula">
					(<?php echo $aluno->matricula; ?>)	
					</div>
			
					<div class="nome">
						<?php echo $aluno->pessoa->nome; ?>
					</div>
					
					<div class="data">
                        <?php echo $aluno->pessoa->data_nascimento->format('d/m/Y'); ?>
                    </div>
					
					<div class="clear"></div>
					
					<div class="idade_atual">
                        <?php 
                            $bday = new \DateTime($aluno->pessoa->data_nascimento->format('Y-m-d'));
                            $today = new \Datetime();
                            $diff = $today->diff($bday);
                            $idade = $diff->y . 'a';
                            if($diff->m != 0)
                              {
                                $idade .= ' ' . $diff->m . 'm';
                              }
                        ?>
						Idade atual <?php echo $idade; ?>
					</div>
			
					<div class="idade_marco_ano_atual">
                        <?php 
                            $marco = new \DateTime(date('Y') . '-03-01');
                            $diff = $marco->diff($bday);
                            $idade_marco = $diff->y . 'a';
                            if($diff->m != 0)
                              {
                                $idade_marco .= ' ' . $diff->m . 'm';
                              }
                        ?>
						(em março ano atual <?php echo $idade_marco; ?>)
					</div>
					
					<div class="clear"></div>
					
					<div class="agrupamento_nivel">
                        <?php echo $aluno->Agrupamento->nome . ' ' . $aluno->Nivel->nome; ?>
					</div>
			
					<div class="turno_permanencia_horario">
						<?php echo $aluno->Turno->nome . ', ' . $aluno->Permanencia->nome . ', ' . $aluno->Horario->nome; ?>
					</div>
					
					<div class="clear"></div>
					<?php
					foreach($aluno->turmas_entities as $servico => $turma)
					{
                        ?>
						<div>
							<div class="sigla_turma">
								<?php echo ($turma->Servico->nome == "Hotelaria") ? 'SC' : substr($turma->Servico->nome, 0, 1); ?>
							</div>
							
							<div class="numero_turma">
								<?php str_pad($turma->nome, 3, "0", STR_PAD_LEFT); ?>
							</div>
							
							<div class="detalhes_turma">
                             <?php
								if((!is_null($turma->horario_inicial))&&(!is_null($turma->horario_final)))
								{
									echo '( ';
										
										$dias_semana = array();
										if(in_array(0, $turma->dias_semana_array)) { $dias_semana[] = 'dom'; }
										if(in_array(1, $turma->dias_semana_array)) { $dias_semana[] = 'seg'; }
										if(in_array(2, $turma->dias_semana_array)) { $dias_semana[] = 'ter';}
										if(in_array(3, $turma->dias_semana_array)) { $dias_semana[] = 'qua';}
										if(in_array(4, $turma->dias_semana_array)) {$dias_semana[] = 'qui';} 
										if(in_array(5, $turma->dias_semana_array)) { $dias_semana[] = 'sex';}
										if(in_array(6, $turma->dias_semana_array)) {$dias_semana[] = 'sáb';}
										
										echo implode(' ', $dias_semana);
										echo '&nbsp;&nbsp;&nbsp;';
										echo $turma->horario_inicial->format('H:i');
										echo ' às ';
										echo $turma->horario_final->format('H:i');
									
									echo ' )';
                                }
                            ?>
							</div>
							
							<div class="clear"></div>
						</div>
                        <?php
					}
                    ?>
				</div>
				<?php
				if($contador == 3)
				{
					$contador = 0;
					?>
					    <div class="clear"></div>
                    <?php
                }
                
				if($contador_total == 21)
				{
					$contador_total = 0;
					$pagina += 1;
					?>
					<div class="page-break"></div>
					
					<div class="titulo">
                        <?php 
                            echo $turma->Unidade->nome;
                            echo ' - ';
                            echo $turma->ano_letivo;
                            echo ' - ';
                            echo 'Turma ';
                            echo ($turma->Servico->nome == 'Hotelaria') ? 'Sistema Creche' : $turma->Servico->nome;
                            echo ' ';
                            echo $turma->nome;
                        ?>
						<div class="subtitulo">
							Data da impressão: <?php date('d/m/Y H:i:s') ?>;
							<div class="clear"></div>
							Página <?php echo $pagina ?> de <?php echo ceil(count($alunos)/21) ?>;
						</div>
					</div>
                    <?php
				}
            }
		?>
	</body>
</html>