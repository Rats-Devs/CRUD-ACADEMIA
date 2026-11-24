<?php

require_once "../conexao.php"; /*conexão com o banco de dados*/

$id = $_GET["id"];

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {/*! = NÃO; || = OU */
        /*
        se o id NÃO for um inteiro válido
        OU se o id for menor que 1,
        a validação vai falhar
        */
    echo "ID inválido."; /*mensagem de erro*/
    exit;
}

$sql = "DELETE FROM PROFESSOR
        WHERE ID_PROFESSOR = ?"; /*apague da tabela ALUNOS o registro cujo ID_ALUNOS seja igual ao valor que será colocado no ?*/

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
header("Location: cadastro.php");
exit;
?>