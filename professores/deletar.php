<?php

require_once "../conexao.php"; /* conexão com o banco de dados */

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: cadastro.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: cadastro.php?erro=id_invalido");
    exit;
}

try {

    $sql = "SELECT COUNT(*)
            FROM TREINO
            WHERE ID_PROFESSOR_FK = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($quantidade);
    $stmt->fetch();
    $stmt->close();

    if ($quantidade > 0) {
        header("Location: cadastro.php?erro=professor_vinculado");
        exit;
    }

    $sql = "DELETE FROM PROFESSOR
            WHERE ID_PROFESSOR = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: cadastro.php?sucesso=excluido");
    exit;

} catch (mysqli_sql_exception $e) {

    header("Location: cadastro.php?erro=exclusao");
    exit;

}

?>