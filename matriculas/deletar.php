<?php

require_once "../conexao.php";

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

    $sql = "SELECT COUNT(*) FROM PAGAMENTO WHERE ID_MATRICULA_FK = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($quantidade);
    $stmt->fetch();
    $stmt->close();

    if ($quantidade > 0) {
        header("Location: cadastro.php?erro=matricula_vinculada");
        exit;
    }

    $sql = "DELETE FROM MATRICULA WHERE ID_MATRICULA = ?";
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