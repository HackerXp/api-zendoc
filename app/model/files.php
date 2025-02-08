<?php

require_once'app/conexao/conexao.php';

class FILES {


	public static function save($files,$idusuario,$iddocumento){

       
		$conexao = ligar();

        $string="INSERT INTO files(nome,size,extension,documentoId,idusuario) VALUES(:n,:s,:e,:d,:u)";

        //pega o array de ficheiros para salvar 
        
        $file_count = count($files['files']);

        for ($i = 0; $i < $file_count; $i++) {
            
           
            @$file_tmp = $files['files']['tmp_name'][$i];

            @$file_error = $files['files']['error'][$i];

            @$file_size = $files['files']['size'][$i];

            //$file_ext = pathinfo($files['files']['name'][$i], PATHINFO_EXTENSION);
            @$file_ext=strchr($_FILES['files']['name'][$i],'.');

            @$file_name = date('Y').date('m').date('d').date('H').date('s').rand(0,99999).$file_ext;

           // chmod('app/files/', 0777, true);

            if ($file_error === UPLOAD_ERR_OK) {
               
                $destination = "app/files/" . $file_name;
                $insert=$conexao->prepare($string);

                $insert->bindParam(":n",$file_name);
                $insert->bindParam(":s",$file_size);
                $insert->bindParam(":e",$file_ext);
                $insert->bindParam(":d",$iddocumento);
                $insert->bindParam(":u",$idusuario);

           if( $insert->execute() ){

            move_uploaded_file($file_tmp, $destination);
           
           }else{

                return false;
                exit;
          }

		
        }
		
    }

    return true;
		
	}


	public static function eliminar($id){

		$conexao = ligar();

		$string="DELETE FROM files WHERE idfiles=:id";

		$insert=$conexao->prepare($string);

		$insert->bindParam(":id",$id);
		

		return $insert->execute() ? true : false;
	}



	public static function listar_todas($iddocumento){

		$retorno=[];

		$conexao = ligar();

		$string = "SELECT * FROM files where documentoId=:id";

		$insert=$conexao->prepare($string);
		$insert->bindParam(':id',$iddocumento);
		$insert->execute();

		if($insert->rowCount()<=0){

			return $retorno; 

		}else{

           
        while($dados=$insert->fetch(PDO::FETCH_OBJ)){
           
            $sizeInMB = $dados->size / (1024 * 1024); // Converte para megabytes
            $sizeInMB = round($sizeInMB, 2); // Arredonda para 2 casas decimais

        $retorno[] = [

            

            'idfiles' => $dados->idfiles,
            'iddocumento' => $dados->documentoId,
            'extension' => $dados->extension,
            'size' =>  $sizeInMB.'Mb',
            'nome' => $dados->nome,
            'url' => 'http://localhost/api/app/files/'.$dados->nome
            
        ];
        }

			return $retorno;
		}
}



}
