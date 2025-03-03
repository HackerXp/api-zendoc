<?php

require_once'app/model/permissao.php';


class PermissaoController
{
public function listarTodasPermisao($pagina,$limite)
    {
    
        $retorno = PERMISSAO::listar_todas($pagina,$limite);

        if(empty($retorno)){

        	$retorno=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
        	return json_encode($retorno);
        }

       

        return json_encode($retorno);
    }




public function listarPermissaoId($id)
    {
    
        $retorno = PERMISSAO::listar_id($id);

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




public function eliminarPermissao($id)
    {
    
        $retorno = PERMISSAO::eliminar($id);

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



public function cadastrarPermissao(){
      
        // Recebe os dados do formulário
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);

        
        // Valida os campos para garantir que não estão vazios
        if (empty($descricao)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '400'
            ];
            http_response_code(400); // Bad Request
        } else {
            // Chama o método save para inserir os dados no banco
            $retorno = PERMISSAO::save($descricao);

            if ($retorno) {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Permissão cadastrada com sucesso.',
                    'codigo' => '200'
                ];
                http_response_code(200); // OK
            } else {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Erro ao cadastrar a permissão.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        }
        return json_encode($retorno);
    }




public function editarPermissao(){


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
            $retorno = PERMISSAO::edit($descricao,$id);

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