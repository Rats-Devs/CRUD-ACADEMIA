
<?php

require_once "../conexao.php"; /* conexão ao banco */

$erro = null;

$id = $_GET["id"] ?? null;
/*exemplo: tenho uma avaliação com ID: 5 e clico para visualizar,
o visualizar.php recebe esse ID pela URL e busca a avaliação*/

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {

    $sql = "SELECT
                A.*,
                AL.NOME_ALUNO
            FROM AVALIACAO_FISICA A
            INNER JOIN ALUNO AL
                ON A.ID_ALUNO_FK = AL.ID_ALUNO
            WHERE A.ID_AVALIACAO = ?";
    /* o "?" será substituído pelo valor de $id.
    o JOIN permite buscar o nome do aluno relacionado à avaliação. */

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result(); /* pega o resultado da consulta */
    $linha = $resultado->fetch_assoc(); /* pega uma linha e guarda em $linha */

    if (empty($linha)) {
        $erro = "Avaliação não encontrada.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/biblioteca-css.css" rel="stylesheet">

    <title>Visualizar Avaliação</title>
</head>
<body>
    <nav>
        <div class="nav_header">
            <div class="nav_logo">
                <a href="../index.php">
                    <img src="../assets/gym.png" alt="logo" class="gym-logo">
                </a>
            </div>
        </div>

        <ul class="nav_link" id="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="cadastro.php">Cadastro de avaliações</a></li>
        </ul>
    </nav>
    <header>
        <div class="header_image">
            <img src="../assets/aponta.png" alt="header" />
        </div>
        <div class="header_content">
            <?php if (!empty($erro)): ?>
                <div class="mensagem-erro">
                    <?php echo $erro; ?>
                </div>
            <?php else: ?>
                <h2>Detalhes da Avaliação Física</h2>
                <div class="card-visualizar">
                    <p>ID: <?php echo htmlspecialchars($linha["ID_AVALIACAO"]); ?></p>
                    <p>Aluno: <?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?></p>
                    <p>Peso: <?php echo htmlspecialchars($linha["PESO"]); ?> kg</p>
                    <p>Altura: <?php echo htmlspecialchars($linha["ALTURA"]); ?> m</p>
                    <p>Percentual de gordura: <?php echo htmlspecialchars($linha["PERCENTUAL_GORDURA"] ?? "-"); ?> %</p>
                    <p>Massa magra: <?php echo htmlspecialchars($linha["MASSA_MAGRA"] ?? "-"); ?> kg</p>
                    <p>Braço: <?php echo htmlspecialchars($linha["BRACO"] ?? "-"); ?> cm</p>
                    <p>Cintura: <?php echo htmlspecialchars($linha["CINTURA"] ?? "-"); ?> cm</p>
                    <p>Quadril: <?php echo htmlspecialchars($linha["QUADRIL"] ?? "-"); ?> cm</p>
                    <p>Data da avaliação: <?php echo htmlspecialchars($linha["DATA_AVALIACAO"]); ?></p>
                </div>
                <div class="btn-voltar">
                    <a href="cadastro.php" class="botao-voltar">Volte para os cadastros</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>