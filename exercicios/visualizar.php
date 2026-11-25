
<?php

require_once "../conexao.php";

// Verifica se o ID foi informado
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: cadastro.php");
    exit;
}

// Busca o exercício pelo ID
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
    $nome,
    $grupo,
    $descricao,
    $equipamentos
);

// Verifica se o exercício existe
if (!$stmt->fetch()) {

    $stmt->close();

    header("Location: cadastro.php?erro=nao_encontrado");
    exit;
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <title>Visualizar exercício</title>

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


<!-- Informações do exercício -->

<div class="card">

    <h2>Informações do exercício</h2>

    <div class="linha">

        <div class="coluna">

            <div class="form-group">

                <label>ID do exercício</label>

                <p>
                    <?php echo $id_exercicio; ?>
                </p>

            </div>

        </div>


        <div class="coluna">

            <div class="form-group">

                <label>Nome do exercício</label>

                <p>
                    <?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8'); ?>
                </p>

            </div>

        </div>


        <div class="coluna">

            <div class="form-group">

                <label>Grupo muscular</label>

                <p>
                    <?php echo htmlspecialchars($grupo, ENT_QUOTES, 'UTF-8'); ?>
                </p>

            </div>

        </div>


        <div class="coluna">

            <div class="form-group">

                <label>Equipamentos</label>

                <p>
                    <?php echo htmlspecialchars($equipamentos ?? "", ENT_QUOTES, 'UTF-8'); ?>
                </p>

            </div>

        </div>


        <div class="coluna">

            <div class="form-group">

                <label>Descrição</label>

                <p>
                    <?php echo htmlspecialchars($descricao ?? "", ENT_QUOTES, 'UTF-8'); ?>
                </p>

            </div>

        </div>

    </div>

</div>


<!-- Ações -->

<div class="acoes-treino">

    <a href="cadastro.php">
        Voltar
    </a>

    <a href="editar.php?id=<?php echo $id_exercicio; ?>">
        Editar exercício
    </a>

</div>


</body>

</html>
