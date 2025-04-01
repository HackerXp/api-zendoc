<?php

require_once'app/conexao/conexao.php';

class USUARIO {


public static function save($nome,$usuario,$senha,$email,$departamento){

		$conexao = ligar();

		$string="INSERT INTO USUARIO(nome,usuario,senha,email,iddepartamento) VALUES(:n,:u,:s,:e,:d)";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":n",$nome);
		$insert->bindParam(":u",$usuario);
		$insert->bindParam(":s",$senha);
		$insert->bindParam(":e",$email);
		$insert->bindParam(":d",$departamento);
		
		

		return $insert->execute() ? $conexao->lastInsertId() : 0;
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
public static function listar_todas($pagina,$limite){

		$retorno=[];

		$offset = ($pagina - 1) * $limite; 

		$conexao = ligar();

		$string = "SELECT * FROM usuario LIMIT :limite OFFSET :offset";

		$insert=$conexao->prepare($string);
		$insert->bindValue(":offset",$offset,PDO::PARAM_INT);
        $insert->bindValue(':limite', $limite, PDO::PARAM_INT);
		$insert->execute();

		     // Conta o total de registros para cálculo de páginas
			 $totalSql = "SELECT COUNT(*) as total from usuario";
	 
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

                'id' => $dados->idusuario,
                'nome' => $dados->nome,
                'usuario' => $dados->usuario,
                'iddepartamento' => $dados->iddepartamento,
                'email' => $dados->email    
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
				'iddepartamento' => $dados->iddepartamento,
                'email' => $dados->email
            ];
			}

			return $retorno;
		}
}


public static function listar_idDepartamento($id,$dept){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM usuario where  iddepartamento = :dept and idusuario <> :id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$id,PDO::PARAM_INT);
		$insert->bindParam(":dept",$dept,PDO::PARAM_INT);
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
				'iddepartamento' => $dados->iddepartamento,
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
				'iddepartamento' => $dados->iddepartamento,
                'email' => $dados->email
            ];
			}

			return $retorno;
		}
}

}