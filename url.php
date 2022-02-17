<?php

if (!isset($_SESSION)) {
    session_start();
}
header('Access-Control-Allow-Origin: *');

$getURL = explode('/', $_SERVER['REQUEST_URI']);
include_once 'variaveis-aplicacao.php';

if (sizeof($getURL) > 1) {
    $pagina = $getURL[1];
    $_POST['p1'] = $pagina;
}


if (sizeof($getURL) > 2) {
    $acao = $getURL[2];
    $_POST['p2'] = $acao;
}

if (sizeof($getURL) > 3) {
    $id = $getURL[3];
    $_POST['p3'] = $id;
}

if (sizeof($getURL) > 4) {
    $edicao = $getURL[4];
    $_POST['p4'] = $edicao;
}

if (sizeof($getURL) > 5) {
    $acao_form = $getURL[5];
    $_POST['p5'] = $acao_form;
}

if (sizeof($getURL) > 6) {
    $hash_form = $getURL[6];
    $_POST['p6'] = $hash_form;
}

$permissao = array(
    '', 'dashboard', 'user', 'files',);

if (trim($pagina) !== '') {
    include_once 'code/validations.php';
}

switch ($pagina) {
    case '':
        $destiny = '/php/login.php';
        break;
    case 'dashboard':
        $destiny = '/php/dashboard.php';
        break;
    case 'user':
        $destiny = '/php/modulos/usuarios/administrador/usuarios.php';
        break;
    case 'files':
        $destiny = '/php/modulos/arquivos/arquivos.php';
        break;
    case 'sair':
        session_destroy();
        header('Location: /');
        break;
    default:
        $destiny = '/php/login.php';
        break;
}

if (in_array($pagina, $permissao)) {
    include_once $_SERVER['DOCUMENT_ROOT'] . $destiny;
} else {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/login.php';
}
