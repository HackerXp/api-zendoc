<?php

require_once'app/conexao/conexao.php';

class PERMISSAO {


	public static function save($descricao){

		$conexao = ligar();

		$string="INSERT INTO permissao(descricao) VALUES(:d)";

		$insert=$conexao->prepare($string);
        $insert->bindParam(":d",$descricao);
		
		return $insert->execute() ? true : false;
	}


	public static function edit($descricao,$id){

		$conexao = ligar();

		$string="UPDATE permissao set descricao=:d WHERE idpermissao=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":d",$descricao);
		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}


	public static function eliminar($id){

		$conexao = ligar();

		$string="DELETE FROM permissao WHERE idpermissao=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}


//função para listar as especialidades de um medico
	public static function listar_todas($pagina,$limite){

		$retorno=[];
		$offset = ($pagina - 1) * $limite; 
		$conexao = ligar();

		$string = "SELECT * FROM permissao LIMIT :limite OFFSET :offset";

		$insert=$conexao->prepare($string);
		$insert->bindValue(":offset",$offset,PDO::PARAM_INT);
        $insert->bindValue(':limite', $limite, PDO::PARAM_INT);
		$insert->execute();

		 // Conta o total de registros para cálculo de páginas
		 $totalSql = "SELECT COUNT(*) as total from permissao";
	 
		 $totalStmt = $conexao->prepare($totalSql);
		 $totalStmt->execute();
		 $totalRegistros = $totalStmt->fetch(PDO::FETCH_OBJ)->total;
 
	 // Calcula o total de páginas
		$totalPaginas = ceil($totalRegistros / $limite);

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [

                'id' => $dados->idpermissao,
                'descricao' => $dados->descricao,
                'estado' => $dados->estado
                
            ];
			}

			return [
				'data' => $retorno,
				'pagina_atual' => $pagina,
				'total_paginas' => $totalPaginas,
				'total_registros' => $totalRegistros,
				'mensagem'=>'operação realizada com sucesso!'
			];
		}
}


public static function listar_id($id){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM permissao where  idpermissao= :id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$id,PDO::PARAM_INT);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [
                'id' => $dados->idpermissao,
                'descricao' => $dados->descricao,
                'estado' => $dados->estado
            ];
			}

			return $retorno;
		}
}


public static function listar_nome($nome){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM permissao where  nome= :id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$nome);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [
                'id' => $dados->idpermissao,
                'descricao' => $dados->descricao,
                'estado' => $dados->estado
            ];
			}

			return $retorno;
		}
}


public static function disable($estado,$id){

    $conexao = ligar();

    $string="UPDATE permissao estado=:e WHERE idpermissao=:id";

    $insert=$conexao->prepare($string);

    $insert->bindParam(":e",$descricao);
    
    $insert->bindParam(":id",$id);
    

    return $insert->execute() ? true : false;
}

}