<?php

if (!isset($_SESSION)) {
    session_start();
}
header('Access-Control-Allow-Origin: *');

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/variaveis-aplicacao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/code/classes-web.class.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/code/functions.php';
$classesWeb = new ClassesWeb();

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use PHPMailer\PHPMailer\PHPMailer;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToCreateDirectory;

if (isset($_GET['action_type'])) {
    $acao = trim($_GET['action_type']);
} else {
    $acao = trim($_POST['action_type']);
}

// AWS Info
$bucketName = 'fulldna07';
$IAM_KEY = 'AKIAZXLXE2L77HNCMJX3';
$IAM_SECRET = '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';

$options =  array(
    'credentials' => array(
        'key' => $IAM_KEY,
        'secret' => $IAM_SECRET
    ),
    'version' => 'latest',
    'region'  => 'us-east-2'
);

$adapter = new League\Flysystem\Local\LocalFilesystemAdapter(
    $_SERVER['DOCUMENT_ROOT'] . '/uploads/',
);

$filesystem = new League\Flysystem\Filesystem($adapter);


$current_datetime = date('Y-m-d H:i:s');
if ($acao === 'gestao_usuarios') {
    if ($_GET['type'] === 'new') {
        /**
         * FUNÇÃO PARA INSERIR UM NOVO USUÁRIO
         */
        /**
         * GERO UM HASH PARA O USUARIO, PARA OS DADOS BANCARIOS E PARA OS DEPENDENTES
         */
        $usuario_hash = gerar_hash();

        $razao_social_amigavel = $usuario_hash;

        /**
         * DEFINO OS CAMPOS DAS TABELAS ONDE SERÃO REALIZADAS INSERÇÕES
         */

        if (!file_exists(ROOT_UPLOAD . 'uploads/' . $razao_social_amigavel)) {
            mkdir(ROOT_UPLOAD . 'uploads/' . $razao_social_amigavel, 0777, true);
        }

        $path = 'uploads/' . $razao_social_amigavel;


        $campos_usuarios = array(
            'hash',
            'nome',
            'username',
            'email',
            'senha',
            'admin',
            'path',
            'status',
            'data_cadastro'
        );

        /**
         * DEFINO AS VARIAVEIS DOS CAMPOS ATRAVÉS DE FOREACH'S
         */
        foreach ($campos_usuarios as $USUARIOS) {
            $variaveis_usuarios[] = '?';
        }

        /**
         * DEFINO OS VALORES A SEREM INSERIDOS EM CADA TABELA
         */
        $valores_usuarios = array(
            $usuario_hash,
            $_POST['nome'],
            $_POST['login'],
            $_POST['email'],
            md5($_POST['senha']),
            $_POST['admin'],
            $path,
            'Ativo',
            $current_datetime
        );

        /**
         * VERIFICO SE O E-MAIL INFORMADO JÁ ESTÁ CADASTRADO NO SISTEMA
         */
        $consulta_email = $classesWeb->consulta_email($_POST['email'], 'usuarios');
        if (!empty($consulta_email)) {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'Email already registered in the system. try another.',
                'type' => 'close'
            ));
        } else {

            $consulta_login = $classesWeb->consulta_login($_POST['login'], 'usuarios');
            if (!empty($consulta_login)) {
                echo json_encode(array(
                    'status' => 'ERROR',
                    'message' => 'Username is already registered in the system. try another.',
                    'type' => 'close'
                ));
                exit();
            } else {

                /**
                 * REALIZO A INSERÇÃO NA TABELA USUÁRIOS SE O CPF FOR VÁLIDO
                 */
                $insert_dados_usuarios = $classesWeb->query_insert(implode(', ', $campos_usuarios), implode(', ', $variaveis_usuarios), $valores_usuarios, 'usuarios');
                //VERIFICO SE O RETORNO DO INSERT FOI MAIOR QUE 
                if ((int) $insert_dados_usuarios > 0) {
                    $campos_files = array(
                        'hash',
                        'hash_usuario',
                        'titulo',
                        'descricao',
                        'path',
                        'data_prevista_exclusao',
                        'status',
                        'data_cadastro'
                    );


                    foreach ($campos_files as $FILES) {
                        $variaveis_files[] = '?';
                    }

                    $valores_files = array(
                        $file_hash,
                        $_GET['key'],
                        $_POST['titulo'],
                        $_POST['descricao'],
                        $keyName,
                        $data_formatada,
                        'Ativo',
                        $current_datetime
                    );



                    $insert_file = $classesWeb->query_insert(implode(', ', $campos_files), implode(', ', $variaveis_files), $valores_files, 'arquivos');
                    echo json_encode(array(
                        'status' => 'OK',
                        'message' => 'Successfully registered.',
                        'type' => 'redirect',
                        'url' => WEBURL . 'user'
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 'Erro',
                        'message' => 'There was an error in the process, try again.',
                        'type' => 'close'
                    ));
                }
            }
        }
    } elseif ($_GET['type'] === 'edit') {
        /**
         * FUNÇÃO PARA EDITAR UM NOVO USUÁRIO
         */

        $campos_usuarios = array(
            'nome',
            'username',
            'email',
            'senha',
            'admin',
            'status',
            'data_ultima_atualizacao'
        );

        /**
         * DEFINO OS VALORES A SEREM INSERIDOS EM CADA TABELA
         */
        $valores_usuarios = array(
            $_POST['nome'],
            $_POST['login'],
            $_POST['email'],
            md5($_POST['senha']),
            $_POST['admin'],
            'Ativo',
            $current_datetime
        );

        /**
         * VERIFICO SE O E-MAIL INFORMADO JÁ ESTÁ CADASTRADO NO SISTEMA
         */
        $consulta_email = $classesWeb->consulta_email($_POST['email'], 'usuarios');
        if (!empty($consulta_email) && $consulta_email->email != $_POST['email']) {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'Email already registered in the system. try another.',
                'type' => 'close'
            ));
            exit();
        }

        $consulta_login = $classesWeb->consulta_login($_POST['login'], 'usuarios');
        if (!empty($consulta_login) && $consulta_login->login != $_POST['login']) {
            echo json_encode(array(
                'status' => 'ERROR',
                'message' => 'Username is already registered in the system. try another.',
                'type' => 'close'
            ));
            exit();
        }

        /**
         * DEFINO AS VARIAVEIS DOS CAMPOS ATRAVÉS DE FOREACH'S
         */
        for ($i = 0; $i < (int) sizeof($campos_usuarios); $i++) {
            $campos_usuarios[$i] = $campos_usuarios[$i] . ' = ?';
        }

        /**
         * REALIZO A INSERÇÃO NA TABELA USUÁRIOS SE O CPF FOR VÁLIDO
         */
        $update_dados_usuarios = $classesWeb->query_update(implode(', ', $campos_usuarios), $valores_usuarios, 'usuarios', 'hash = "' . $_GET['key'] . '"');
        if ((int) $update_dados_usuarios > 0) {

            echo json_encode(array(
                'status' => 'OK',
                'message' => 'Data successfully updated.',
                'type' => 'redirect',
                'url' => WEBURL . 'dashboard'
            ));
        } else {
            echo json_encode(array(
                'status' => 'Erro',
                'message' => 'There was an error in the process, try again.',
                'type' => 'close'
            ));
        }
    }
}

