<?php

require_once'app/conexao/conexao.php';

class DEPARTAMENTO {


	public static function save($nome,$descricao){

		$conexao = ligar();

		$string="INSERT INTO departamento(departamento, descricao) VALUES(:n,:d)";

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
	public static function listar_todas($pagina,$limite){

		$retorno=[];
		$offset = ($pagina - 1) * $limite; 
		$conexao = ligar();

		$string = "SELECT * FROM departamento LIMIT :limite OFFSET :offset";

		$insert=$conexao->prepare($string);
		$insert->bindValue(":offset",$offset,PDO::PARAM_INT);
        $insert->bindValue(':limite', $limite, PDO::PARAM_INT);
		$insert->execute();

		$totalSql = "SELECT COUNT(*) as total from departamento";
	 
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
	                'id' => $dados->iddepartamento,
	                'descricao' => $dados->descricao,
	                'nome' => $dados->departamento
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

		$string = "SELECT * FROM departamento where  departamento= :id";

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

    $string="UPDATE departamento set departamento=:n,descricao=:d WHERE iddepartamento=:id";

    $insert=$conexao->prepare($string);

    $insert->bindParam(":d",$descricao);
    $insert->bindParam(":n",$nome);
    
    $insert->bindParam(":id",$id);
    

    return $insert->execute() ? true : false;
}

}