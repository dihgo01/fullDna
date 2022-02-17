<?php

if (!isset($_SESSION)) {
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/variaveis-aplicacao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/code/classes-web.class.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/code/functions.php';
$classesWeb = new ClassesWeb();

if (isset($_GET['action_type'])) {
    $acao = trim($_GET['action_type']);
} else {
    $acao = trim($_POST['action_type']);
}

if ($acao === 'cadastro_de_empresas') {
    if ($_GET['type'] === 'new') {
        /**
         * Insere um nova empresa do grupo
         */

        $hash_empresa = gerar_hash();

        
       // $extensao = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        //$formatos = array('pdf', 'png', 'jpg');
        $novoNome = uniqid() .$_FILES['logo']['name'];
        $path = 'uploads/empresas_logo/'.$novoNome;
        $path_imagem = $_SERVER['DOCUMENT_ROOT'] . $path;



        $campos = array(
            'hash',
            'cnpj',
            'razao_social',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'estado',
            'cidade',
            'path_logo',
            'cor_principal',
            'cor_secundaria',
            'status',
            'data_cadastro',
            'data_ultima_atualizacao',
            'data_exclusao',

        );

        $valores = array(
            $hash_empresa,
            $_POST['cnpj'],
            $_POST['razao_social'],
            $_POST['cep'],
            $_POST['logradouro'],
            $_POST['numero'],
            $_POST['complemento'],
            $_POST['bairro'],
            $_POST['estado'],
            $_POST['cidade'],
            $path,
            $_POST['color-primaria'],
            $_POST['color-secundaria'],
            'Ativo',
            date("Y-m-d H:i:s"),
            null,
            null,

        );
        foreach ($campos as $CAMPOS_INSERT) {
            $variaveis[] = '?';
        }

        
        if ( move_uploaded_file($_FILES['logo']['tmp_name'], $path_imagem)) {
           $message = 'logo cadastrada com sucesso';
        }else{
             echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'Selecione um arquivo válido. Tente novamente.',
                'type' => 'close'
            ));
        }

        /*
         * Insere os dados na tabela email clientes erp
         * 
         */

        try {
            echo validar_cnpj_do_banco($_POST['cnpj']);

            $insert = $classesWeb->query_insert(implode(', ', $campos), implode(', ', $variaveis), $valores, 'empresas_grupo');
            if ((int) $insert > 0) {

                if (isset($_POST['usuario_hash']) && array($_POST['usuario_hash']) && !empty($_POST['usuario_hash'])) {

                    $variavel = array();
                    $campo = array('hash', 'empresa_grupo_hash', 'usuario_hash', 'status', 'data_cadastro');
                    foreach ($campo as $CAMPOS_INSERT) {
                        $variavel[] = '?';
                    }
                    for ($i = 0; $i < (int) sizeof($_POST['usuario_hash']); $i++) {

                        $valor = array(gerar_hash(), $hash_empresa, $_POST['usuario_hash'][$i], 'Ativo', date("Y-m-d H:i:s"));
                        $classesWeb->query_insert(implode(', ', $campo), implode(', ', $variavel), $valor, 'empresas_grupo_contatos_empresariais');
                    }
                }


                echo json_encode(array(
                    'status' => 'OK',
                    'message' => 'Empresa cadastrada com sucesso.',
                    'type' => 'redirect',
                    'url' => WEBURL . 'empresas/empresas_grupo'
                ));
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'message' => 'Ocorreu um erro durante o processo. Tente novamente.',
                    'type' => 'close'
                ));
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    } else {

        /**
         * 
         * Update de empresas
         * 
         */

         
     if(isset($_FILES['logo']) && !empty($_FILES['logo']['name'])){  
        $novoNome = uniqid() .$_FILES['logo']['name'];
        $path = 'uploads/empresas_logo/'.$novoNome;
        $path_imagem = $_SERVER['DOCUMENT_ROOT'] . $path;
     }else{
         $path = $_POST['logo_path'];
        }
        
        $campos = array(
            'cnpj',
            'razao_social',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'estado',
            'cidade',
            'path_logo',
            'cor_principal',
            'cor_secundaria',
            'status',
            'data_ultima_atualizacao',
            'data_exclusao',

        );

        $valores = array(
            $_POST['cnpj'],
            $_POST['razao_social'],
            $_POST['cep'],
            $_POST['logradouro'],
            $_POST['numero'],
            $_POST['complemento'],
            $_POST['bairro'],
            $_POST['estado'],
            $_POST['cidade'],
            $path,
            $_POST['color-primaria'],
            $_POST['color-secundaria'],
            'Ativo',
            date("Y-m-d H:i:s"),
            null,
        );

        for ($i = 0; $i < (int) sizeof($campos); $i++) {
            $campos[$i] = $campos[$i] . ' = ?';
        }

        $mudar_status = $classesWeb->mudar_status_usuarios_contato($_GET['key']);


        $update = $classesWeb->query_update(implode(', ', $campos), $valores, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');
        if ((int) $update > 0) {

            if(isset($_FILES['logo']) && !empty($_FILES['logo']['name'])){ 
               
                move_uploaded_file($_FILES['logo']['tmp_name'], $path_imagem);
             }

            if (isset($mudar_status) && empty($mudar_status)) {
                foreach ($_POST['usuario_hash'] as $key => $value) {

                    $usuarios_database = $classesWeb->get_query_unica("SELECT usuario_hash  FROM empresas_grupo_contatos_empresariais WHERE usuario_hash = " . $value . "");
                    if ($value === $usuarios_database) {
                        $campos = array('status', '	data_ultima_atualizacao', 'data_exclusao');
                        $valores = array('Ativo', date("Y-m-d H:i:s"), null);

                        for ($i = 0; $i < (int) sizeof($campos); $i++) {
                            $campos[$i] = $campos[$i] . ' = ?';
                        }

                        $update = $classesWeb->query_update(implode(', ', $campos), $valores, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');
                    } else {

                        $variavel = array();
                        $campo = array('hash', 'empresa_grupo_hash', 'usuario_hash', 'status', 'data_cadastro');
                        foreach ($campo as $CAMPOS_INSERT) {
                            $variavel[] = '?';
                        }

                        $valor = array(gerar_hash(), $_GET['key'], $value, 'Ativo', date("Y-m-d H:i:s"));
                        $usuario = $classesWeb->query_insert(implode(', ', $campo), implode(', ', $variavel), $valor, 'empresas_grupo_contatos_empresariais');
                    }
                }
            }

            echo json_encode(array(
                'status' => 'OK',
                'message' => 'Empresa atualizada com sucesso.',
                'type' => 'redirect',
                'url' => WEBURL . 'empresas/empresas_grupo'
            ));
        } else {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'Ocorreu um erro durante o processo. Tente novamente.',
                'type' => 'close'
            ));
        }
    }
}

