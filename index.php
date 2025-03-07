<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Unifica todos os headers permitidos em uma única linha:
header("Access-Control-Allow-Headers: Content-Type, Authorization, x-xsrf-token, x_csrftoken, Cache-Control, x-Requested-With");

//medidas de segurança
header("Content-Security-Policy: default-src 'self'; script-src 'self';");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN"); // Prevenir clickjacking
header("X-Content-Type-Options: nosniff"); // Prevenir execução de arquivos incorretos
header("Referrer-Policy: no-referrer"); // Controle de informações enviadas no cabeçalho Referer

// Tratar preflight (método OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


require_once"app/controller/controller-usuario.php";
$usuariocontroller=new UsuarioController();

require_once"app/controller/controller-permissao.php";
$permissaocontroller=new PermissaoController();

require_once"app/controller/controller-departamento.php";
$departamentocontroller=new DepartamentoController();

require_once"app/controller/controller-usuario-permissao.php";
$usuarioPermissaocontroller=new UsuarioPermissaoController();

require_once"app/controller/controller-documento.php";
$documentocontroller=new DocumentoController();

require_once"app/controller/controller-categoria.php";
$categoriacontroller=new CategoriaController();

require_once"app/controller/controller-jwt.php";





// Definir rotas dinamicamente
$rotasUsuario = [
    //usuário
    'listar-todos-usuario' => ['method' => 'GET', 'handler' => 'listarTodos'],
    'listar-usuario-por-id' => ['method' => 'GET', 'handler' => 'listarPorId'],
    'cadastrar-usuario' => ['method' => 'POST', 'handler' => 'cadastrar'],
    'editar-usuario' => ['method' => 'PUT', 'handler' => 'editar'],
    'editar-senha' => ['method' => 'PUT', 'handler' => 'editar_senha'],

    //permissão
    'listar-permissao' => ['method' => 'GET', 'handler' => 'listarTodasPermisao'],
    'listar-permissao-por-id' => ['method' => 'GET', 'handler' => 'listarPermissaoId'],
    'cadastrar-permissao' => ['method' => 'POST', 'handler' => 'cadastrarPermissao'],
    'editar-permissao' => ['method' => 'PUT', 'handler' => 'editarPermissao'],
    'eliminar-permissao' => ['method' => 'DELETE', 'handler' => 'eliminarPermissao'],

    // departamento

    'listar-todos-departamento' => ['method' => 'GET', 'handler' => 'listarTodosDepartamento'],
    'listar-departamento-por-id' => ['method' => 'GET', 'handler' => 'listarDepartamentoId'],
    'cadastrar-departamento' => ['method' => 'POST', 'handler' => 'cadastrarDepartamento'],
    'editar-departamento' => ['method' => 'PUT', 'handler' => 'editarDepartamento'],
    'eliminar-departamento' => ['method' => 'DELETE', 'handler' => 'eliminarDepartamento'],
    // USUARIO-PERMISSAO

    
    'listar-permissao-por-usuario' => ['method' => 'GET', 'handler' => 'listarPorUsuario'],
    'adicionar-permissao' => ['method' => 'POST', 'handler' => 'adicionarPermissao'],
    'remover-permissao-usuario' => ['method' => 'DELETE', 'handler' => 'eliminarPermissaoUsuario'],


    // login
    'autenticacao' => ['method' => 'POST', 'handler' => 'autenticar'],


    //documentos
    'listar-todos-documentos' => ['method' => 'GET', 'handler' => 'listarTodosDocumentos'],
    'listar-documentos-por-id' => ['method' => 'GET', 'handler' => 'listarDocumentosId'],
    'filtro-avancado' => ['method' => 'GET', 'handler' => 'buscaAvancada'],
    'editar-documento' => ['method' => 'PUT', 'handler' => 'editarDocumento'],
    'eliminar-documento' => ['method' => 'DELETE', 'handler' => 'eliminarDocumento'],
    'salvar-documento' => ['method' => 'POST', 'handler' => 'salvarDocumento'],
    'listar-todos-documentos-por-categoria' => ['method' => 'GET', 'handler' => 'listarTodosDocumentosPorCategoria'],
    'listar-documentos-id-categoria' => ['method' => 'GET', 'handler' => 'listarDocumentosIdCategoria'],


    //categoria

    'listar-todas-categoria' => ['method' => 'GET', 'handler' => 'listarTodasCategoria'],
    'listar-categoria-por-id' => ['method' => 'GET', 'handler' => 'listarCategoriaId'],
    'criar-categoria' => ['method' => 'POST', 'handler' => 'cadastrarCategoria'],
    'eliminar-categoria' => ['method' => 'DELETE', 'handler' => 'eliminarCategoria'],
    'editar-categoria' => ['method' => 'PUT', 'handler' => 'editarCategoria'],

];


