<?php

require_once'app/conexao/conexao.php';

class DEPARTAMENTO {


	public static function save($nome,$descricao){

		$conexao = ligar();

		$string="INSERT INTO departamento(nome, descricao) VALUES(:n,:d)";

		$insert=$conexao->prepare($string);
        $insert->bindParam(":d",$descricao);
        $insert->bindParam(":n",$nome);
		
		return $insert->execute() ? true : false;
	}



	public static function eliminar($id){

		$conexao = ligar();

		$string="DELETE FROM departamento WHERE iddepartamento=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}


//função para listar as especialidades de um medico
	public static function listar_todas(){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM departamento";

		$insert=$conexao->prepare($string);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [

                'id' => $dados->iddepartamento,
                'descricao' => $dados->descricao,
                'nome' => $dados->nome
                
            ];
			}

			return $retorno;
		}
}


public static function listar_id($id){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM departamento where  iddepartamento= :id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$id,PDO::PARAM_INT);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [
                'id' => $dados->iddepartamento,
                'descricao' => $dados->descricao,
                'nome' => $dados->nome
            ];
			}

			return $retorno;
		}
}


public static function listar_nome($nome){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM departamento where  nome= :id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$nome);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [
                'id' => $dados->iddepartamento,
                'descricao' => $dados->descricao,
                'nome' => $dados->nome
            ];
			}

			return $retorno;
		}
}


public static function editar($descricao,$nome,$id){

    $conexao = ligar();

    $string="UPDATE departamento set nome=:n,descricao=:d WHERE iddepartamento=:id";

    $insert=$conexao->prepare($string);

    $insert->bindParam(":d",$descricao);
    $insert->bindParam(":n",$nome);
    
    $insert->bindParam(":id",$id);
    

    return $insert->execute() ? true : false;
}

}