if ($acao === 'envio_de_arquivos') {

    $file_hash = gerar_hash();

    // Connect to AWS
    try {
        // You may need to change the region. It will say in the URL when the bucket is open
        // and on creation. us-east-2 is Ohio, us-east-1 is North Virgina
        $s3 = new S3Client(
            array(
                'credentials' => array(
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET
                ),
                'version' => 'latest',
                'region'  => 'us-east-2'
            )
        );
    } catch (Exception $e) {
        // We use a die, so if this fails. It stops here. Typically this is a REST call so this would
        // return a json object.
        die("Error: " . $e->getMessage());
    }

    if (!isset($_FILES['file']['tmp_name']) && empty(TRIM($_FILES['file']['name']))) {
        echo json_encode(array(
            'status' => 'ERROR',
            'message' => 'Select a file.',
            'type' => 'close'
        ));
        exit;
    }

    // For this, I would generate a unqiue random string for the key name. But you can do whatever.
    $keyName = TRIM($_GET['key']) . '/' . basename($_FILES['file']['name']);
    $pathInS3 = 'https://s3.us-east-2.amazonaws.com/' . $bucketName . '/' . $keyName;

    // Add it to S3
    try {
        $file = $_FILES['file']['tmp_name'];

        $s3->putObject(
            array(
                'Bucket' => $bucketName,
                'Key' =>  $keyName,
                'SourceFile' => $file,
                'StorageClass' => 'REDUCED_REDUNDANCY'
            )
        );

        // WARNING: You are downloading a file to your local server then uploading
        // it to the S3 Bucket. You should delete it from this server.
        // $tempFilePath - This is the local file path.

    } catch (S3Exception $e) {
        die('Error:' . $e->getMessage());
    } catch (Exception $e) {
        die('Error:' . $e->getMessage());
    }


    $data_formatada = implode('-', array_reverse(explode('/', $_POST['data_exclusao'])));


    $campos_files = array(
        'hash',
        'hash_usuario',
        'titulo',
        'descricao',
        'path',
        'data_prevista_exclusao',
        'status',
        'data_cadastro'
    );


    foreach ($campos_files as $FILES) {
        $variaveis_files[] = '?';
    }

    $valores_files = array(
        $file_hash,
        $_GET['key'],
        $_POST['titulo'],
        $_POST['descricao'],
        $keyName,
        $data_formatada,
        'Ativo',
        $current_datetime
    );



    $insert_file = $classesWeb->query_insert(implode(', ', $campos_files), implode(', ', $variaveis_files), $valores_files, 'arquivos');
    //VERIFICO SE O RETORNO DO INSERT FOI MAIOR QUE 0
    if ((int) $insert_file > 0) {

        $mail = new PHPMailer(true);

        $html = '<table align="center" border="0" cellspacing="0" style="width:600px; font-family: Rubik, sans-serif; border: solid 2px #666666; border-radius: 15px; padding: 20px;">
                <tbody>
                <tr>
                <td style="direction:ltr;font-size:0px;padding:0px;text-align:center">
                <img src="' . WEBURL . 'assets/img/logos/logo-black.png" alt="logo" title="Logo Meta Pública" style=" width: 400px; margin:80px 0px 80px 0px;" >
                </td>
                </tr>
                <tr>
                <td align="center">
                <h1 style="text-align: center;">✅ <strong>Download available </strong></h1>
                <p> You have download files available on the FullDNA platform.</p> 
                </td>
                </tr>
                <tr>
                <td align="left">
                <div>
                
                </div>
                </td>
                </tr>
                </tbody>
                </table>';

        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.mailtrap.io';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'b3542a2d53b8ed';                       //SMTP username
        $mail->Password   = '4cc1a3b74ec156';                       //SMTP password                   
        $mail->Port       = 2525;

        //Recipients
        $mail->setFrom('atendimento@parrotsolucoes.com.br', 'Parrot');
        $mail->addAddress('diegocandi95@gmail.com', 'Diego');     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Download FullDNA ';
        $mail->Body    = $html;
        $mail->AltBody = 'Óla email esta sendo enviado para testes e verificaçao da segunda linha ';

        $mail->send();
        echo json_encode(array(
            'status' => 'OK',
            'message' => 'Successfully registered.',
            'type' => 'redirect',
            'url' => WEBURL . 'files'
        ));
    } else {
        echo json_encode(array(
            'status' => 'Erro',
            'message' => 'There was an error in the process, try again.',
            'type' => 'close'
        ));
    }
}


