<?php

require_once'app/conexao/conexao.php';

class CATEGORIA {


	public static function save($categoria,$descricao){

		$conexao = ligar();

		$string="INSERT INTO CATEGORIA(categoria,descricao) VALUES(:c,:d)";

		$insert=$conexao->prepare($string);
        $insert->bindParam(":c",$categoria);
        $insert->bindParam(":d",$descricao);
		
		return $insert->execute() ? true : false;
	}


	public static function edit($categoria,$descricao,$id){

		$conexao = ligar();

		$string="UPDATE CATEGORIA set descricao=:d, categoria=:c WHERE idcategoria=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":c",$categoria);
        $insert->bindParam(":d",$descricao);
		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}


	public static function eliminar($id){

		$conexao = ligar();

		$string="DELETE FROM categoria WHERE idcategoria=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}


//função para listar as especialidades de um medico
	public static function listar_todas(){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM categoria";

		$insert=$conexao->prepare($string);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [

                'id' => $dados->idcategoria,
                'descricao' => $dados->descricao,
                'categoria' => $dados->categoria
                
            ];
			}

			return $retorno;
		}
}


public static function listar_id($id){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM categoria where  idcategoria= :id";

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
                'categoria' => $dados->categoria
            ];
			}

			return $retorno;
		}
}



}