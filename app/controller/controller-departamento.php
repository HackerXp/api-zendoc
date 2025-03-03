<?php

require_once'app/model/departamento.php';


class DepartamentoController
{
public function listarTodosDepartamento($pagina,$limite)
    {
    
        $retorno = DEPARTAMENTO::listar_todas($pagina,$limite);

        if(empty($retorno)){
            http_response_code(600);
        	$retorno=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
        	return json_encode($retorno);
        }

        
        http_response_code(200);

        return json_encode($retorno);
    }




public function listarDepartamentoId($id)
    {
    
        $retorno = DEPARTAMENTO::listar_id($id);

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




public function eliminarDepartamento($id)
    {
    
        $retorno = DEPARTAMENTO::eliminar($id);

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



public function cadastrarDepartamento(){
      
        // Recebe os dados do formulário
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
        $nome = filter_input(INPUT_POST, 'departamento', FILTER_SANITIZE_SPECIAL_CHARS);

        
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
            $retorno = DEPARTAMENTO::save($nome,$descricao);

            if ($retorno) {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Departamento cadastrada com sucesso.',
                    'codigo' => '200'
                ];
                http_response_code(200); // OK
            } else {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Erro ao cadastrar a departamento.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        }
        return json_encode($retorno);
    }




public function editarDepartamento(){


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
        $nome = filter_var($putData['departamento'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

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
            $retorno = DEPARTAMENTO::editar($descricao,$nome,$id);

            if ($retorno) {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'departamento editada com sucesso.',
                    'codigo' => '200'
                ];
                http_response_code(200); // OK
            } else {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Erro ao editar a departamento.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        }
        return json_encode($retorno);
    }


}