if ($acao === 'entrada_em_pasta') {


    $listing = $filesystem->listContents($_POST['path_folder'])->toArray();

    $_SESSION['path_anterior'] = $_POST['path_anterior'];

    $list_html = array();

    foreach ($listing as $key => $item) {

        if ($item instanceof \League\Flysystem\FileAttributes) {
            $folder_path_array = explode("/", $item->path());
            $folder_sem_ultimo_elem = array_pop($folder_path_array);

            $folder_path_certo = implode("/", $folder_path_array);
            $list_html[$key] = '<li class ="list-files-user">
            <div class="row">
                <input type="hidden" id="folder" value="' . $folder_path_certo . '" />
                <div class="col-md-10 col-9">
                <a class="actionFile" data-path="' . $item->path() . '" href="#" ><i class="material-icons">description</i> ' . basename($item->path()) . ' </a>
                </div>
                <div class="col-md-2 col-3">
                    <div class="row">
                        <div class="col-4">
                         <a href="' . WEBURL . 'uploads/' . $item->path() . '" download class="" data-path="' . $item->path() . '">
                             <i class="material-icons opacity-10">download</i>
                        </a>
                        </div>
                        <div class="col-4">
                           <a href="#" class="link-delete-files" data-path="' . $item->path() . '">
                            <i class="material-icons opacity-10">delete</i>
                           </a>
                        </div>
                    <div>
                
                </div>
            </div>       
         </li>';
        } elseif ($item instanceof \League\Flysystem\DirectoryAttributes) {
            $folder_path_array = explode("/", $item->path());
            $folder_sem_ultimo_elem = array_pop($folder_path_array);

            $folder_path_certo = implode("/", $folder_path_array);

            $list_html[$key] = '<li class ="list-files-user">
            <div class="row">
            <input type="hidden" id="folder" value="' . $folder_path_certo . '" />
            <div class="col-md-10 col-9">
            <i class="material-icons">folder</i>
            <a class="actionFolder" data-path-back="' . $_SESSION['path_anterior'] . '" data-path="' . $item->path() . '" href="#" >' . basename($item->path()) . '</a>
            </div>
            <div class="col-md-2 col-3">
                <div class="row">
                    <div class="col-4">
                    <a href="#" class="link-rename" data-path="' . $item->path() . '">
                        <i class="material-icons opacity-10">edit</i>
                    </a>   
                    </div>
                    <div class="col-4">
                    <a href="#" class="link-delete-folder" data-path="' . $item->path() . '">
                        <i class="material-icons opacity-10">delete</i>
                    </a>
                    </div>
                <div>
                </div>
            </div>       
        </li>';
        }
    }

    $retorno_html = implode(" ", $list_html);
    echo json_encode(array(
        'html_list' => $retorno_html
    ));
}

