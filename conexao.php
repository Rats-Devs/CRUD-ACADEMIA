<?php

$servidor = "localhost";
$usuario = "root";
$senha = "1234";
$banco = "ACADEMIA";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//etecPoa#2026
?>
