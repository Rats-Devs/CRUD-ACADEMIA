
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

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome_plano"]);
    $duracao = (int) $_POST["duracao_plano"];
    $valor = (float) $_POST["valor_plano"];
    $descricao = trim($_POST["descricao_plano"]);
    $status = $_POST["status_plano"];

    if (
        $nome == "" ||
        $duracao <= 0 ||
        $valor < 0 ||
        !in_array($status, ["Ativo", "Inativo"], true)
    ) {
        $erro = "Preencha os dados corretamente.";
    } else {

        $sql = "UPDATE PLANO SET
            NOME_PLANO = ?,
            DURACAO_PLANO = ?,
            VALOR_PLANO = ?,
            DESCRICAO_PLANO = ?,
            STATUS_PLANO = ?
            WHERE ID_PLANO = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "sidssi",
            $nome,
            $duracao,
            $valor,
            $descricao,
            $status,
            $id
        );

        $stmt->execute();

        header("Location: cadastro.php");
        exit;
    }

    // Mantém os valores enviados no formulário em caso de erro.
    $plano["NOME_PLANO"] = $nome;
    $plano["DURACAO_PLANO"] = $duracao;
    $plano["VALOR_PLANO"] = $valor;
    $plano["DESCRICAO_PLANO"] = $descricao;
    $plano["STATUS_PLANO"] = $status;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link href="../css/cadastro-css.css" rel="stylesheet">
    <title>Editar Plano</title>
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

<?php if ($erro != ""): ?>
    <p><?php echo htmlspecialchars($erro); ?></p>
<?php endif; ?>

<form method="POST">

<div class="card">
<div class="linha">

    <div class="coluna">
        <div class="form-group">
            <label>Nome do plano</label>
            <input type="text" name="nome_plano"
                value="<?php echo htmlspecialchars($plano["NOME_PLANO"]); ?>"
                required>
        </div>
    </div>

    <div class="coluna">
        <div class="form-group">
            <label>Duração em dias</label>
            <input type="number" name="duracao_plano"
                min="1"
                value="<?php echo htmlspecialchars($plano["DURACAO_PLANO"]); ?>"
                required>
        </div>
    </div>

    <div class="coluna">
        <div class="form-group">
            <label>Valor</label>
            <input type="number" name="valor_plano"
                min="0" step="0.01"
                value="<?php echo htmlspecialchars($plano["VALOR_PLANO"]); ?>"
                required>
        </div>
    </div>

    <div class="coluna">
        <div class="form-group">
            <label>Descrição</label>
            <input type="text" name="descricao_plano"
                value="<?php echo htmlspecialchars($plano["DESCRICAO_PLANO"] ?? ""); ?>">
        </div>
    </div>

    <div class="coluna">
        <div class="form-group">
            <label>Status</label>
            <select name="status_plano">
                <option value="Ativo"
                    <?php echo $plano["STATUS_PLANO"] == "Ativo" ? "selected" : ""; ?>>
                    Ativo
                </option>
                <option value="Inativo"
                    <?php echo $plano["STATUS_PLANO"] == "Inativo" ? "selected" : ""; ?>>
                    Inativo
                </option>
            </select>
        </div>
    </div>

    <div class="coluna">
        <input type="submit" value="Salvar alterações">
    </div>

</div>
</div>
</form>

</body>
</html>
