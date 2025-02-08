<?php

require_once'app/conexao/conexao.php';

class USUARIO {


public static function save($nome,$usuario,$senha,$email){

		$conexao = ligar();

		$string="INSERT INTO USUARIO(nome,usuario,senha,email) VALUES(:n,:u,:s,:e)";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":n",$nome);
		$insert->bindParam(":u",$usuario);
		$insert->bindParam(":s",$senha);
		$insert->bindParam(":e",$email);
		

		return $insert->execute() ? true : false;
	}



public static function edit($nome,$usuario,$email,$id){

		$conexao = ligar();

		$string="UPDATE USUARIO SET nome=:n,usuario=:u,email=:e WHERE idusuario=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":n",$nome);
		$insert->bindParam(":u",$usuario);
		$insert->bindParam(":id",$id);
		$insert->bindParam(":e",$email);

		return $insert->execute() ? true : false;
	}





public static function editPassword($senha,$id){

		$conexao = ligar();

		$string="UPDATE USUARIO set senha=:s WHERE idusuario=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":s",$senha);
		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	

}

//função para listar as especialidades de um medico
public static function listar_todas(){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM usuario";

		$insert=$conexao->prepare($string);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [

                'id' => $dados->idusuario,
                'nome' => $dados->nome,
                'usuario' => $dados->usuario,
                'email' => $dados->email
                
            ];
			}

			return $retorno;
		}
}



public static function listar_id($id){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM usuario where  idusuario= :id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$id,PDO::PARAM_INT);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [

                'id' => $dados->idusuario,
                'nome' => $dados->nome,
                'usuario' => $dados->usuario,
                'email' => $dados->email
            ];
			}

			return $retorno;
		}
}



public static function buscar($email,$usuario){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM usuario where  email= :e or usuario=:u";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":e",$email);
		$insert->bindParam(":u",$usuario);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [

                'id' => $dados->idusuario,
                'nome' => $dados->nome,
                'usuario' => $dados->usuario,
                'email' => $dados->email
            ];
			}

			return $retorno;
		}
}

}