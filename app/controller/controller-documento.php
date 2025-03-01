<?php

require_once'app/model/documento.php';
require_once'app/model/files.php';

class DocumentoController
{


public function listarTodosDocumentos(){
    
        $retorno = DOCUMENTO::listar_todos();

        if(empty($retorno)){

        	$retorno=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
            http_response_code(600);
        	return json_encode($retorno);
        }else {
            http_response_code(200);

            $dados=[
            'data'=> $retorno,
            'mensagem'=>'operação realizada com sucesso!',
            'codigo'=>'200'];
            
            return json_encode($dados);
        }


    }

public function listarTodosDocumentosPorCategoria(){
    
        $retorno = DOCUMENTO::listar_por_categoria();

        if(empty($retorno)){

        	$retorno=['data'=>null,'mensagem'=>'Nenhuma informação encontrada','codigo'=>'600'];
            http_response_code(600);
        	return json_encode($retorno);
        }else {
            http_response_code(200);

            $dados=[
            'data'=> $retorno,
            'mensagem'=>'operação realizada com sucesso!',
            'codigo'=>'200'];
            
            return json_encode($dados);
        }


    }

public function listarDocumentosId($id){
    
        $retorno = DOCUMENTO::listar_id($id);

        if(empty($retorno)){

        	$retorno=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
        	return json_encode($retorno);
        }

        $dados=[
        'data'=>$retorno,
        'mensagem'=>'operação realizada com sucesso!',
        'codigo'=>'200'];
        
        return json_encode($dados);
    }

public function listarDocumentosIdCategoria($id){
    
        $retorno = DOCUMENTO::listar_id_categoria($id);

        if(empty($retorno)){

        	$retorno=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
        	return json_encode($retorno);
        }

        $dados=[
        'data'=>$retorno,
        'mensagem'=>'operação realizada com sucesso!',
        'codigo'=>'200'];
        
        return json_encode($dados);
    }

public function buscaAvancada(){
    
       $search=filter_input(INPUT_POST,'busca',FILTER_SANITIZE_SPECIAL_CHARS);

        $retorno = DOCUMENTO::buscaAvancada($search);

        if(empty($retorno)){

        	$retorno=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
        	return json_encode($retorno);
        }

        $dados=[
        'data'=>$retorno,
        'mensagem'=>'operação realizada com sucesso!',
        'codigo'=>'200'];
        
        return json_encode($dados);
    }




public function eliminarDocumento($id)
    {
    
        $retorno = DOCUMENTO::eliminar($id);

        if($retorno==0){

        	$retorno=['data'=>null,'mensagem'=>'id inválido','codigo'=>'600'];

 			http_response_code(400);

        	return json_encode($retorno);

        }else if($retorno){

        	http_response_code(200);

        	$retorno=['data'=>null,'mensagem'=>'operação realizada com sucesso','codigo'=>'200'];
        }else{
        	http_response_code(600);

        	$retorno=['data'=>null,'mensagem'=>'operação não realizada','codigo'=>'600'];
        }


        return json_encode($retorno);
    }


    public function salvarDocumento(){
      
        // Recebe os dados do formulário
        $idusuario = filter_input(INPUT_POST, 'idusuario', FILTER_SANITIZE_SPECIAL_CHARS);
        $categoria = filter_input(INPUT_POST, 'idcategoria', FILTER_SANITIZE_SPECIAL_CHARS);
        $departamento = filter_input(INPUT_POST, 'iddepartamento', FILTER_SANITIZE_SPECIAL_CHARS);
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
        $tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_SPECIAL_CHARS);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
    
