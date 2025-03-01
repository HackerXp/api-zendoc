<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
require_once'app/conexao/conexao.php';
require_once'app/model/usuario-permissao.php';

use app\vendor\autoloader;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;



$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 2));
$dotenv->load();

function login($usuario, $senha) {



    if (empty($nome) || empty($usuario)) {
        $retorno = [
            'data' => null,
            'mensagem' => 'Todos os campos são obrigatórios.',
            'codigo' => '400'
        ];
        http_response_code(400); // Bad Request
    }

    $key = "dhaisd7sds8dsodshdisuds7d8sd8gsd8suidgs8dsnxsxss989dslkd";
    $conexao = ligar();
    $busca = $conexao->prepare("SELECT * FROM usuario WHERE usuario = ?");
    
    $busca->bindParam(1, $usuario);
    $busca->execute();
    
    $user = $busca->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(404);
        // Retorna uma mensagem específica se o usuário não for encontrado
        $retorno = [
            'data' => null,
            'mensagem' => 'Usuário não encontrado',
            'codigo' => '404'
        ];
    } else {
        if ($user['bloqueado']) {
            http_response_code(403);
            // Retorna uma mensagem se o usuário estiver bloqueado
            $retorno = [
                'data' => null,
                'mensagem' => 'Usuário bloqueado devido a múltiplas tentativas de login falhas',
                'codigo' => '403'
            ];
        } else {
            $criptada = $user['senha'];
            
            if (password_verify($senha, $criptada)) {

                
                $permissoes=USUARIOPERMISSAO::listarPorUsuario($user['idusuario']);

                $payload = [
                    "exp" => time() + 1000,
                    "iat" => time(),
                    "email" => $user['email'],
                    "permissoes" => empty($permissoes) ? null :$permissoes,
                    "nome" => $user['nome'],
                    "idusuario" => $user['idusuario']
                ];
                $jwt = JWT::encode($payload, $key, 'HS256');
                
                // Reseta tentativas falhas ao logar com sucesso
                $reset = $conexao->prepare("UPDATE usuario SET tentativas_falhas = 0 WHERE idusuario = ?");
                $reset->bindParam(1, $user['idusuario']);
                $reset->execute();
                http_response_code(200);
                
                $retorno = [
                    'data' => $jwt,
                    'mensagem' => 'Login realizado com sucesso',
                    'codigo' => '200'
                ];
            } else {
                // Incrementa tentativas falhas
                $increment = $conexao->prepare("UPDATE usuario SET tentativas_falhas = tentativas_falhas + 1 WHERE idusuario = ?");
                $increment->bindParam(1, $user['idusuario']);
                $increment->execute();
                
                // Verifica se o número de tentativas falhas atingiu 3
                if ($user['tentativas_falhas'] + 1 >= 3) {
                    $bloqueio = $conexao->prepare("UPDATE usuario SET bloqueado = TRUE WHERE idusuario = ?");
                    $bloqueio->bindParam(1, $user['idusuario']);
                    $bloqueio->execute();
                    http_response_code(200);
                    
                    $retorno = [
                        'data' => null,
                        'mensagem' => 'Usuário bloqueado após múltiplas tentativas de login falhas',
                        'codigo' => '403'
                    ];
                } else {
                    http_response_code(200);
                    $retorno = [
                        'data' => null,
                        'mensagem' => 'Senha incorreta',
                        'codigo' => '401'
                    ];
                }
            }
        }
    }
    
    return json_encode($retorno);
}




function validateToken($token) {
    $key = "dhaisd7sds8dsodshdisuds7d8sd8gsd8suidgs8dsnxsxss989dslkd";

    try {
        // Decodifica o token JWT
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        http_response_code(200);

        // Retorna os dados decodificados se o token for válido
        return [
            'data' => $decoded,
            'mensagem' => 'Token válido',
            'codigo' => '200'
        ];
    } catch (Exception $e) {
        http_response_code(401);
        // Retorna uma mensagem de erro se o token for inválido
        return [
            'data' => null,
            'mensagem' => 'Erro ao validar o token: ' . $e->getMessage(),
            'codigo' => '401'
        ];
    }
}


