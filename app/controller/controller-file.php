<?php

require_once 'app/model/files.php';

class FilesController
{
    public function uploadFiles()
    {
        if (!isset($_FILES['files']) || empty($_FILES['files'])) {
            http_response_code(400);
            return json_encode([
                'data' => null,
                'mensagem' => 'Nenhum arquivo enviado.',
                'codigo' => '400'
            ]);
        }

        // Pegando os IDs do usuário e do documento
        $idusuario = filter_input(INPUT_POST, 'idusuario', FILTER_SANITIZE_NUMBER_INT);
        $iddocumento = filter_input(INPUT_POST, 'iddocumento', FILTER_SANITIZE_NUMBER_INT);

        if (empty($idusuario) || empty($iddocumento)) {
            http_response_code(400);
            return json_encode([
                'data' => null,
                'mensagem' => 'Os campos idusuario e iddocumento são obrigatórios.',
                'codigo' => '400'
            ]);
        }

        $retorno = FILES::save($_FILES, $idusuario, $iddocumento);

        if ($retorno) {
            http_response_code(200);
            return json_encode([
                'data' => null,
                'mensagem' => 'Arquivos enviados com sucesso.',
                'codigo' => '200'
            ]);
        } else {
            http_response_code(500);
            return json_encode([
                'data' => null,
                'mensagem' => 'Erro ao salvar os arquivos.',
                'codigo' => '500'
            ]);
        }
    }

    public function listarArquivos($iddocumento)
    {
        $iddocumento = filter_var($iddocumento, FILTER_SANITIZE_NUMBER_INT);

        if (empty($iddocumento)) {
            http_response_code(400);
            return json_encode([
                'data' => null,
                'mensagem' => 'O campo iddocumento é obrigatório.',
                'codigo' => '400'
            ]);
        }

        $retorno = FILES::listar_todas($iddocumento);

        if (empty($retorno)) {
            http_response_code(404);
            return json_encode([
                'data' => null,
                'mensagem' => 'Nenhum arquivo encontrado.',
                'codigo' => '404'
            ]);
        }

        http_response_code(200);
        return json_encode([
            'data' => $retorno,
            'mensagem' => 'Operação realizada com sucesso.',
            'codigo' => '200'
        ]);
    }

    public function deletarArquivo($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        if (empty($id)) {
            http_response_code(400);
            return json_encode([
                'data' => null,
                'mensagem' => 'O campo id é obrigatório.',
                'codigo' => '400'
            ]);
        }

        $retorno = FILES::eliminar($id);

        if ($retorno) {
            http_response_code(200);
            return json_encode([
                'data' => null,
                'mensagem' => 'Arquivo deletado com sucesso.',
                'codigo' => '200'
            ]);
        } else {
            http_response_code(500);
            return json_encode([
                'data' => null,
                'mensagem' => 'Erro ao deletar o arquivo.',
                'codigo' => '500'
            ]);
        }
    }
}
