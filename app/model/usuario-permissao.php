<?php

require_once'app/conexao/conexao.php';

class USUARIOPERMISSAO {


	public static function save($idusuario,$idpermissao){

		$conexao = ligar();

		$string="INSERT INTO usuario_permissao(permissao_idpermissao,usuario_idusuario) VALUES(:p,:u)";

		$insert=$conexao->prepare($string);
        $insert->bindParam(":p",$idpermissao);
        $insert->bindParam(":u",$idusuario);
		
		return $insert->execute() ? true : false;
	}




	public static function eliminar($id){

		$conexao = ligar();

		$string="DELETE FROM usuario_permissao WHERE idusuario_permissao=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}




public static function listarPorUsuario($id){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM usuario_permissao u inner join permissao p on(u.permissao_idpermissao=p.idpermissao) where u.usuario_idusuario= :id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(":id",$id,PDO::PARAM_INT);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{


			while($dados=$insert->fetch(PDO::FETCH_OBJ)){
			 // Adiciona os dados ao array de retorno
            $retorno[] = [
                'id' => $dados->idusuario_permissao,
                'descricao' => $dados->descricao,
                'idusuario' => $dados->usuario_idusuario,
                'idpermissao' => $dados->idpermissao,
            ];
			}

			return $retorno;
		}
}




public static function verifica($idusuario,$idpermissao)
{

    $conexao = ligar();

    $tring="select * from usuario_permissao where usuario_idusuario=:u and permissao_idpermissao=:p";
    $busca=$conexao->prepare($tring);

    $busca->bindParam(':u',$idusuario);
    $busca->bindParam(':p',$idpermissao);
    $busca->execute();

    if($busca->rowCount()>0){
        return true;
    }else{
        return false;
    }

}



}