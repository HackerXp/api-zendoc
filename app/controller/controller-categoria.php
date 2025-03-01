<?php

require_once'app/model/categoria.php';


class CategoriaController
{
public function listarTodasCategoria()
    {
    
        $retorno = CATEGORIA::listar_todas();

        if(empty($retorno)){
            http_response_code(600);
        	$retorno=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
        	return json_encode($retorno);
        }

        http_response_code(200);
        $dados=[
        'data'=>$retorno,
        'mensagem'=>'operação realizada com sucesso!',
        'codigo'=>'200'];

        return json_encode($dados);
    }




public function listarCategoriaId($id)
    {
    
        $retorno = CATEGORIA::listar_id($id);

        if(empty($retorno)){
            http_response_code(600);
        	$retorno=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
        	return json_encode($retorno);
        }

        http_response_code(200);
        $dados=[
        'data'=>$retorno,
        'mensagem'=>'operação realizada com sucesso!',
        'codigo'=>'200'];
        
        return json_encode($dados);
    }




public function eliminarCategoria($id)
    {
    
        $retorno = CATEGORIA::eliminar($id);

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



public function cadastrarCategoria(){
      
        // Recebe os dados do formulário
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS);

        
        // Valida os campos para garantir que não estão vazios
        if (empty($descricao) || empty($categoria)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '400'
            ];
            http_response_code(400); // Bad Request
        } else {
            // Chama o método save para inserir os dados no banco
            $retorno = CATEGORIA::save($categoria,$descricao);

            if ($retorno) {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'categoria cadastrada com sucesso.',
                    'codigo' => '200'
                ];
                http_response_code(200); // OK
            } else {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Erro ao cadastrar a categoria.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        }
        return json_encode($retorno);
    }




public function editarCategoria(){


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

  
        // Recebe os dados do formulário
        $descricao = filter_var($putData['descricao'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
        $categoria = filter_var($putData['categoria'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

        $id = filter_var($putData['id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
       
        // Valida os campos para garantir que não estão vazios
        if (empty($descricao) || empty($id)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '400'
            ];
            http_response_code(400); // Bad Request
        } else {
            // Chama o método save para inserir os dados no banco
            $retorno = CATEGORIA::edit($categoria,$descricao,$id);

            if ($retorno) {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Permissão editada com sucesso.',
                    'codigo' => '200'
                ];
                http_response_code(200); // OK
            } else {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Erro ao editar a permissão.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        }
        return json_encode($retorno);
    }


}