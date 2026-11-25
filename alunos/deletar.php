
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

    $sql = "SELECT
                (SELECT COUNT(*) FROM MATRICULA WHERE ID_ALUNO_FK = ?) +
                (SELECT COUNT(*) FROM TREINO WHERE ID_ALUNO_FK = ?) +
                (SELECT COUNT(*) FROM AVALIACAO_FISICA WHERE ID_ALUNO_FK = ?)
            AS quantidade";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $id, $id, $id);
    $stmt->execute();
    $stmt->bind_result($quantidade);
    $stmt->fetch();
    $stmt->close();

    if ($quantidade > 0) {
        header("Location: cadastro.php?erro=aluno_vinculado");
        exit;
    }

    $sql = "DELETE FROM ALUNO
            WHERE ID_ALUNO = ?";

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
