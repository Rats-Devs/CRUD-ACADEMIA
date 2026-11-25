
<?php

require_once "../conexao.php";

$erro = "";

// Identifica o exercício
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
} else {
    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
}

if (!$id || $id <= 0) {
    header("Location: cadastro.php");
    exit;
}

// Busca os dados atuais do exercício
$sql = "SELECT
            ID_EXERCICIOS,
            NOME_EXERCICIO,
            GRUPO_MUSCULAR,
            DESCRICAO_EXERCICIO,
            EQUIPAMENTOS
        FROM EXERCICIOS
        WHERE ID_EXERCICIOS = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt->bind_result(
    $id_exercicio,
    $nome_atual,
    $grupo_atual,
    $descricao_atual,
    $equipamentos_atual
);

if (!$stmt->fetch()) {
    $stmt->close();

    header("Location: cadastro.php?erro=nao_encontrado");
    exit;
}

$stmt->close();

// Guarda os dados encontrados
$nome = $nome_atual;
$grupo = $grupo_atual;
$descricao = $descricao_atual;
$equipamentos = $equipamentos_atual ?? "";

// Processa a edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome_exercicio"] ?? "");
    $grupo = trim($_POST["grupo_muscular"] ?? "");
    $descricao = trim($_POST["descricao_exercicio"] ?? "");
    $equipamentos = trim($_POST["equipamentos"] ?? "");

    if ($nome == "" || $grupo == "") {

        $erro = "Preencha o nome e o grupo muscular.";

    } elseif (
        strlen($nome) > 100 ||
        strlen($grupo) > 50 ||
        strlen($descricao) > 255 ||
        strlen($equipamentos) > 255
    ) {

        $erro = "Um dos campos ultrapassou o limite permitido.";

    } else {

        try {

            $sql = "UPDATE EXERCICIOS
                    SET
                        NOME_EXERCICIO = ?,
                        GRUPO_MUSCULAR = ?,
                        DESCRICAO_EXERCICIO = ?,
                        EQUIPAMENTOS = ?
                    WHERE ID_EXERCICIOS = ?";

            $stmt = $conexao->prepare($sql);

            $stmt->bind_param(
                "ssssi",
                $nome,
                $grupo,
                $descricao,
                $equipamentos,
                $id
            );

            $stmt->execute();
            $stmt->close();

            header("Location: visualizar.php?id=" . $id . "&sucesso=editado");
            exit;

        } catch (mysqli_sql_exception $e) {

            $erro = "Não foi possível atualizar o exercício.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <title>Editar exercício</title>

</head>

<body>

<!-- Navegação -->

<nav>

    <div class="nav_header">

        <div class="nav_logo">

            <a href="../index.php">

                <img
                    src="../assets/gym.png"
                    alt="Logo"
                    class="gym-logo"
                >

            </a>

        </div>

    </div>

    <ul class="nav_link">

        <li>
            <a href="../index.php">Home</a>
        </li>

        <li>
            <a href="cadastro.php">Exercícios</a>
        </li>

        <li>
            <a href="../treinos/cadastro.php">Treinos</a>
        </li>

    </ul>

</nav>


<!-- Mensagem de erro -->

<?php if ($erro != ""): ?>

    <p class="mensagem-erro">

        <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>

    </p>

<?php endif; ?>


<!-- Formulário de edição -->

<form method="POST" action="editar.php">

    <input
        type="hidden"
        name="id"
        value="<?php echo $id_exercicio; ?>"
    >

    <div class="card">

        <h2>Editar exercício</h2>

        <div class="linha">

            <div class="coluna">

                <div class="form-group">

                    <label>Nome do exercício</label>

                    <input
                        type="text"
                        name="nome_exercicio"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >

                </div>

            </div>


            <div class="coluna">

                <div class="form-group">

                    <label>Grupo muscular</label>

                    <select name="grupo_muscular" required>

                        <option value="">Selecione um grupo muscular</option>

                        <?php

                        $grupos = [
                            "Peito",
                            "Costas",
                            "Bíceps",
                            "Tríceps",
                            "Pernas",
                            "Ombros",
                            "Abdômen",
                            "Panturrilha"
                        ];

                        foreach ($grupos as $opcao):

                        ?>

                            <option
                                value="<?php echo htmlspecialchars($opcao, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo ($grupo == $opcao) ? "selected" : ""; ?>
                            >

                                <?php echo htmlspecialchars($opcao, ENT_QUOTES, 'UTF-8'); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>


            <div class="coluna">

                <div class="form-group">

                    <label>Equipamentos</label>

                    <input
                        type="text"
                        name="equipamentos"
                        maxlength="255"
                        value="<?php echo htmlspecialchars($equipamentos, ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>

            </div>


            <div class="coluna">

                <div class="form-group">

                    <label>Descrição do exercício</label>

                    <input
                        type="text"
                        name="descricao_exercicio"
                        maxlength="255"
                        value="<?php echo htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>

            </div>

        </div>

    </div>

    <br>

    <input type="submit" value="Salvar alterações">

    <a href="cadastro.php">Cancelar</a>

</form>


</body>

</html>