if ($acao === 'pegando_cidades') {

    $busca_cidade = $classesWeb->busca_cidade_por_estado($_POST['estado']);
    //var_dump($busca_cidade);
    if ((int) $busca_cidade > 0) {
        echo '<option value="">Selecione</option>';
        foreach ($busca_cidade as $key => $value) {

            echo '<option data-cidade="' . $value->nome . '" value="' . $value->hash . '">' . $value->nome . '</option>';
        }
    } else {
        echo json_encode(array(
            'status' => 'ERROR',
            'message' => 'Selecione um país.',
            'type' => 'close'
        ));
    }
}


if ($acao === 'busca_dados_usuario') {

    $busca_usuarios = $classesWeb->busca_unico_usuarios($_POST['usuarios']);

    echo '
    <div class="card-body d-flex" id="f_contato">

    <div class="card-body">
        <div class="">
            <input type="hidden" name="usuario_hash[]" placeholder="" value="' . $busca_usuarios->hash . '" autocomplete="off">
            <div class="col-md-12 mb-3">
                <ul>
                    <li> ' . $busca_usuarios->nome . '</li>
                    <li>'  . $busca_usuarios->email . '</li>
                    <li>'  . $busca_usuarios->udp_tel . '</li>
                    <li>' . $busca_usuarios->udp_cel . '</li>
                    <li>' . $busca_usuarios->udp_what . '</li>
                </ul>
            </div>
        </div>
    </div>
    <a href="#" id="contato_close" name="contato_close" class="btn-close p-4 "></a>
</div>';
}


if ($acao === 'cadastro_de_documentos') {


    $path = 'uploads/produtos';
    $extensao = pathinfo($_FILES['foto-produto']['name'], PATHINFO_EXTENSION);
    $formatos = array('pdf', 'png', 'jpg');
    $novoNome = uniqid() . ".$extensao";
    $path_imagem = $path . '/' . $novoNome;
}