        // Valida os campos para garantir que não estão vazios
        if (empty($categoria) || empty($idusuario) || empty($categoria) || empty($departamento) || empty($tipo)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '400'
            ];
            http_response_code(400); // Bad Request
            return json_encode($retorno);
        }
    
        if (DOCUMENTO::verifica($categoria, $departamento, $titulo, $tags)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Este documento já foi cadastrado!',
                'codigo' => '600'
            ];
            http_response_code(200); // OK
            return json_encode($retorno);
        }
    
        $destination = "../files/";
    
        $iddocumento = DOCUMENTO::save($titulo, $tipo, $descricao, $categoria, $departamento, $tags, $idusuario);
    
        if ($iddocumento != 0) {


            
            if (isset($_FILES['files']) && is_array($_FILES['files'])) {
                $retorno = FILES::save($_FILES, $idusuario, $iddocumento);
                
                $documento = DOCUMENTO::listar_id($iddocumento);
                if ($retorno) {
                    $retorno = [
                        'data' => $documento,
                        'mensagem' => 'Documento cadastrado com sucesso!',
                        'codigo' => '200'
                    ];
                    http_response_code(200); // OK
                } else {
                    $retorno = [
                        'data' => $documento,
                        'mensagem' => 'Documento cadastrado, ficheiros não carregados',
                        'codigo' => '500'
                    ];
                    http_response_code(500); // Internal Server Error
                }
            } else {
                $retorno = [
                    'data' => $documento,
                    'mensagem' => 'Nenhum arquivo enviado.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        } else {
            $retorno = [
                'data' => $documento,
                'mensagem' => 'Erro ao cadastrar o documento.',
                'codigo' => '500'
            ];
            http_response_code(500); // Internal Server Error
        }
    
        return json_encode($retorno);
    }
    






public function carregarDocumento(){

    $idusuario = filter_input(INPUT_POST, 'idusuario', FILTER_SANITIZE_SPECIAL_CHARS);
    $iddocumento = filter_input(INPUT_POST, 'iddocumento', FILTER_SANITIZE_SPECIAL_CHARS);

    if ( empty($idusuario) || empty($iddocumento)) {
        $retorno = [
            'data' => null,
            'mensagem' => 'Todos os campos são obrigatórios.',
            'codigo' => '400'
        ];
        http_response_code(400); // Bad Request

    } else {

        if (isset($_FILES['files'])) {

            $retorno=FILES::save($_FILES,$idusuario,$iddocumento);

            if($retorno){
                $retorno = [
                    'data' => null,
                    'mensagem' => 'ficheiros carregados com sucesso!',
                    'codigo' => '200'
                ];
                http_response_code(200); // Bad Request
            }else{
                $retorno = [
                    'data' => null,
                    'mensagem' => 'não foi possível carregar ficheiros, tente mais tarde!',
                    'codigo' => '500'
                ];
                http_response_code(200); // Bad Request
            }

        }else{
            $retorno = [
                'data' => null,
                'mensagem' => 'deve seleccionar no maximo um ficheiro',
                'codigo' => '400'
            ];
            http_response_code(400); // Bad Request

        }
    }
    return json_encode($retorno);
}

public function editarDocumento(){

        // Capturar dados enviados pelo método PUT
    $boundary = substr($_SERVER['CONTENT_TYPE'], strpos($_SERVER['CONTENT_TYPE'], "boundary=") + 9);
    $rawData = file_get_contents("php://input");
    $parts = explode("--" . $boundary, $rawData);
    $putData = [];

    foreach ($parts as $part) {
        if (empty($part) || $part == "--\r\n") {
            continue;
        }

        // Separar o cabeçalho e o conteúdo
        list($headers, $content) = explode("\r\n\r\n", $part, 2);
        preg_match('/name="([^"]+)"/', $headers, $matches);

        if (!empty($matches[1])) {
            $fieldName = $matches[1];
            $putData[$fieldName] = trim($content);
        }
    }

       $idusuario = filter_var($putData['idusuario'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
        
        $categoria=filter_var($putData['idcategoria'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

        $departamento=filter_var($putData['iddepartamento'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

        $titulo=filter_var($putData['titulo'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

        $tags=filter_var($putData['tags'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);


        $tipo= filter_var($putData['tipo'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

        $descricao= filter_var($putData['descricao'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

         // Valida os campos para garantir que não estão vazios
         if (empty($categoria) || empty($idusuario) || empty($categoria) || empty($departamento) || empty($tipo)) {

            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '400'
            ];

            http_response_code(400); // Bad Request

        } else {

            $retorno = DOCUMENTO:: edit($titulo,$tipo,$descricao,$categoria,$departamento,$tags,$idusuario);

            if($retorno){

                $retorno = [
                    'data' => null,
                    'mensagem' => 'Documento editado com sucesso!.',
                    'codigo' => '200'
                ];
    
                http_response_code(200); // Bad Request
            }else{

                $retorno = [
                    'data' => null,
                    'mensagem' => 'erro ao editar documento, tente novamente',
                    'codigo' => '500'
                ];
                http_response_code(200); // Bad Request
            }

        }
        return json_encode($retorno);
}

}