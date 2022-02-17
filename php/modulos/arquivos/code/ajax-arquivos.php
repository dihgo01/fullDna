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


if (isset($_GET['action_type'])) {
    $acao = trim($_GET['action_type']);
} else {
    $acao = trim($_POST['action_type']);
}

$current_datetime = date('Y-m-d H:i:s');
if ($acao === 'dowload_de_arquivo') {

    $arquivoNome = basename($_GET['path']); // nome do arquivo que será enviado p/ download
    $arquivoLocal =$_SERVER['DOCUMENT_ROOT'].'/uploads/'. $_GET['path']; // caminho absoluto do arquivo
    // Verifica se o arquivo não existe
    if (!file_exists($arquivoLocal)) {
    // Exiba uma mensagem de erro caso ele não exista
     echo 'nao existe';
    exit;
    }
    // Aqui você pode aumentar o contador de downloads
    $novoNome = 'imagem_nova.jpg';
    // Configuramos os headers que serão enviados para o browser
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="'.$arquivoNome.'"');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($arquivoNome));
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');
    // Envia o arquivo para o cliente
    readfile($arquivoNome);
    /*

    $downloads = $_GET['qtd_download'] + 1;

    $campos_files = array(
        'qtd_download'
    );

    for ($i = 0; $i < (int) sizeof($campos_files); $i++) {
        $campos_files[$i] = $campos_files[$i] . ' = ?';
    }

    $valores_files = array(
        $downloads,
    );

    $update_qtd_downloads = $classesWeb->query_update(implode(', ', $campos_files), $valores_files, 'arquivos', 'hash = "' . $_GET['hash_file'] . '"');
*/
}
