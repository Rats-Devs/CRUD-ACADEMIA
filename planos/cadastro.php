
<?php
require_once "../conexao.php";

// Busca os planos cadastrados
$sql = "SELECT * FROM PLANO ORDER BY ID_PLANO DESC";
$resultado = $conexao->query($sql);

$erro = "";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome_plano"]);
    $duracao = (int) $_POST["duracao_plano"];
    $valor = (float) $_POST["valor_plano"];
    $descricao = trim($_POST["descricao_plano"]);

    if ($nome == "" || $duracao <= 0 || $valor < 0) {
        $erro = "Preencha os campos corretamente.";
    } else {

        $sql = "INSERT INTO PLANO
        (NOME_PLANO, DURACAO_PLANO, VALOR_PLANO, DESCRICAO_PLANO)
        VALUES (?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sids", $nome, $duracao, $valor, $descricao);
        $stmt->execute();

        header("Location: cadastro.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../css/cadastro-css.css" rel="stylesheet">
    <title>Planos</title>
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

<form method="POST">
    <div class="card">
        <div class="linha">

            <div class="coluna">
                <div class="form-group">
                    <label>Nome do plano</label>
                    <input type="text" name="nome_plano"
                           maxlength="100" required>
                </div>
            </div>

            <div class="coluna">
                <div class="form-group">
                    <label>Duração em dias</label>
                    <input type="number" name="duracao_plano"
                           min="1" required>
                </div>
            </div>

            <div class="coluna">
                <div class="form-group">
                    <label>Valor</label>
                    <input type="number" name="valor_plano"
                           min="0" step="0.01" required>
                </div>
            </div>

            <div class="coluna">
                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" name="descricao_plano"
                           maxlength="255">
                </div>
            </div>

            <div class="coluna">
                <input type="submit" value="Cadastrar plano">
            </div>

        </div>
    </div>
</form>

<?php if ($erro != ""): ?>
    <p><?php echo htmlspecialchars($erro); ?></p>
<?php endif; ?>

<div class="tabela-wrapper">
<table class="table-content">

<thead>
<tr class="table-header-row">
    <th class="table-th">ID</th>
    <th class="table-th">Nome</th>
    <th class="table-th">Duração</th>
    <th class="table-th">Valor</th>
    <th class="table-th">Descrição</th>
    <th class="table-th">Status</th>
    <th class="table-th">Ações</th>
</tr>
</thead>

<tbody>

<?php while ($plano = $resultado->fetch_assoc()): ?>

<tr class="table-data-row">

    <td class="table-td">
        <?php echo htmlspecialchars($plano["ID_PLANO"]); ?>
    </td>

    <td class="table-td">
        <?php echo htmlspecialchars($plano["NOME_PLANO"]); ?>
    </td>

    <td class="table-td">
        <?php echo htmlspecialchars($plano["DURACAO_PLANO"]); ?> dias
    </td>

    <td class="table-td">
        R$ <?php echo number_format($plano["VALOR_PLANO"], 2, ",", "."); ?>
    </td>

    <td class="table-td">
        <?php echo htmlspecialchars($plano["DESCRICAO_PLANO"] ?? ""); ?>
    </td>

    <td class="table-td">
        <?php echo htmlspecialchars($plano["STATUS_PLANO"]); ?>
    </td>

    <td class="table-td">
        <div class="actions-container">
        <a href="visualizar.php?id=<?php echo $plano["ID_PLANO"]; ?>" class="action-link">Visualizar</a>

        <a href="editar.php?id=<?php echo $plano["ID_PLANO"]; ?>" class="action-link">Editar</a>

    <form action="deletar.php" method="POST"
      onsubmit="return confirm('Deseja excluir este plano?')">

    <input type="hidden" name="id"
           value="<?php echo $plano['ID_PLANO']; ?>">

    <button type="submit" class="action-link delete-link">Excluir</button>
</div>
    </form>
    </td>

</tr>

<?php endwhile; ?>

</tbody>
</table>
</div>

</body>
</html>