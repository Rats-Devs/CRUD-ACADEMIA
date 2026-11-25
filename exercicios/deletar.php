
<?php

require_once "../conexao.php";

// Permite exclusão somente por POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: cadastro.php");
    exit;
}

// Recebe e valida o ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: cadastro.php?erro=id_invalido");
    exit;
}

try {

    // Verifica se o exercício está vinculado a algum treino
    $sql = "SELECT COUNT(*)
            FROM TREINO_EXERCICIO
            WHERE ID_EXERCICIOS_FK = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->bind_result($quantidade);

    $stmt->fetch();

    $stmt->close();

    // Impede a exclusão se houver vínculos
    if ($quantidade > 0) {

        header("Location: cadastro.php?erro=exercicio_vinculado");
        exit;

    }

    // Exclui o exercício
    $sql = "DELETE FROM EXERCICIOS
            WHERE ID_EXERCICIOS = ?";

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
