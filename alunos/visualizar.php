<?php

require_once "../conexao.php"; /*conexão ao banco*/

$erro = null;

$id = $_GET["id"] ?? null;
/*exemplo: tenho um aluno com ID: 5 e clico para visualizar,
abra o visualizar.php e quero o registro de ID 5, isso que esse comando faz*/

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {
        $sql = "SELECT * FROM ALUNO WHERE ID_ALUNO = ?"; /*"?" será substituido pelo $id*/
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();/*pega o resultado da consulta e guarda na variável $resultado*/
        $linha = $resultado->fetch_assoc(); /*pega uma linha do resultado e guarda na variável $linha*/

        if (empty($linha)) {
            $erro = "Aluno não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/biblioteca-css.css" rel="stylesheet">

    <title>Visualizar Aluno</title>
</head>
<body>
    <nav>
        <div class="nav_header">
            <div class="nav_logo">
                <a href="../index.php">
                    <img src="../assets/logo-dark.png" alt="logo" class="logo-dark">
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
            <h2>Detalhes do Aluno</h2>
            <div class="card-visualizar">
                <p>ID: <?php echo htmlspecialchars($linha["ID_ALUNO"]); ?></p>
                <p>Nome: <?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?></p>
                <p>CPF: <?php echo htmlspecialchars($linha["CPF_ALUNO"]); ?></p>
                <p>Data de nascimento: <?php echo htmlspecialchars($linha["DATA_NASCIMENTO_ALUNO"]); ?></p>
                <p>Telefone: <?php echo htmlspecialchars($linha["TELEFONE_ALUNO"]); ?></p>
                <p>E-mail: <?php echo htmlspecialchars($linha["EMAIL_ALUNO"]); ?></p>
                <p>Data de cadastro: <?php echo htmlspecialchars($linha["DATA_CADASTRO_ALUNO"]); ?></p>
                <p>Status: <?php echo htmlspecialchars($linha["STATUS_ALUNO"]); ?></p>
            </div>
            <div class="header_btn">
                <a href="cadastro.php" class="botao-voltar">Volte para os cadastros</a>
            </div>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>