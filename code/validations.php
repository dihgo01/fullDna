<?php
header('Access-Control-Allow-Origin: *');
include_once $_SERVER['DOCUMENT_ROOT'] . '/variaveis-aplicacao.php';
include_once 'classes-web.class.php';
include_once 'functions.php';
$classesWeb = new ClassesWeb();

$permissoes_ativas_de_modulos = false;

