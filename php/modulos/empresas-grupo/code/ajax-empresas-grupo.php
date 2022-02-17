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

$current_datetime = date('Y-m-d H:i:s');
if ($acao === 'cadastro_de_empresas') {
    if ($_GET['type'] === 'new') {
        /*
         * FUNÇÃO PARA INSERIR UMA NOVA EMPRESA NO GRUPO
         */

        /*
         * GERO UM HASH PARA A EMPRESA
         * E CRIO UMA URL AMIGÁVEL PARA A RAZÃO SOCIAL DA EMPRESA
         */
        $hash_empresa = gerar_hash();
        $razao_social_amigavel = gerar_nome_amigavel($_POST['razao_social']);


        /*
         * CRIO OS DIRETÓRIOS E O ARQUIVO CSS DA EMPRESA
         */
        if (!file_exists(ROOT_UPLOAD . 'uploads/empresas_grupo')) {
            mkdir(ROOT_UPLOAD . 'uploads/empresas_grupo', 0777, true);
        }

        if (!file_exists(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel)) {
            mkdir(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel, 0777, true);
        }

        if (!file_exists(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel . '/logo')) {
            mkdir(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel . '/logo', 0777, true);
        }

        if (!file_exists(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial')) {
            mkdir(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial', 0777, true);
        }

        $logo_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['logo']['name']);
        $path = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/logo/' . $logo_novo_nome;
        $path_imagem = ROOT_UPLOAD . $path;


        $icone_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['icone_empresa']['name']);
        $path_upload = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/logo/' . $icone_novo_nome;
        $path_icone = ROOT_UPLOAD . $path_upload;

        $capa_proposta_comercial_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['capa_proposta_comercial']['name']);
        $path_upload_capa_proposta_comercial = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial/' . $capa_proposta_comercial_novo_nome;
        $path_capa_proposta_comercial = ROOT_UPLOAD . $path_upload_capa_proposta_comercial;

        $header_proposta_comercial_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['cabecalho_proposta_comercial']['name']);
        $path_upload_header_proposta_comercial = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial/' . $header_proposta_comercial_novo_nome;
        $path_header = ROOT_UPLOAD . $path_upload_header_proposta_comercial;

        $footer_proposta_comercial_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['rodape_proposta_comercial']['name']);
        $path_upload_footer_proposta_comercial = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial/' . $footer_proposta_comercial_novo_nome;
        $path_footer = ROOT_UPLOAD . $path_upload_footer_proposta_comercial;

        $background_proposta_comercial_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['imagem_de_fundo_proposta_comercial']['name']);
        $path_upload_background_proposta_comercial = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial/' . $background_proposta_comercial_novo_nome;
        $path_background = ROOT_UPLOAD . $path_upload_background_proposta_comercial;

        $css_empresa = 'assets/css/' . $razao_social_amigavel . '.css';
        if (!file_exists(ROOT_UPLOAD . $css_empresa)) {
            fopen(ROOT_UPLOAD . $css_empresa, 'w');
            $arquivo_base_css = file_get_contents(ROOT_UPLOAD . 'assets/css/style.css');
            $novo_css_empresa = str_replace('#f73164', $_POST['cor_secundaria'], str_replace('#be0d0d', $_POST['cor_principal'], $arquivo_base_css));
            file_put_contents(ROOT_UPLOAD . $css_empresa, $novo_css_empresa);
        }


        /*
         * CONFIGURO OS CAMPOS E VALORES QUE SERÃO UTILIZADOS NO INSERT
         */
        $campos = array(
            'hash',
            'cnpj',
            'razao_social',
            'nome_fantasia',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'estado',
            'cidade',
            'path_logo',
            'path_icone',
            'cor_principal',
            'cor_secundaria',
            'path_style',
            'path_capa_proposta',
            'path_header_proposta',
            'path_footer_proposta',
            'path_background_proposta',
            'status',
            'data_cadastro',
            'data_ultima_atualizacao',
            'data_exclusao'
        );

        $valores = array(
            $hash_empresa,
            TRIM($_POST['cnpj']),
            TRIM($_POST['razao_social']),
            TRIM($_POST['nome_fantasia']),
            TRIM($_POST['cep']),
            TRIM($_POST['logradouro']),
            TRIM($_POST['numero']),
            TRIM($_POST['complemento']),
            TRIM($_POST['bairro']),
            TRIM($_POST['estado']),
            TRIM($_POST['cidade']),
            $path,
            $path_upload,
            TRIM($_POST['cor_principal']),
            TRIM($_POST['cor_secundaria']),
            $css_empresa,
            $path_upload_capa_proposta_comercial,
            $path_upload_header_proposta_comercial,
            $path_upload_footer_proposta_comercial,
            $path_upload_background_proposta_comercial,
            'Ativo',
            $current_datetime,
            NULL,
            NULL,
        );


        foreach ($campos as $CAMPOS_INSERT) {
            $variaveis[] = '?';
        }

        /*
         * VERIFICO SE É UM CNPJ VÁLIDO E SE EXISTE CADASTRO COM O MESMO CNPJ NO BANCO DE DADOS
         */

        if (validar_cpf_cnpj(retorna_apenas_numeros(TRIM($_POST['cnpj'])), 'CNPJ') === false) {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'Informe um CNPJ válido.',
                'type' => 'close'
            ));
            exit;
        }
        $buscar_empresa_cnpj = $classesWeb->buscar_empresa_por_cnpj(TRIM($_POST['cnpj']), 'ATIVO');
        if (!empty($buscar_empresa_cnpj)) {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'CNPJ já cadastrado no sistema. Tente outro.',
                'type' => 'close'
            ));
            exit;
        }

        if(isset($_FILES['capa_proposta_comercial']) && $_FILES['capa_proposta_comercial'] !== '') {
            $fileName = $_FILES['capa_proposta_comercial']['tmp_name'];
            $sourceProperties = getimagesize($fileName);
            $resizeFileName = time();
            $fileExt = pathinfo($_FILES['capa_proposta_comercial']['name'], PATHINFO_EXTENSION);
            $uploadImageType = $sourceProperties[2];
            $sourceImageWidth = $sourceProperties[0];
            $sourceImageHeight = $sourceProperties[1];
            switch ($uploadImageType) {
                case IMAGETYPE_JPEG:
                    $resourceType = imagecreatefromjpeg($fileName);
                    $imageLayer = redimensionamentoDeCapaPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagejpeg($imageLayer, $path_capa_proposta_comercial);
                    break;

                case IMAGETYPE_GIF:
                    $resourceType = imagecreatefromgif($fileName);
                    $imageLayer = redimensionamentoDeCapaPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagegif($imageLayer, $path_capa_proposta_comercial);
                    break;

                case IMAGETYPE_PNG:
                    $resourceType = imagecreatefrompng($fileName);
                    $imageLayer = redimensionamentoDeCapaPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagepng($imageLayer, $path_capa_proposta_comercial);
                    break;

                default:
                    $imageProcess = 0;
                    break;
            }
            move_uploaded_file($file, $path_capa_proposta_comercial);
        }
        if(isset($_FILES['cabecalho_proposta_comercial']) && $_FILES['cabecalho_proposta_comercial'] !== '') {
            $fileName = $_FILES['cabecalho_proposta_comercial']['tmp_name'];
            $sourceProperties = getimagesize($fileName);
            $resizeFileName = time();
            $fileExt = pathinfo($_FILES['cabecalho_proposta_comercial']['name'], PATHINFO_EXTENSION);
            $uploadImageType = $sourceProperties[2];
            $sourceImageWidth = $sourceProperties[0];
            $sourceImageHeight = $sourceProperties[1];
            switch ($uploadImageType) {
                case IMAGETYPE_JPEG:
                    $resourceType = imagecreatefromjpeg($fileName);
                    $imageLayer = redimensionamentoDeHeaderPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagejpeg($imageLayer, $path_header);
                    break;

                case IMAGETYPE_GIF:
                    $resourceType = imagecreatefromgif($fileName);
                    $imageLayer = redimensionamentoDeHeaderPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagegif($imageLayer, $path_header);
                    break;

                case IMAGETYPE_PNG:
                    $resourceType = imagecreatefrompng($fileName);
                    $imageLayer = redimensionamentoDeHeaderPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagepng($imageLayer, $path_header);
                    break;

                default:
                    $imageProcess = 0;
                    break;
            }
            move_uploaded_file($file, $path_header);
        }
        if(isset($_FILES['rodape_proposta_comercial']) && $_FILES['rodape_proposta_comercial'] !== '') {
            $fileName = $_FILES['rodape_proposta_comercial']['tmp_name'];
            $sourceProperties = getimagesize($fileName);
            $resizeFileName = time();
            $fileExt = pathinfo($_FILES['rodape_proposta_comercial']['name'], PATHINFO_EXTENSION);
            $uploadImageType = $sourceProperties[2];
            $sourceImageWidth = $sourceProperties[0];
            $sourceImageHeight = $sourceProperties[1];
            switch ($uploadImageType) {
                case IMAGETYPE_JPEG:
                    $resourceType = imagecreatefromjpeg($fileName);
                    $imageLayer = redimensionamentoDeFooterPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagejpeg($imageLayer, $path_footer);
                    break;

                case IMAGETYPE_GIF:
                    $resourceType = imagecreatefromgif($fileName);
                    $imageLayer = redimensionamentoDeFooterPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagegif($imageLayer, $path_footer);
                    break;

                case IMAGETYPE_PNG:
                    $resourceType = imagecreatefrompng($fileName);
                    $imageLayer = redimensionamentoDeFooterPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagepng($imageLayer, $path_footer);
                    break;

                default:
                    $imageProcess = 0;
                    break;
            }
            
            move_uploaded_file($file, $path_footer);
        }
        if(isset($_FILES['imagem_de_fundo_proposta_comercial']) && $_FILES['imagem_de_fundo_proposta_comercial'] !== '') {
            $fileName = $_FILES['imagem_de_fundo_proposta_comercial']['tmp_name'];
            $sourceProperties = getimagesize($fileName);
            $resizeFileName = time();
            $fileExt = pathinfo($_FILES['imagem_de_fundo_proposta_comercial']['name'], PATHINFO_EXTENSION);
            $uploadImageType = $sourceProperties[2];
            $sourceImageWidth = $sourceProperties[0];
            $sourceImageHeight = $sourceProperties[1];
            switch ($uploadImageType) {
                case IMAGETYPE_JPEG:
                    $resourceType = imagecreatefromjpeg($fileName);
                    $imageLayer = redimensionamentoDeBackgroundPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagejpeg($imageLayer, $path_background);
                    break;

                case IMAGETYPE_GIF:
                    $resourceType = imagecreatefromgif($fileName);
                    $imageLayer = redimensionamentoDeBackgroundPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagegif($imageLayer, $path_background);
                    break;

                case IMAGETYPE_PNG:
                    $resourceType = imagecreatefrompng($fileName);
                    $imageLayer = redimensionamentoDeBackgroundPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagepng($imageLayer, $path_background);
                    break;

                default:
                    $imageProcess = 0;
                    break;
            }

            move_uploaded_file($file, $path_background);
        }

        if (isset($_FILES['icone_empresa']) && !empty($_FILES['icone_empresa']['name'])) {

            $fileName = $_FILES['icone_empresa']['tmp_name'];
            $sourceProperties = getimagesize($fileName);
            $resizeFileName = time();
            $fileExt = pathinfo($_FILES['icone_empresa']['name'], PATHINFO_EXTENSION);
            $uploadImageType = $sourceProperties[2];
            $sourceImageWidth = $sourceProperties[0];
            $sourceImageHeight = $sourceProperties[1];
            switch ($uploadImageType) {
                case IMAGETYPE_JPEG:
                    $resourceType = imagecreatefromjpeg($fileName);
                    $imageLayer = redimensionamentoDeIcone($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagejpeg($imageLayer, $path_icone);
                    break;

                case IMAGETYPE_GIF:
                    $resourceType = imagecreatefromgif($fileName);
                    $imageLayer = redimensionamentoDeIcone($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagegif($imageLayer, $path_icone);
                    break;

                case IMAGETYPE_PNG:
                    $resourceType = imagecreatefrompng($fileName);
                    $imageLayer = redimensionamentoDeIcone($resourceType, $sourceImageWidth, $sourceImageHeight);
                    $file = imagepng($imageLayer, $path_icone);
                    break;

                default:
                    $imageProcess = 0;
                    break;
            }
            move_uploaded_file($file, $path_icone);
        }

        if (isset($_FILES['logo']['tmp_name']) && TRIM($_FILES['logo']['name']) !== '') {
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $path_imagem)) {
                $insert = $classesWeb->query_insert(implode(', ', $campos), implode(', ', $variaveis), $valores, 'empresas_grupo');
                if ((int) $insert > 0) {
                    /*
                     * CÓDIGO NOVO
                     * SÓ FAZ OS INSERTS AUXILIARES APÓS A CONFIRMAÇÃO DO INSERT PRINCIPAL
                     */

                    /*
                     * FAZ A VERIFICAÇÃO E O INSERT DOS USUÁRIOS VINCULADOS À EMPRESA
                     */
                    if (isset($_POST['usuarios_vinculados']) && is_array($_POST['usuarios_vinculados']) && !empty($_POST['usuarios_vinculados'])) {
                        $variaveis = array();
                        $campos = array('hash', 'empresa_grupo_hash', 'usuario_hash', 'status', 'data_cadastro');
                        foreach ($campos as $CAMPOS_INSERT) {
                            $variaveis[] = '?';
                        }
                        foreach ($_POST['usuarios_vinculados'] as $USUARIOS_VINCULADOS) {
                            $valores = array(gerar_hash(), $hash_empresa, $USUARIOS_VINCULADOS, 'Ativo', date("Y-m-d H:i:s"));
                            $insert_usuario_empresa = $classesWeb->query_insert(implode(', ', $campos), implode(', ', $variaveis), $valores, 'empresas_grupo_contatos_empresariais');
                        }
                    }

                    if (isset($_POST['impostos_vinculados']) && is_array($_POST['impostos_vinculados']) && !empty($_POST['impostos_vinculados'])) {
                        $variaveis = array();
                        $campos = array(
                            'hash',
                            'empresas_grupo_hash',
                            'imposto_hash',
                            'aliquota',
                            'reajuste',
                            'status',
                            'data_cadastro',
                            'data_ultima_atualizacao',
                            'data_exclusao',
                        );

                        foreach ($campos as $CAMPOS_INSERT) {
                            $variaveis[] = '?';
                        }

                        for ($i = 0; $i < (int) sizeof($_POST['impostos_vinculados']); $i++) {
                            $valores = array(
                                gerar_hash(),
                                $hash_empresa,
                                TRIM($_POST['impostos_vinculados'][$i]),
                                retorna_percentual_decimal_banco_dados($_POST['aliquotas_vinculados'][$i]),
                                retorna_percentual_decimal_banco_dados($_POST['reajustes_vinculados'][$i]),
                                'Ativo',
                                $current_datetime,
                                NULL,
                                NULL,
                            );

                            $insert_imposto_empresa = $classesWeb->query_insert(implode(', ', $campos), implode(', ', $variaveis), $valores, 'empresas_grupo_impostos');
                        }
                    }

                    echo json_encode(array(
                        'status' => 'OK',
                        'message' => 'Empresa cadastrada com sucesso.',
                        'type' => 'redirect',
                        'url' => WEBURL . 'empresas/empresas-grupo'
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 'ERROR',
                        'message' => 'Ocorreu um erro durante o processo. Tente novamente.',
                        'type' => 'close'
                    ));
                }
            } else {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'message' => 'Não foi possível enviar a logo para o servidor. Tente novamente.',
                    'type' => 'close'
                ));
                exit;
            }
        } else {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'Selecione uma imagem.',
                'type' => 'close'
            ));
        }
    } else {
        /*
         * FUNÇÃO QUE ATUALIZA OS DADOS DA EMPRESA DO GRUPO
         */

        /*
         * CRIO UMA URL AMIGÁVEL PARA A RAZÃO SOCIAL DA EMPRESA
         */
        $razao_social_amigavel = gerar_nome_amigavel($_POST['razao_social']);

        /*
         * VERIFICO SE AS PASTAS DE UPLOADS ESTAO CRIADAS SE NAO ESTIVER CRIA
         */

        if (!file_exists(ROOT_UPLOAD . 'uploads/empresas_grupo')) {
            mkdir(ROOT_UPLOAD . 'uploads/empresas_grupo', 0777, true);
        }

        if (!file_exists(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel)) {
            mkdir(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel, 0777, true);
        }

        if (!file_exists(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel . '/logo')) {
            mkdir(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel . '/logo', 0777, true);
        }

        if (!file_exists(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial')) {
            mkdir(ROOT_UPLOAD . 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial', 0777, true);
        }

        /*
         * GERO UM NOME PARA ARQUIVO CSS E MUDO AS CORES DO ARQUIVO PRINCIPAL E CRIO EM CIMA DO ARQUIVO CSS JÁ EXISTENTE
         */

        $css_empresa = 'assets/css/' . $razao_social_amigavel . '.css';

        fopen(ROOT_UPLOAD . $css_empresa, 'w');
        $arquivo_base_css = file_get_contents(ROOT_UPLOAD . 'assets/css/style.css');
        $novo_css_empresa = str_replace('#f73164', $_POST['cor_secundaria'], str_replace('#be0d0d', $_POST['cor_principal'], $arquivo_base_css));
        file_put_contents(ROOT_UPLOAD . $css_empresa, $novo_css_empresa);

        $campos = array(
            'cnpj',
            'razao_social',
            'nome_fantasia',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'estado',
            'cidade',
            'cor_principal',
            'cor_secundaria',
            'path_style',
            'status',
            'data_ultima_atualizacao',
            'data_exclusao',
        );

        $valores = array(
            $_POST['cnpj'],
            $_POST['razao_social'],
            $_POST['nome_fantasia'],
            $_POST['cep'],
            $_POST['logradouro'],
            $_POST['numero'],
            $_POST['complemento'],
            $_POST['bairro'],
            $_POST['estado'],
            $_POST['cidade'],
            $_POST['cor_principal'],
            $_POST['cor_secundaria'],
            $css_empresa,
            'Ativo',
            $current_datetime,
            NULL
        );

        for ($i = 0; $i < (int) sizeof($campos); $i++) {
            $campos[$i] = $campos[$i] . ' = ?';
        }

        if (validar_cpf_cnpj(retorna_apenas_numeros(TRIM($_POST['cnpj'])), 'CNPJ') === false) {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'Informe um CNPJ válido.',
                'type' => 'close'
            ));
            exit;
        }

        /*
         * CÓDIGO NOVO
         * NÃO SE FAZ COMPARAÇÃO POR STRING
         */
        $buscar_empresa_cnpj = $classesWeb->busca_cnpj_diferente_da_empresa_cadastrada(TRIM($_POST['cnpj']), $_GET['key']);
        if (!empty($buscar_empresa_cnpj)) {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'CNPJ já cadastrado no sistema. Tente outro;',
                'type' => 'close'
            ));
            exit;
        }

        /*
         * ALTERO O STATUS DE TODOS OS REGISTROS DA TABELA USUARIOS CONTATOS QUE TENHA HASH DA EMPRESA EDITADA
         */
        $mudar_status_usuarios = $classesWeb->mudar_status_usuarios_contato($_GET['key']);
        $mudar_status_impostos = $classesWeb->mudar_status_impostos_empresas($_GET['key']);

        $update = $classesWeb->query_update(implode(', ', $campos), $valores, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');
        if ((int) $update > 0) {
            /*
             * CÓDIGO NOVO
             */
            if ($_GET['key'] === $_SESSION['EMP_SELECIONADA_DASH_STYLE']['HASH_EMPRESA']) {
                $_SESSION['EMP_SELECIONADA_DASH_STYLE']['NOME_EMPRESA'] = $_POST['nome_fantasia'];
            }

            /*
             * CÓDIGO NOVO
             */
            if (isset($_FILES['logo']) && !empty($_FILES['logo']['name'])) {
                $logo_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['logo']['name']);
                $path = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/logo/' . $logo_novo_nome;
                $path_imagem = ROOT_UPLOAD . $path;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $path_imagem)) {
                    $campos = array('path_logo');
                    for ($i = 0; $i < (int) sizeof($campos); $i++) {
                        $campos[$i] = $campos[$i] . ' = ?';
                    }
                    $valores = array($path);
                    $update_logo = $classesWeb->query_update(implode(', ', $campos), $valores, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');
                }
            }

            if (isset($_FILES['capa_proposta_comercial']) && !empty($_FILES['capa_proposta_comercial']['name'])) {               
                $capa_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['capa_proposta_comercial']['name']);
                $path = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial/' . $capa_novo_nome;
                $path_imagem = ROOT_UPLOAD . $path;

                $fileName = $_FILES['capa_proposta_comercial']['tmp_name'];
                $sourceProperties = getimagesize($fileName);
                $resizeFileName = time();
                $fileExt = pathinfo($_FILES['capa_proposta_comercial']['name'], PATHINFO_EXTENSION);
                $uploadImageType = $sourceProperties[2];
                $sourceImageWidth = $sourceProperties[0];
                $sourceImageHeight = $sourceProperties[1];
                switch ($uploadImageType) {
                    case IMAGETYPE_JPEG:
                        $resourceType = imagecreatefromjpeg($fileName);
                        $imageLayer = redimensionamentoDeCapaPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagejpeg($imageLayer, $path_imagem);
                        break;

                    case IMAGETYPE_GIF:
                        $resourceType = imagecreatefromgif($fileName);
                        $imageLayer = redimensionamentoDeCapaPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagegif($imageLayer, $path_imagem);
                        break;

                    case IMAGETYPE_PNG:
                        $resourceType = imagecreatefrompng($fileName);
                        $imageLayer = redimensionamentoDeCapaPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagepng($imageLayer, $path_imagem);
                        break;

                    default:
                        $imageProcess = 0;
                        break;
                }
                $campos_capa = array('path_capa_proposta');

                for ($i = 0; $i < (int) sizeof($campos_capa); $i++) {
                    $campos_capa[$i] = $campos_capa[$i] . ' = ?';
                }
                $valores_capa = array($path);

                $classesWeb->query_update(implode(', ', $campos_capa), $valores_capa, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');

                move_uploaded_file($file, $path_imagem);
            }

            if (isset($_FILES['cabecalho_proposta_comercial']) && !empty($_FILES['cabecalho_proposta_comercial']['name'])) {
                $cabecalho_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['cabecalho_proposta_comercial']['name']);
                $path = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial/' . $cabecalho_novo_nome;
                $path_imagem = ROOT_UPLOAD . $path;

                $fileName = $_FILES['cabecalho_proposta_comercial']['tmp_name'];
                $sourceProperties = getimagesize($fileName);
                $resizeFileName = time();
                $fileExt = pathinfo($_FILES['cabecalho_proposta_comercial']['name'], PATHINFO_EXTENSION);
                $uploadImageType = $sourceProperties[2];
                $sourceImageWidth = $sourceProperties[0];
                $sourceImageHeight = $sourceProperties[1];
                switch ($uploadImageType) {
                    case IMAGETYPE_JPEG:
                        $resourceType = imagecreatefromjpeg($fileName);
                        $imageLayer = redimensionamentoDeHeaderPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagejpeg($imageLayer, $path_imagem);
                        break;

                    case IMAGETYPE_GIF:
                        $resourceType = imagecreatefromgif($fileName);
                        $imageLayer = redimensionamentoDeHeaderPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagegif($imageLayer, $path_imagem);
                        break;

                    case IMAGETYPE_PNG:
                        $resourceType = imagecreatefrompng($fileName);
                        $imageLayer = redimensionamentoDeHeaderPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagepng($imageLayer, $path_imagem);
                        break;

                    default:
                        $imageProcess = 0;
                        break;
                }
                $campos_cabecalho = array('path_header_proposta');

                for ($i = 0; $i < (int) sizeof($campos_cabecalho); $i++) {
                    $campos_cabecalho[$i] = $campos_cabecalho[$i] . ' = ?';
                }
                $valores_cabecalho = array($path);

                $classesWeb->query_update(implode(', ', $campos_cabecalho), $valores_cabecalho, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');

                move_uploaded_file($file, $path_imagem);
            }

            if (isset($_FILES['rodape_proposta_comercial']) && !empty($_FILES['rodape_proposta_comercial']['name'])) {
                $rodape_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['rodape_proposta_comercial']['name']);
                $path = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial/' . $rodape_novo_nome;
                $path_imagem = ROOT_UPLOAD . $path;
                
                $fileName = $_FILES['rodape_proposta_comercial']['tmp_name'];
                $sourceProperties = getimagesize($fileName);
                $resizeFileName = time();
                $fileExt = pathinfo($_FILES['rodape_proposta_comercial']['name'], PATHINFO_EXTENSION);
                $uploadImageType = $sourceProperties[2];
                $sourceImageWidth = $sourceProperties[0];
                $sourceImageHeight = $sourceProperties[1];
                switch ($uploadImageType) {
                    case IMAGETYPE_JPEG:
                        $resourceType = imagecreatefromjpeg($fileName);
                        $imageLayer = redimensionamentoDeFooterPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagejpeg($imageLayer, $path_imagem);
                        break;

                    case IMAGETYPE_GIF:
                        $resourceType = imagecreatefromgif($fileName);
                        $imageLayer = redimensionamentoDeFooterPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagegif($imageLayer, $path_imagem);
                        break;

                    case IMAGETYPE_PNG:
                        $resourceType = imagecreatefrompng($fileName);
                        $imageLayer = redimensionamentoDeFooterPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagepng($imageLayer, $path_imagem);
                        break;

                    default:
                        $imageProcess = 0;
                        break;
                }
                $campos_rodape = array('path_footer_proposta');

                for ($i = 0; $i < (int) sizeof($campos_rodape); $i++) {
                    $campos_rodape[$i] = $campos_rodape[$i] . ' = ?';
                }
                $valores_rodape = array($path);

                $classesWeb->query_update(implode(', ', $campos_rodape), $valores_rodape, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');

                move_uploaded_file($file, $path_imagem);
            }

            if (isset($_FILES['imagem_de_fundo_proposta_comercial']) && !empty($_FILES['imagem_de_fundo_proposta_comercial']['name'])) {
                $background_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['imagem_de_fundo_proposta_comercial']['name']);
                $path = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/proposta-comercial/' . $background_novo_nome;
                $path_imagem = ROOT_UPLOAD . $path;
                
                $fileName = $_FILES['imagem_de_fundo_proposta_comercial']['tmp_name'];
                $sourceProperties = getimagesize($fileName);
                $resizeFileName = time();
                $fileExt = pathinfo($_FILES['imagem_de_fundo_proposta_comercial']['name'], PATHINFO_EXTENSION);
                $uploadImageType = $sourceProperties[2];
                $sourceImageWidth = $sourceProperties[0];
                $sourceImageHeight = $sourceProperties[1];
                switch ($uploadImageType) {
                    case IMAGETYPE_JPEG:
                        $resourceType = imagecreatefromjpeg($fileName);
                        $imageLayer = redimensionamentoDeBackgroundPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagejpeg($imageLayer, $path_imagem);
                        break;

                    case IMAGETYPE_GIF:
                        $resourceType = imagecreatefromgif($fileName);
                        $imageLayer = redimensionamentoDeBackgroundPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagegif($imageLayer, $path_imagem);
                        break;

                    case IMAGETYPE_PNG:
                        $resourceType = imagecreatefrompng($fileName);
                        $imageLayer = redimensionamentoDeBackgroundPropostaComercial($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagepng($imageLayer, $path_imagem);
                        break;

                    default:
                        $imageProcess = 0;
                        break;
                }
                $campos_plano_de_fundo = array('path_background_proposta');

                for ($i = 0; $i < (int) sizeof($campos_plano_de_fundo); $i++) {
                    $campos_plano_de_fundo[$i] = $campos_plano_de_fundo[$i] . ' = ?';
                }
                $valores_plano_de_fundo = array($path);

                $classesWeb->query_update(implode(', ', $campos_plano_de_fundo), $valores_plano_de_fundo, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');

                move_uploaded_file($file, $path_imagem);
            }

            /*
             * UPDATE DE ICONE DA EMPRESA
             */
            if (isset($_FILES['icone_empresa']) && !empty($_FILES['icone_empresa']['name'])) {

                $icone_novo_nome = uniqid() . gerar_nome_amigavel($_FILES['icone_empresa']['name']);
                $path_upload = 'uploads/empresas_grupo/' . $razao_social_amigavel . '/logo/' . $icone_novo_nome;
                $path_icone = ROOT_UPLOAD . $path_upload;

                $fileName = $_FILES['icone_empresa']['tmp_name'];
                $sourceProperties = getimagesize($fileName);
                $resizeFileName = time();
                $fileExt = pathinfo($_FILES['icone_empresa']['name'], PATHINFO_EXTENSION);
                $uploadImageType = $sourceProperties[2];
                $sourceImageWidth = $sourceProperties[0];
                $sourceImageHeight = $sourceProperties[1];
                switch ($uploadImageType) {
                    case IMAGETYPE_JPEG:
                        $resourceType = imagecreatefromjpeg($fileName);
                        $imageLayer = redimensionamentoDeIcone($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagejpeg($imageLayer, $path_icone);
                        break;

                    case IMAGETYPE_GIF:
                        $resourceType = imagecreatefromgif($fileName);
                        $imageLayer = redimensionamentoDeIcone($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagegif($imageLayer, $path_icone);
                        break;

                    case IMAGETYPE_PNG:
                        $resourceType = imagecreatefrompng($fileName);
                        $imageLayer = redimensionamentoDeIcone($resourceType, $sourceImageWidth, $sourceImageHeight);
                        $file = imagepng($imageLayer, $path_icone);
                        break;

                    default:
                        $imageProcess = 0;
                        break;
                }
                $campos_icone = array('path_icone');

                for ($i = 0; $i < (int) sizeof($campos_icone); $i++) {
                    $campos_icone[$i] = $campos_icone[$i] . ' = ?';
                }
                $valores_icone = array($path_upload);

                $classesWeb->query_update(implode(', ', $campos_icone), $valores_icone, 'empresas_grupo', 'hash = "' . $_GET['key'] . '"');

                move_uploaded_file($file, $path_icone);
            }


            /*
             * CÓDIGO NOVO
             */
            if (isset($_POST['usuarios_vinculados']) && is_array($_POST['usuarios_vinculados']) && !empty($_POST['usuarios_vinculados'])) {
                $variaveis = array();
                $campos = array('hash', 'empresa_grupo_hash', 'usuario_hash', 'status', 'data_cadastro');
                foreach ($campos as $CAMPOS_INSERT) {
                    $variaveis[] = '?';
                }
                foreach ($_POST['usuarios_vinculados'] as $USUARIOS_VINCULADOS) {
                    $valores = array(gerar_hash(), $_GET['key'], $USUARIOS_VINCULADOS, 'Ativo', date("Y-m-d H:i:s"));
                    $insert_usuario_empresa = $classesWeb->query_insert(implode(', ', $campos), implode(', ', $variaveis), $valores, 'empresas_grupo_contatos_empresariais');
                }
            }

            /*
             * CÓDIGO NOVO
             */
            if (isset($_POST['impostos_vinculados']) && is_array($_POST['impostos_vinculados']) && !empty($_POST['impostos_vinculados'])) {
                $variaveis = array();
                $campos = array(
                    'hash',
                    'empresas_grupo_hash',
                    'imposto_hash',
                    'aliquota',
                    'reajuste',
                    'status',
                    'data_cadastro',
                    'data_ultima_atualizacao',
                    'data_exclusao',
                );

                foreach ($campos as $CAMPOS_INSERT) {
                    $variaveis[] = '?';
                }

                for ($i = 0; $i < (int) sizeof($_POST['impostos_vinculados']); $i++) {
                    $valores = array(
                        gerar_hash(),
                        $_GET['key'],
                        TRIM($_POST['impostos_vinculados'][$i]),
                        retorna_percentual_decimal_banco_dados($_POST['aliquotas_vinculados'][$i]),
                        retorna_percentual_decimal_banco_dados($_POST['reajustes_vinculados'][$i]),
                        'Ativo',
                        $current_datetime,
                        NULL,
                        NULL,
                    );

                    $insert_imposto_empresa = $classesWeb->query_insert(implode(', ', $campos), implode(', ', $variaveis), $valores, 'empresas_grupo_impostos');
                }
            }

            echo json_encode(array(
                'status' => 'OK',
                'message' => 'Empresa atualizada com sucesso.',
                'type' => 'redirect',
                'url' => WEBURL . 'empresas/empresas-grupo'
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

if ($acao === 'busca_dados_usuario') {
    /*
     * BUSCO NO BANCO DE DADOS O USUARIO QUE ESTA SETADO NO MODAL E REPASSO AS INFORMAÇOES
     * CÓDIGO NOVO
     */

    $busca_usuarios = $classesWeb->busca_unico_usuarios($_POST['usuario']);
    if (!empty($busca_usuarios)) {
        echo '<div class="info-block">
                <div class="row">
                    <div class="col-md-11 col-10">
                        <h6><i class="icon-user"></i> ' . mb_strtoupper($busca_usuarios->nome, 'UTF-8') . '</h6>
                    </div>
                    <div class="col-md-1 col-2 text-right">
                        <button type="button" class="icone-excluir-lista btnExcluirContatoEmpresa"><i class="icon-close"></i></button>
                    </div>
                </div>
                <input type="hidden" name="usuarios_vinculados[]" value="' . $busca_usuarios->hash . '">
                <div class="star-ratings">
                    <ul class="search-info">
                        <li><i class="icofont icofont-ui-email"></i> ' . $busca_usuarios->email . '</li>
                        <li><i class="icofont icofont-ipod-touch"></i> ' . $busca_usuarios->USUARIO_CELULAR . '</li>
                        <li><i class="icofont icofont-brand-whatsapp"></i> ' . $busca_usuarios->USUARIO_WHATSAPP . '</li>
                        <li><i class="icofont icofont-ui-call"></i> ' . $busca_usuarios->USUARIO_TELEFONE . '</li>
                    </ul>
                </div>
            </div>';
    }
}


if ($acao === 'buscar_impostos_do_grupo') {
    /*
     * BUSCO NA TABELA IMPOSTO AQUIOTA E REAJUSTE
     * CÓDIGO NOVO
     */
    $busca_impostos_do_grupo = $classesWeb->busca_impostos_de_um_grupo($_POST['imposto']);
    $array_retorno = array();
    if (!empty($busca_impostos_do_grupo)) {
        foreach ($busca_impostos_do_grupo as $IMPOSTOS) {
            $array_retorno[] = array(
                'HASH' => $IMPOSTOS->hash,
                'IMPOSTO' => $IMPOSTOS->imposto,
                'ALIQUOTA' => number_format($IMPOSTOS->aliquota, 2, ',', '.') . '%',
                'REAJUSTE' => number_format($IMPOSTOS->reajuste, 2, ',', '.') . '%'
            );
        }
    }
    echo json_encode($array_retorno);
}

/*
 * CÓDIGO NOVO
 */
if ($acao === 'excluir_usuario_edicao') {
    $campos = array('status', 'data_ultima_atualizacao');
    for ($i = 0; $i < (int) sizeof($campos); $i++) {
        $campos[$i] = $campos[$i] . ' = ?';
    }

    $valores = array('Inativo', $current_datetime);

    $update = $classesWeb->query_update(implode(', ', $campos), $valores, 'empresas_grupo_contatos_empresariais', 'hash = "' . $_POST['usuario'] . '"');
    if ((int) $update > 0) {
        echo json_encode(array(
            'status' => 'OK'
        ));
    } else {
        echo json_encode(array(
            'status' => 'ERRO'
        ));
    }
}

/*
 * CÓDIGO NOVO
 */
if ($acao === 'excluir_imposto_edicao') {
    $campos = array('status', 'data_ultima_atualizacao');
    for ($i = 0; $i < (int) sizeof($campos); $i++) {
        $campos[$i] = $campos[$i] . ' = ?';
    }

    $valores = array('Inativo', $current_datetime);

    $update = $classesWeb->query_update(implode(', ', $campos), $valores, 'empresas_grupo_impostos', 'hash = "' . $_POST['imposto'] . '"');
    if ((int) $update > 0) {
        echo json_encode(array(
            'status' => 'OK'
        ));
    } else {
        echo json_encode(array(
            'status' => 'ERRO'
        ));
    }
}
