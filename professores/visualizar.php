<?php

require_once "../conexao.php"; /*conexão ao banco*/

$erro = null;

$id = $_GET["id"] ?? null;
/*exemplo: tenho um professor com ID: 5 e clico para visualizar,
o visualizar.php recebe esse ID pela URL e busca a avaliação*/

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {
        $sql = "SELECT * FROM PROFESSOR WHERE ID_PROFESSOR = ?"; /*"?" será substituido pelo $id*/
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result(); /*pega o resultado da consulta e guarda na variável $resultado*/
        $linha = $resultado->fetch_assoc(); /*pega uma linha do resultado e guarda na variável $linha*/

        if (empty($linha)) {
            $erro = "Professor não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/biblioteca-css.css" rel="stylesheet">

    <title>Visualizar Professor</title>
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
            <li><a href="cadastro.php">Cadastro</a></li>
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
            <h2>Detalhes do Professor</h2>
            <div class="card-visualizar">
                <p>ID: <?php echo htmlspecialchars($linha["ID_PROFESSOR"]); ?></p>
                <p>Nome: <?php echo htmlspecialchars($linha["NOME_PROFESSOR"]); ?></p>
                <p>CPF: <?php echo htmlspecialchars($linha["CPF_PROFESSOR"]); ?></p>
                <p>CREF: <?php echo htmlspecialchars($linha["CREF"]); ?></p>
                <p>Telefone: <?php echo htmlspecialchars($linha["TELEFONE_PROFESSOR"]); ?></p>
                <p>E-mail: <?php echo htmlspecialchars($linha["EMAIL_PROFESSOR"]); ?></p>
            </div>
            <div class="btn-voltar">
                <a href="cadastro.php" class="botao-voltar">Volte para os cadastros</a>
            </div>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>