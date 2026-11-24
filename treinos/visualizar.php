
<?php
require_once "../conexao.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID inválido.");
}

// Busca os dados principais do treino
$sql = "SELECT
            T.*,
            A.NOME_ALUNO,
            P.NOME_PROFESSOR
        FROM TREINO T
        INNER JOIN ALUNO A
            ON T.ID_ALUNO_FK = A.ID_ALUNO
        INNER JOIN PROFESSOR P
            ON T.ID_PROFESSOR_FK = P.ID_PROFESSOR
        WHERE T.ID_TREINO = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$treino = $stmt->get_result()->fetch_assoc();

if (!$treino) {
    die("Treino não encontrado.");
}

// Busca os exercícios relacionados
$sql = "SELECT
            E.NOME_EXERCICIO,
            E.GRUPO_MUSCULAR,
            TE.ORDEM_EXERCICIO,
            TE.SERIES,
            TE.REPETICOES,
            TE.CARGA,
            TE.OBSERVACOES
        FROM TREINO_EXERCICIO TE
        INNER JOIN EXERCICIOS E
            ON TE.ID_EXERCICIOS_FK = E.ID_EXERCICIOS
        WHERE TE.ID_TREINO_FK = ?
        ORDER BY TE.ORDEM_EXERCICIO";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$exercicios = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link href="../css/biblioteca-css.css" rel="stylesheet">
    <title>Visualizar Treino</title>
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
        <li><a href="cadastro.php">Treinos</a></li>
    </ul>
</nav>

<header>
<div class="header_content">

    <h2><?php echo htmlspecialchars($treino["NOME_TREINO"]); ?></h2>

    <p><strong>Aluno:</strong>
        <?php echo htmlspecialchars($treino["NOME_ALUNO"]); ?>
    </p>

    <p><strong>Professor:</strong>
        <?php echo htmlspecialchars($treino["NOME_PROFESSOR"]); ?>
    </p>

    <p><strong>Data:</strong>
        <?php echo htmlspecialchars($treino["DATA_CRIACAO"]); ?>
    </p>

    <p><strong>Observações:</strong>
        <?php echo htmlspecialchars($treino["OBSERVACOES"] ?? ""); ?>
    </p>

    <h3>Exercícios</h3>

    <table class="table-content">

        <thead>
        <tr>
            <th>Ordem</th>
            <th>Exercício</th>
            <th>Grupo muscular</th>
            <th>Séries</th>
            <th>Repetições</th>
            <th>Carga</th>
            <th>Observações</th>
        </tr>
        </thead>

        <tbody>

        <?php while ($exercicio = $exercicios->fetch_assoc()): ?>

        <tr>
            <td><?php echo $exercicio["ORDEM_EXERCICIO"]; ?></td>

            <td><?php echo htmlspecialchars($exercicio["NOME_EXERCICIO"]); ?></td>

            <td><?php echo htmlspecialchars($exercicio["GRUPO_MUSCULAR"]); ?></td>

            <td><?php echo htmlspecialchars($exercicio["SERIES"]); ?></td>

            <td><?php echo htmlspecialchars($exercicio["REPETICOES"]); ?></td>

            <td><?php echo htmlspecialchars($exercicio["CARGA"]); ?> kg</td>

            <td><?php echo htmlspecialchars($exercicio["OBSERVACOES"] ?? ""); ?></td>
        </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

    <br>
    <a href="cadastro.php">Voltar</a>

</div>
</header>

</body>
</html>