if(isset($_GET['rota']) && $_GET['rota']!=null){

  $rota = filter_input(INPUT_GET, 'rota', FILTER_SANITIZE_SPECIAL_CHARS);

    // Verifica o token antes de acessar as demais rotas
   /* if($rota !="autenticacao"){

     $headers = getallheaders();

    if (!isset($headers['Authorization'])) {

        $retorno = [
            'data' => null,
            'mensagem' => 'Token não fornecido, faça o login',
            'codigo' => '401'
        ];

        echo json_encode($retorno);

        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);

    $validacao = validateToken($token);

    if ($validacao['codigo'] != '200') {

        echo json_encode($validacao);
        exit;
    }}*/



//// usuário 
if (isset($rota) && array_key_exists($rota, $rotasUsuario)) {
    $rotaInfo = $rotasUsuario[$rota];
    
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== $rotaInfo['method']) {
        http_response_code(405); // Method Not Allowed
        echo json_encode([
            'data' => null,
            'mensagem' => "Método HTTP inválido. Use {$rotaInfo['method']}.",
            'codigo' => '405',
        ]);
        exit;
    }

    $handler = $rotaInfo['handler'];
    try {
       
        switch($handler){



            case'autenticar':

                $usuario=filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);

                $senha=filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

                echo login($usuario, $senha);
                break;


            // usuários

           case'listarUsuarioId':
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {

                echo json_encode([
                    'data' => null,
                    'mensagem' => 'ID não fornecido ou inválido. listar usuario',
                    'codigo' => '400',
                ]);
                http_response_code(400);
                exit;
            }
              echo $usuariocontroller->$handler($id);
            break;

            
            case'listarTodos':
                $pagina=isset($_GET['pagina']) ? filter_input(INPUT_GET,'pagina',FILTER_SANITIZE_NUMBER_INT):1;
                $limite= isset($_GET['limite'])? filter_input(INPUT_GET,'limite',FILTER_SANITIZE_NUMBER_INT):20;
                
                echo $usuariocontroller->$handler($pagina,$limite);
            break;


            case'cadastrar':
               echo $usuariocontroller->$handler();
            break;


            case'editar':

               parse_str(file_get_contents("php://input"), $putData);
               echo $usuariocontroller->$handler($putData);
            break;

            case'editar_senha':

              parse_str(file_get_contents("php://input"), $putData);
              echo $usuariocontroller->$handler($putData);
            break;


            
            //PERMISSÕES

            case 'listarTodasPermisao':
                $pagina=isset($_GET['pagina']) ? filter_input(INPUT_GET,'pagina',FILTER_SANITIZE_NUMBER_INT):1;
                $limite= isset($_GET['limite'])? filter_input(INPUT_GET,'limite',FILTER_SANITIZE_NUMBER_INT):20;
                echo $permissaocontroller->$handler($pagina,$limite);
                break;

            case 'listarPermissaoId':

            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {

                echo json_encode([
                    'data' => null,
                    'mensagem' => 'ID não fornecido ou inválido.',
                    'codigo' => '400',
                ]);
                http_response_code(400);
                exit;
            }
                echo $permissaocontroller->$handler($id);
                break;
            
            case 'eliminarPermissao':

            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {

                echo json_encode([
                    'data' => null,
                    'mensagem' => 'ID não fornecido ou inválido.',
                    'codigo' => '400',
                ]);
                http_response_code(400);
                exit;
            }
                echo $permissaocontroller->$handler($id);
                break;


            case'cadastrarPermissao':
                echo $permissaocontroller->$handler();
                break;

            case'editarPermissao':
                parse_str(file_get_contents("php://input"), $putData);
                echo $permissaocontroller->$handler($putData);
                break;




        //departamento

            case 'listarTodosDepartamento':
                $pagina=isset($_GET['pagina']) ? filter_input(INPUT_GET,'pagina',FILTER_SANITIZE_NUMBER_INT):1;
                $limite= isset($_GET['limite'])? filter_input(INPUT_GET,'limite',FILTER_SANITIZE_NUMBER_INT):20;
                echo $departamentocontroller->$handler($pagina,$limite);
                break;

            case 'listarDepartamentoId':

            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {

                echo json_encode([
                    'data' => null,
                    'mensagem' => 'ID não fornecido ou inválido.',
                    'codigo' => '400',
                ]);
                http_response_code(400);
                exit;
            }
                echo $departamentocontroller->$handler($id);
                break;

            case 'eliminarDepartamento':

            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {

                echo json_encode([
                    'data' => null,
                    'mensagem' => 'ID não fornecido ou inválido.',
                    'codigo' => '400',
                ]);
                http_response_code(400);
                exit;
            }
                echo $departamentocontroller->$handler($id);
                break;


            case'cadastrarDepartamento':
                echo $departamentocontroller->$handler();
                break;

            case'editarDepartamento':
                parse_str(file_get_contents("php://input"), $putData);
                echo $departamentocontroller->$handler($putData);
                break;


                  //// usuario-permissao



            case 'listarPorUsuario':

                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                if (!$id) {
    
                    echo json_encode([
                        'data' => null,
                        'mensagem' => 'ID não fornecido ou inválido.',
                        'codigo' => '400',
                    ]);
                    http_response_code(400);
                    exit;
                }
                echo $usuarioPermissaocontroller->$handler($id);
                break;

           
            case 'eliminarPermissaoUsuario':

            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {

                echo json_encode([
                    'data' => null,
                    'mensagem' => 'ID não fornecido ou inválido.',
                    'codigo' => '400',
                ]);
                http_response_code(400);
                exit;
            }
                echo $usuarioPermissaocontroller->$handler($id);
                break;


            case'adicionarPermissao':
                echo $usuarioPermissaocontroller->$handler();
                break;




 //tags

            case 'listar-todas-tag':

                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                if (!$id) {
    
                    echo json_encode([
                        'data' => null,
                        'mensagem' => 'ID não fornecido ou inválido.',
                        'codigo' => '400',
                    ]);
                    http_response_code(400);
                    exit;
                }
                echo $usuarioPermissaocontroller->$handler($id);
                break;

           
            case 'eliminarPermissaoUsuario':

            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if (!$id) {

                echo json_encode([
                    'data' => null,
                    'mensagem' => 'ID não fornecido ou inválido.',
                    'codigo' => '400',
                ]);
                http_response_code(400);
                exit;
            }
                echo $usuarioPermissaocontroller->$handler($id);
                break;


            case'adicionarPermissao':
                echo $usuarioPermissaocontroller->$handler();
                break;

       
               
            // documentos

            
            case 'listarTodosDocumentos':
                $pagina=isset($_GET['pagina']) ? filter_input(INPUT_GET,'pagina',FILTER_SANITIZE_NUMBER_INT):1;
                $limite= isset($_GET['limite'])? filter_input(INPUT_GET,'limite',FILTER_SANITIZE_NUMBER_INT):20;
            echo $documentocontroller->$handler($pagina,$limite);
            break;

            case 'listarDocumentosId':

                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                if (!$id) {
    
                    echo json_encode([
                        'data' => null,
                        'mensagem' => 'ID não fornecido ou inválido.',
                        'codigo' => '400',
                    ]);
                    http_response_code(400);
                    exit;
                }
                
                echo $documentocontroller->$handler($id);
                break;
                
            case 'listarDocumentosIdCategoria':

                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                if (!$id) {
    
                    echo json_encode([
                        'data' => null,
                        'mensagem' => 'ID não fornecido ou inválido.',
                        'codigo' => '400',
                    ]);
                    http_response_code(400);
                    exit;
                }
                $pagina=isset($_GET['pagina']) ? filter_input(INPUT_GET,'pagina',FILTER_SANITIZE_NUMBER_INT):1;
                $limite= isset($_GET['limite'])? filter_input(INPUT_GET,'limite',FILTER_SANITIZE_NUMBER_INT):20;
                echo $documentocontroller->$handler($id,$pagina,$limite);
                break;


            case 'eliminarDocumento':

                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                if (!$id) {
    
                    echo json_encode([
                        'data' => null,
                        'mensagem' => 'ID não fornecido ou inválido.',
                        'codigo' => '400',
                    ]);
                    http_response_code(400);
                    exit;
                }
                
                echo $documentocontroller->$handler($id);
                break;


            case 'buscaAvancada':
            echo $documentocontroller->$handler();
            break;

            case 'editarDocumento':
            parse_str(file_get_contents("php://input"), $putData);
            echo $documentocontroller->$handler($putData);
            break;
          
            case 'salvarDocumento':
            echo $documentocontroller->$handler();
            break;

            case 'listarTodosDocumentosPorCategoria':
                echo $documentocontroller->$handler();
                break;
          


            ///categorias

             
            case 'listarTodasCategoria':
                $pagina=isset($_GET['pagina']) ? filter_input(INPUT_GET,'pagina',FILTER_SANITIZE_NUMBER_INT):1;
                $limite= isset($_GET['limite'])? filter_input(INPUT_GET,'limite',FILTER_SANITIZE_NUMBER_INT):20;
                echo $categoriacontroller->$handler($pagina,$limite);
                break;
    
                case 'listarCategoriaId':
    
                    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                    if (!$id) {
        
                        echo json_encode([
                            'data' => null,
                            'mensagem' => 'ID não fornecido ou inválido.',
                            'codigo' => '400',
                        ]);
                        http_response_code(400);
                        exit;
                    }
                    
                    echo $categoriacontroller->$handler($id);
                    break;
    
    
                case 'eliminarCategoria':

                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                if (!$id) {
    
                    echo json_encode([
                        'data' => null,
                        'mensagem' => 'ID não fornecido ou inválido.',
                        'codigo' => '400',
                    ]);
                    http_response_code(400);
                    exit;
                }
                
                echo $categoriacontroller->$handler($id);
                break;
    
    
               
    
                case 'editarCategoria':
                parse_str(file_get_contents("php://input"), $putData);
                echo $categoriacontroller->$handler($putData);
                break;
              
                case 'cadastrarCategoria':
                echo $categoriacontroller->$handler();
                break;





            default:
            echo json_encode([
                'data' => null,
                'mensagem' => "Endereço não encontrado ".$handler,
                'codigo' => '404',
            ]);
            http_response_code(404);
        }





    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'data' => null,
            'mensagem' => 'Erro ao processar a requisição.',
            'detalhes' => $e->getMessage(),
            'codigo' => '500',
        ]);
    }

} else {
    http_response_code(404); // Not Found
    echo json_encode([
        'data' => null,
        'mensagem' => 'Rota não encontrada.',
        'codigo' => '404',
    ]);

}
/*
if($rota=="listarUsuario"){ //para listar todas uidades hospitalares

    echo $usuariocontroller->listarTodos();

}

if($rota=="listarUsuarioId"){

    $id=filter_input(INPUT_GET,'id', FILTER_SANITIZE_NUMBER_INT);

    echo $usuariocontroller->listarPorId($id);

}


if($rota=="cadastrar-usuario"){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    echo $usuariocontroller->cadastrar();

    } else {
        // Caso o método não seja POST
        $retorno = [
            'data' => null,
            'mensagem' => 'Método HTTP inválido. Use POST.',
            'codigo' => '405'
        ];
        http_response_code(405); // Method Not Allowed
        return json_encode($retorno);
    }
}


if($rota=="editar-usuario"){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    echo $usuariocontroller->editar();

    } else {
        // Caso o método não seja POST
        $retorno = [
            'data' => null,
            'mensagem' => 'Método HTTP inválido. Use POST.',
            'codigo' => '405'
        ];

        http_response_code(405); // Method Not Allowed
        return json_encode($retorno);
    }
}


if($rota=="editar-senha"){

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    echo $usuariocontroller->editar_senha();

    } else {
        // Caso o método não seja POST
        $retorno = [
            'data' => null,
            'mensagem' => 'Método HTTP inválido. Use POST.',
            'codigo' => '405'
        ];

        http_response_code(405); // Method Not Allowed
        return json_encode($retorno);
    }
}
*/

}