if ($acao === 'voltando_em_pasta') {

    $listing = $filesystem->listContents($_POST['path_folder'])->toArray();

    //$_SESSION['path_anterior'] = $_POST['path_anterior'];

    $list_html = array();

    foreach ($listing as $key => $item) {

        if ($item instanceof \League\Flysystem\FileAttributes) {
            $list_html[$key] = '<li class ="list-files-user">
            <div class="row">
                <div class="col-md-10 col-9">
                <a class="actionFile" data-path="' . $item->path() . '" href="#" ><i class="material-icons">description</i> ' . basename($item->path()) . ' </a>
                </div>
                <div class="col-md-2 col-3">
                    <div class="row">
                        <div class="col-4">
                         <a href="#" class="link-download" data-path="' . $item->path() . '">
                             <i class="material-icons opacity-10">download</i>
                        </a>
                        </div>
                        <div class="col-4">
                           <a href="#" class="link-delete-files" data-path="' . $item->path() . '">
                            <i class="material-icons opacity-10">delete</i>
                           </a>
                        </div>
                    <div>
                
                </div>
            </div>       
         </li>';
        } elseif ($item instanceof \League\Flysystem\DirectoryAttributes) {
            $list_html[$key] = '<li class ="list-files-user">
            <div class="row">
            <div class="col-md-10 col-9">
            <i class="material-icons">folder</i>
            <a class="actionFolder" data-path-back="' . $_SESSION['path_anterior'] . '" data-path="' . $item->path() . '" href="#" >' . basename($item->path()) . '</a>
            </div>
            <div class="col-md-2 col-3">
                <div class="row">
                    <div class="col-4">
                    <a href="#" class="link-rename" data-path="' . $item->path() . '">
                        <i class="material-icons opacity-10">edit</i>
                    </a>   
                    </div>
                    <div class="col-4">
                    <a href="#" class="link-delete-folder" data-path="' . $item->path() . '">
                        <i class="material-icons opacity-10">delete</i>
                    </a>
                    </div>
                <div>
                </div>
            </div>       
        </li>';
        }
    }

    $retorno_html = implode(" ", $list_html);
    echo json_encode(array(
        'html_list' => $retorno_html
    ));
}

if ($acao === 'rename_files') {

    $pasta = explode("/", $_POST['path']);

    $pasta_sem_file = array_pop($pasta);

    $pasta_string = implode("/", $pasta);

    $oldname = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $_POST['path'];

    $newName = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $pasta_string . '/' . $_POST['pasta_nome'];

    $path_new = rename($oldname, $newName);

    echo json_encode(array(
        'path_new' => $path_new,
    ));
}

