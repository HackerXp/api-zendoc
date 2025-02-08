<?php

require_once'app/model/usuario-permissao.php';

class UsuarioPermissaoController
{


public function listarPorUsuario($id)
    {
    
        $retorno = USUARIOPERMISSAO::listarPorUsuario($id);

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




public function eliminarPermissaoUsuario($id)
    {
    
        $retorno = USUARIOPERMISSAO::eliminar($id);

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



public function adicionarPermissao(){
      
        // Recebe os dados do formulário
        $idusuario = filter_input(INPUT_POST, 'idusuario', FILTER_SANITIZE_SPECIAL_CHARS);
        $idpermissao = filter_input(INPUT_POST, 'idpermissao', FILTER_SANITIZE_SPECIAL_CHARS);

        
        // Valida os campos para garantir que não estão vazios
        if (empty($idpermissao) || empty($idusuario)) {
            $retorno = [
                'data' => null,
                'mensagem' => 'Todos os campos são obrigatórios.',
                'codigo' => '400'
            ];
            http_response_code(400); // Bad Request
        } else {


            if(USUARIOPERMISSAO::verifica($idusuario,$idpermissao)){
                $retorno = [
                    'data' => null,
                    'mensagem' => 'esta permissão já foi addicionada a este usuário.',
                    'codigo' => '600'
                ];
                http_response_code(200); // OK
            }
            $retorno = USUARIOPERMISSAO::save($idusuario,$idpermissao);
          
            if ($retorno) {
                $retorno = [
                    'data' => null,
                    'mensagem' => 'permissão  addicionada com sucesso.',
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






}