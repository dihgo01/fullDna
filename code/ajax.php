<?php

if (!isset($_SESSION)) {
    session_start();
}
header('Access-Control-Allow-Origin: *');
include_once '../variaveis-aplicacao.php';
include_once 'classes-web.class.php';
include_once 'functions.php';
$classesWeb = new ClassesWeb();

if (isset($_GET['action_type'])) {
    $acao = trim($_GET['action_type']);
} else {
    $acao = trim($_POST['action_type']);
}

if ($acao === 'login') {
    $buscar_usuario_sistema = $classesWeb->fazer_login(mb_strtolower($_POST['login'], 'UTF-8'), md5($_POST['password']));
    if (!empty($buscar_usuario_sistema)) {

            $_SESSION['USUARIO_SESSION_ID'] = array(
                'USUARIO_ID' => $buscar_usuario_sistema->hash,
                'ADMIN' => $buscar_usuario_sistema->admin,
                'NOME' => TRIM($buscar_usuario_sistema->nome),
            );
           
            echo json_encode(array(
                'status' => 'OK',
                'message' => 'Login successfully',
                'type' => 'login_redirect'
            ));
    } else {
        echo json_encode(array(
            'status' => 'ERROR',
            'message' => 'Invalid username or password.',
            'type' => 'close'
        ));
    }
}


if ($acao === 'excluir_item') {
    $campos = array('status', 'data_exclusao');
    for ($i = 0; $i < (int) sizeof($campos); $i++) {
        $campos[$i] = $campos[$i] . ' = ?';
    }
    $date = date('Y-m-d H:i:s');
    $valores = array('Inativo', $date);
    $update = $classesWeb->query_update(implode(', ', $campos), $valores, $_POST['table'], $_POST['parameter'] . ' = "' . $_POST['key'] . '"');
    if ((int) $update > 0) {
        echo json_encode(array(
            'status' => 'OK',
            'message' => 'Item successfully deleted',
            'type' => ''
        ));
    } else {
        echo json_encode(array(
            'status' => 'ERROR',
            'message' => 'An error occurred during the process. Try again.',
            'type' => 'close'
        ));
    }
}
