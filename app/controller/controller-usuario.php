<?php

require_once'app/model/usuario.php';
require_once'app/model/usuario-permissao.php';

class UsuarioController
{
    public function listarTodos($pagina,$limite){
    
        $retorno = USUARIO::listar_todas($pagina,$limite);

        if(empty($retorno)){

        	$dados=['data'=>null,'mensagem'=>'nenhuma informãção encontrada','codigo'=>'600'];
        	return json_encode($dados);
        }

        

        return json_encode($retorno);
    }




    public function listarPorId($id) {
    
        $retorno = USUARIO::listar_id($id);

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


    public function listar_idDepartamento($id,$dept) {
    
        $retorno = USUARIO::listar_idDepartamento($id,$dept);

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




     public function cadastrar(){
      
        // Recebe os dados do formulário
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);

        $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $departamento = filter_input(INPUT_POST, 'iddepartamento', FILTER_SANITIZE_NUMBER_INT);

        $permissoes = filter_input(INPUT_POST, 'permissoes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if ($permissoes === null || !is_array($permissoes)) {
        return json_encode(['mensagem' => 'Permissões inválidas.', 'codigo' => 400]);
       }
        
        // Valida os campos para garantir que não estão vazios
        if (empty($nome) || empty($usuario) || empty($email) || empty($senha)|| empty($departamento)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '400'
            ];
            http_response_code(400); // Bad Request
        } else {


            if(USUARIO::buscar($email,$usuario)!=null){

                 $retorno = [
                'data' => USUARIO::buscar($email,$usuario),
                'mensagem' => 'Já existe um usuário com este email/nome de usuário '.$email.' '.$usuario,
                'codigo' => '600'
            ];
            http_response_code(600); // Bad Request

            }else{

               
            // Chama o método save para inserir os dados no banco
            $retorno = USUARIO::save($nome,$usuario,password_hash($senha, PASSWORD_DEFAULT),$email,$departamento);

            if ($retorno!=0) {
                foreach($permissoes as $key){

                    USUARIOPERMISSAO::save($retorno,$key);
                }

               
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Usuário cadastrado com sucesso.',
                    'codigo' => '200'
                ];
                http_response_code(200); // OK
            } else {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Erro ao cadastrar a Usuário.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        }}
        return json_encode($retorno);
    }


public function editar(){
      
        
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

        // Receber e filtrar os dados
        $nome = filter_var($putData['nome'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
        $usuario = filter_var($putData['usuario'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($putData['email'] ?? null, FILTER_SANITIZE_EMAIL);
        $id = filter_var($putData['id'] ?? null, FILTER_SANITIZE_NUMBER_INT);

        // Valida os campos para garantir que não estão vazios
        if (empty($nome) || empty($usuario) || empty($email) || empty($id)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '400'
            ];
            http_response_code(400); // Bad Request
        } else {
            // Chama o método save para inserir os dados no banco
            $retorno = USUARIO::edit($nome,$usuario,$email,$id);

            if ($retorno) {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Usuário editado com sucesso.',
                    'codigo' => '200'
                ];
                http_response_code(200); // OK
            } else {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Erro ao editar usuário.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        }
        return json_encode($retorno);
    }



    public function editar_senha(){


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

        // Receber e filtrar os dados
        $senha = filter_var($putData['senha'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
        $id = filter_input($putData['id'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
            
        // Valida os campos para garantir que não estão vazios
        if (empty($senha) || empty($id)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '600'
            ];
            http_response_code(600); // Bad Request
        } else {
            // Chama o método save para inserir os dados no banco
            $retorno = USUARIO::editPassword(password_hash($senha, PASSWORD_DEFAULT),$id);

            if ($retorno) {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'senha editada com sucesso.',
                    'codigo' => '200'
                ];
                http_response_code(200); // OK
            } else {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'Erro ao editar senha.',
                    'codigo' => '500'
                ];
                http_response_code(500); // Internal Server Error
            }
        }
        return json_encode($retorno);
    }


}