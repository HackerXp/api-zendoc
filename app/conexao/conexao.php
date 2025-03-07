<?php 

function ligar(){

	$ususario="root";
	$senha="root";
	$host="mysql:host=localhost;dbname=documental";

	try{
		$utf=array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES UTF8');
		$conexao=new PDO($host,$ususario,$senha,$utf);
		return $conexao;

	}catch(Exception $e){
		echo $e->getMessage();
	}
}



