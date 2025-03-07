<?php

require_once'app/conexao/conexao.php';
require_once'app/model/files.php';

class DOCUMENTO {


	public static function save($titulo,$tipo,$descricao,$categoria,$departamento,$tags,$usuario){

		$conexao = ligar();

		$string="INSERT INTO documento(titulo,tipo,descricao,categoria_idcategoria,departamento_iddepartamento,tags,usuario_idusuario)
         VALUES(:t,:ti,:d,:c,:de,:ta,:us)";

		$insert=$conexao->prepare($string);
        $insert->bindParam(":t",$titulo);
        $insert->bindParam(":ti",$tipo);
        $insert->bindParam(":d",$descricao);
        $insert->bindParam(":c",$categoria);
        $insert->bindParam(":de",$departamento);
        $insert->bindParam(":ta",$tags);
        $insert->bindParam(":us",$usuario);
		
		return $insert->execute() ? $conexao->lastInsertId() : 0;
	}


	public static function edit($descricao,$id){

		$conexao = ligar();

		$string="UPDATE  documento SET titulo=:t,tipo=:ti,descricao=:d,categoria_idcategoria=:c,departamento_iddepartamento=:de,tags=:ta,usuario_idusuario=:us
         where iddocumento=:id";

		$insert=$conexao->prepare($string);
        $insert->bindParam(":t",$titulo);
        
        $insert->bindParam(":ti",$tipo);
        $insert->bindParam(":d",$descricao);
        $insert->bindParam(":c",$categoria);
        $insert->bindParam(":de",$departamento);
        $insert->bindParam(":ta",$tags);
        $insert->bindParam(":us",$usuario);
		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}


	public static function eliminar($id){

		$conexao = ligar();

        $sq=$conexao->prepare("SELECT * FROM files WHERE documentoId=$id");
        $sq->execute();
        
        while($dados=$sq->fetch(PDO::FETCH_OBJ)){

        $d="DELETE FROM files WHERE idfiles=:id";

		$dd=$conexao->prepare($d);

		$dd->bindParam(":id",$dados->idfiles);

        $dd->execute();

        }

		$string="DELETE FROM documento WHERE iddocumento=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}


//função para listar as especialidades de um medico
public static function listar_todos($pagina,$limite){

		$retorno=[];
        $offset = ($pagina - 1) * $limite; 
		$conexao = ligar();

		$string = "SELECT * FROM documento d 
        inner join categoria c on(categoria_idcategoria=idcategoria) 
        inner join departamento de on(departamento_iddepartamento=iddepartamento) 
        inner join usuario on(usuario_idusuario=idusuario)  LIMIT :limite OFFSET :offset";

		$insert=$conexao->prepare($string);
        $insert->bindValue(":offset",$offset,PDO::PARAM_INT);
        $insert->bindValue(':limite', $limite, PDO::PARAM_INT);
		$insert->execute();


        // Conta o total de registros para cálculo de páginas
		 $totalSql = "SELECT count(*) as total FROM documento d 
        inner join categoria c on(categoria_idcategoria=idcategoria) 
        inner join departamento de on(departamento_iddepartamento=iddepartamento) 
        inner join usuario on(usuario_idusuario=idusuario)";
	 
		 $totalStmt = $conexao->prepare($totalSql);
		 $totalStmt->execute();
		 $totalRegistros = $totalStmt->fetch(PDO::FETCH_OBJ)->total;
 
	 // Calcula o total de páginas
		$totalPaginas = ceil($totalRegistros / $limite);

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			
           $files=FILES::listar_todas($dados->iddocumento);

            $retorno[] = [

                'id' => $dados->iddocumento,
                'titulo' => $dados->titulo,
                'tipo' => $dados->tipo,
                'descricao' => $dados->descricao,
                'categoria' => $dados->categoria,
                'idcategoria' => $dados->idcategoria,
                'departamento' => $dados->departamento,
                'iddepartamento' => $dados->iddepartamento,
                'tags' => $dados->tags,
                'usuario' => $dados->nome,
                'idusuario' => $dados->idusuario,
                'data_criacao' => $dados->data_criado,
                'files'=> empty($files) ? 'nenhum arquivo associado' : $files
                
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

		$string = "SELECT * FROM documento d 
        INNER JOIN categoria c ON (categoria_idcategoria = idcategoria) 
        INNER JOIN departamento de ON (departamento_iddepartamento = iddepartamento) 
        INNER JOIN usuario u ON (usuario_idusuario = idusuario) where iddocumento=:id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$id,PDO::PARAM_INT);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
             $files=FILES::listar_todas($dados->iddocumento);
 
             $retorno[] = [
                 'id' => $dados->iddocumento,
                 'titulo' => $dados->titulo,
                 'tipo' => $dados->tipo,
                 'descricao' => $dados->descricao,
                 'categoria' => $dados->categoria,
                 'idcategoria' => $dados->idcategoria,
                 'departamento' => $dados->departamento,
                 'iddepartamento' => $dados->iddepartamento,
                 'tags' => $dados->tags,
                 'usuario' => $dados->nome,
                 'idusuario' => $dados->idusuario,
                 'data_criacao' => $dados->data_criado,
                 'files' => empty($files) ? 'nenhum arquivo associado' : $files
             ];
			}

			return $retorno;
		}
}


