
<?php
require_once "../conexao.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Requisição inválida.");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID inválido.");
}

try {

    $sql = "DELETE FROM PLANO WHERE ID_PLANO = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: cadastro.php");
    exit;

} catch (mysqli_sql_exception $e) {

    http_response_code(409);

    echo "<h2>Não foi possível excluir o plano.</h2>";
    echo "<p>Esse plano pode estar vinculado a matrículas.</p>";
    echo '<a href="cadastro.php">Voltar</a>';
}
?>
