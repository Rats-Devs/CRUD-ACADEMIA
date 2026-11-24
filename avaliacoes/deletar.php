<?php

require_once "../conexao.php"; /* conexão com o banco de dados */

$id = $_GET["id"] ?? null;

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) { /* ! = NÃO; || = OU */
        /*
        se o id NÃO for um inteiro válido
        OU se o id for menor que 1,
        a validação vai falhar
        */
    echo "ID inválido.";
    exit;
}

$sql = "DELETE FROM AVALIACAO_FISICA
        WHERE ID_AVALIACAO = ?";/*apague da tabela AVALIACAO_FISICA o registro cujo ID_AVALIACAO seja igual ao valor que será colocado no ?*/


$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: cadastro.php");
exit;
?>