public static function listar_id_categoria($id,$pagina,$limite){

		$retorno=[];
        $offset = ($pagina - 1) * $limite; 
		$conexao = ligar();

		$string = "SELECT * FROM documento d 
        INNER JOIN categoria c ON (categoria_idcategoria = idcategoria) 
        INNER JOIN departamento de ON (departamento_iddepartamento = iddepartamento) 
        INNER JOIN usuario u ON (usuario_idusuario = idusuario) where c.idcategoria=:id LIMIT :limite OFFSET :offset";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$id,PDO::PARAM_INT);
        $insert->bindValue(":offset",$offset,PDO::PARAM_INT);
        $insert->bindValue(':limite', $limite, PDO::PARAM_INT);
		$insert->execute();


        // Conta o total de registros para cálculo de páginas
		 $totalSql = "SELECT count(*) as total FROM documento d 
        INNER JOIN categoria c ON (categoria_idcategoria = idcategoria) 
        INNER JOIN departamento de ON (departamento_iddepartamento = iddepartamento) 
        INNER JOIN usuario u ON (usuario_idusuario = idusuario) where c.idcategoria=:id";
      
          $totalStmt = $conexao->prepare($totalSql);
          $totalStmt->bindValue(":id",$id);
          $totalStmt->execute();
          $totalRegistros = $totalStmt->fetch(PDO::FETCH_OBJ)->total;
  
      // Calcula o total de páginas
         $totalPaginas = ceil($totalRegistros / $limite);

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
             $files=FILES::listar_todas($dados->iddocumento);
 
             $retorno[] = [
                 'id' => $dados->iddocumento,
                 'titulo' => $dados->titulo,
                 'tipo' => $dados->tipo,
                 'descricao' => $dados->descricao,
                 'categoria' => $dados->categoria,
                 'idcategoria' => $dados->idcategoria,
                 'departamento' => $dados->departamento,
                 'iddepartamento' => $dados->iddepartamento,
                 'tags' => $dados->tags,
                 'usuario' => $dados->nome,
                 'idusuario' => $dados->idusuario,
                 'data_criacao' => $dados->data_criado,
                 'files' => empty($files) ? 'nenhum arquivo associado' : $files
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

public static function listar_por_categoria(){

    $retorno=[];

    $conexao = ligar();

    $string = "SELECT 
                c.categoria, 
                c.idcategoria, 
                COUNT(d.iddocumento) AS total_documentos
                FROM 
                documento d
                INNER JOIN categoria c ON d.categoria_idcategoria = c.idcategoria
                INNER JOIN departamento de ON d.departamento_iddepartamento = de.iddepartamento
                INNER JOIN usuario u ON d.usuario_idusuario = u.idusuario
                GROUP BY 
                c.categoria
                ";

    $insert=$conexao->prepare($string);
    $insert->execute();

    if($insert->rowCount()<=0){

        return $retorno; 

    }else{

        while($dados=$insert->fetch(PDO::FETCH_OBJ)){
    
        $retorno[] = [
            'id' => $dados->idcategoria,
            'categoria' => $dados->categoria,
            'total' => $dados->total_documentos,
            'icon' => 'icon-doc'
        ];
        }

        return $retorno;
    }
}

public static function buscaAvancada($searchTerm) {
    $retorno = [];
    $conexao = ligar();

    // Monta a consulta SQL com filtro de pesquisa
    $string = "SELECT * FROM documento d 
        INNER JOIN categoria c ON (categoria_idcategoria = idcategoria) 
        INNER JOIN departamento de ON (departamento_iddepartamento = iddepartamento) 
        INNER JOIN usuario u ON (usuario_idusuario = idusuario) WHERE 1=1";

    // Adiciona filtro de pesquisa
    if ($searchTerm) {
        $string .= " AND (d.titulo LIKE :searchTerm
            OR c.categoria LIKE :searchTerm
            OR de.departamento LIKE :searchTerm
            OR u.nome LIKE :searchTerm
            OR d.descricao LIKE :searchTerm
            OR d.tags LIKE :searchTerm)";
    }

    $insert = $conexao->prepare($string);

    // Vincula o parâmetro de pesquisa
    if ($searchTerm) {
        $insert->bindValue(':searchTerm', '%' . $searchTerm . '%');
    }

    $insert->execute();

    if ($insert->rowCount() <= 0) {
        return $retorno;
    } else {
        while ($dados = $insert->fetch(PDO::FETCH_OBJ)) {
            
            $files=FILES::listar_todas($dados->iddocumento);

            $retorno[] = [
                'id' => $dados->iddocumento,
                'titulo' => $dados->titulo,
                'tipo' => $dados->tipo,
                'descricao' => $dados->descricao,
                'categoria' => $dados->categoria,
                'idcategoria' => $dados->idcategoria,
                'departamento' => $dados->departamento,
                'iddepartamento' => $dados->iddepartamento,
                'tags' => $dados->tags,
                'usuario' => $dados->nome,
                'idusuario' => $dados->idusuario,
                'data_criacao' => $dados->data_criado,
                'files' => empty($files) ? 'nenhum arquivo associado' : $files
            ];
        }

        return $retorno;
    }
}


public static function verifica($categoria,$departamento,$titulo,$tags)
{

    $conexao = ligar();

    $tring="select * from documento where  categoria_idcategoria=:c and departamento_iddepartamento=:d and titulo=:t
    and tags=:ta";
    $busca=$conexao->prepare($tring);

    $busca->bindParam(':c',$categoria);
    $busca->bindParam(':d',$departamento);
    $busca->bindParam(':t',$titulo);
    $busca->bindParam(':ta',$tags);
    $busca->execute();

    if($busca->rowCount()>0){
        return true;
    }else{
        return false;
    }

}

}