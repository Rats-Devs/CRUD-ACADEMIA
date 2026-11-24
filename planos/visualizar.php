
<?php
require_once "../conexao.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID inválido.");
}

$sql = "SELECT * FROM PLANO WHERE ID_PLANO = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$plano = $resultado->fetch_assoc();

if (!$plano) {
    die("Plano não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link href="../css/biblioteca-css.css" rel="stylesheet">
    <title>Visualizar Plano</title>
</head>
<body>

<nav>
    <div class="nav_header">
        <div class="nav_logo">
            <a href="../index.php">
                <img src="../assets/gym.png" alt="Logo" class="gym-logo">
            </a>
        </div>
    </div>
    <ul class="nav_link">
        <li><a href="../index.php">Home</a></li>
        <li><a href="cadastro.php">Planos</a></li>
    </ul>
</nav>

<header>
<div class="header_content">

    <h2>Detalhes do Plano</h2>

    <div class="card-visualizar">

        <p><strong>ID:</strong>
            <?php echo htmlspecialchars($plano["ID_PLANO"]); ?>
        </p>

        <p><strong>Nome:</strong>
            <?php echo htmlspecialchars($plano["NOME_PLANO"]); ?>
        </p>

        <p><strong>Duração:</strong>
            <?php echo htmlspecialchars($plano["DURACAO_PLANO"]); ?> dias
        </p>

        <p><strong>Valor:</strong>
            R$ <?php echo number_format($plano["VALOR_PLANO"], 2, ",", "."); ?>
        </p>

        <p><strong>Descrição:</strong>
            <?php echo htmlspecialchars($plano["DESCRICAO_PLANO"] ?? ""); ?>
        </p>

        <p><strong>Status:</strong>
            <?php echo htmlspecialchars($plano["STATUS_PLANO"]); ?>
        </p>

    </div>

    <a href="cadastro.php">Voltar</a>

</div>
</header>

</body>
</html>