if ($acao === 'delete_files') {

    try {
        $files_delete = $filesystem->delete($_POST['path']);
    } catch (FilesystemException | UnableToDeleteFile $exception) {
        echo $exception;
    }

    echo json_encode(array(
        'delete' => $files_delete,
    ));
}

if ($acao === 'delete_folder') {

    try {
        $files_delete = $filesystem->deleteDirectory($_POST['path']);
    } catch (FilesystemException | UnableToDeleteDirectory $exception) {
        echo $exception;
    }

    echo json_encode(array(
        'delete' => $files_delete,
    ));
}

if ($acao === 'create_folder') {

    if (isset($_POST['path'])) {
        mkdir(ROOT_UPLOAD . 'uploads/' . $_POST['path'] . '/' . $_POST['pasta_nome'], 0777, true);

        echo json_encode(array(
            'create' => "Ok",
        ));
    }
}

if ($acao === 'upload_files') {

    $file = $_POST['file']['name'];
    $path = 'uploads/' . $_POST['path'] . '/' . $file;
    $path_insert = ROOT_UPLOAD . $path;

    $file_hash = gerar_hash();

    $data_formatada = implode('-', array_reverse(explode('/', $_POST['data'])));


    $campos_files = array(
        'hash',
        'hash_usuario',
        'titulo',
        'descricao',
        'path',
        'data_prevista_exclusao',
        'status',
        'data_cadastro'
    );


    foreach ($campos_files as $FILES) {
        $variaveis_files[] = '?';
    }

    $valores_files = array(
        $file_hash,
        $_POST['hash_usuario'],
        $_POST['titulo'],
        $_POST['descricao'],
        $path,
        $data_formatada,
        'Ativo',
        $current_datetime
    );


    if (move_uploaded_file($_FILES['file']['tmp_name'], $path_insert)) {
        $insert_file = $classesWeb->query_insert(implode(', ', $campos_files), implode(', ', $variaveis_files), $valores_files, 'arquivos');
        //VERIFICO SE O RETORNO DO INSERT FOI MAIOR QUE 0
        if ((int) $insert_file > 0) {

            $mail = new PHPMailer(true);

            $html = '<table align="center" border="0" cellspacing="0" style="width:600px; font-family: Rubik, sans-serif; border: solid 2px #666666; border-radius: 15px; padding: 20px;">
                <tbody>
                <tr>
                <td style="direction:ltr;font-size:0px;padding:0px;text-align:center">
                <img src="' . WEBURL . 'assets/img/logos/logo-black.png" alt="logo" title="Logo Meta Pública" style=" width: 400px; margin:80px 0px 80px 0px;" >
                </td>
                </tr>
                <tr>
                <td align="center">
                <h1 style="text-align: center;">✅ <strong>Download available </strong></h1>
                <p> You have download files available on the FullDNA platform.</p> 
                </td>
                </tr>
                <tr>
                <td align="left">
                <div>
                
                </div>
                </td>
                </tr>
                </tbody>
                </table>';

            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.mailtrap.io';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'b3542a2d53b8ed';                       //SMTP username
            $mail->Password   = '4cc1a3b74ec156';                       //SMTP password                   
            $mail->Port       = 2525;

            //Recipients
            $mail->setFrom('atendimento@parrotsolucoes.com.br', 'Parrot');
            $mail->addAddress('diegocandi95@gmail.com', 'Diego');     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Download FullDNA ';
            $mail->Body    = $html;
            $mail->AltBody = 'Óla email esta sendo enviado para testes e verificaçao da segunda linha ';

            $mail->send();
            echo json_encode(array(
                'status' => 'OK',
                'message' => 'Successfully registered.',
                'type' => 'redirect',
                'url' => WEBURL . 'files'
            ));
        } else {
            echo json_encode(array(
                'status' => 'Erro',
                'message' => 'There was an error in the process, try again.',
                'type' => 'close'
            ));
        }
    } else {
        echo json_encode(array(
            'status' => 'ERROR',
            'message' => 'Could not send file to server. Try again.',
            'type' => 'close'
        ));
        exit;
    }

}
