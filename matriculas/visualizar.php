
<?php

require_once "../conexao.php"; /* conexão ao banco */

$erro = null;

$id = $_GET["id"] ?? null;
/* exemplo: tenho uma matrícula com ID: 1 e clico para visualizar,
o visualizar.php recebe esse ID pela URL e busca a matrícula */

/* verifica se o ID recebido é um número inteiro válido e maior que zero */
if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {
    /* busca os dados da matrícula e os nomes do aluno e do plano relacionados */
    $sql = "SELECT MATRICULA.*, ALUNO.NOME_ALUNO, PLANO.NOME_PLANO
    FROM MATRICULA
    INNER JOIN ALUNO ON MATRICULA.ID_ALUNO_FK = ALUNO.ID_ALUNO
    INNER JOIN PLANO ON MATRICULA.ID_PLANO_FK = PLANO.ID_PLANO
    WHERE MATRICULA.ID_MATRICULA = ?"; /* o ? será substituído pelo $id */

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id); /* i indica que o ID é um inteiro */
    $stmt->execute();

    $resultado = $stmt->get_result(); /* pega o resultado da consulta e guarda na variável $resultado */
    $linha = $resultado->fetch_assoc(); /* pega uma linha do resultado e guarda na variável $linha */

    if (empty($linha)) {
        $erro = "Matrícula não encontrada.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/biblioteca-css.css" rel="stylesheet">

    <title>Visualizar Matrícula</title>
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
            <li><a href="cadastro.php">Cadastro de Matrículas</a></li>
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
                <h2>Detalhes da Matrícula</h2>

                <div class="card-visualizar">
                    <p>ID: <?php echo htmlspecialchars($linha["ID_MATRICULA"]); ?></p>
                    <p>Aluno: <?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?></p>
                    <p>Plano: <?php echo htmlspecialchars($linha["NOME_PLANO"]); ?></p>
                    <p>Data de início: <?php echo htmlspecialchars($linha["DATA_INICIO"]); ?></p>
                    <p>Data de término: <?php echo htmlspecialchars($linha["DATA_FIM"]); ?></p>
                    <p>Status: <?php echo htmlspecialchars($linha["STATUS_MATRICULA"]); ?></p>
                </div>

                <div class="btn-voltar">
                    <a href="cadastro.php" class="botao-voltar">Voltar para as matrículas</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>
