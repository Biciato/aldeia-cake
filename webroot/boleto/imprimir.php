<?php
	header ('Content-type: text/html; charset=UTF-8');

	$dados       = $_POST['data'];
	$dados       = $dados['Boleto'];
	$dadosboleto = $dados;

	require('funcoes_santander.php');
	require('layout_santander.php');
?>
