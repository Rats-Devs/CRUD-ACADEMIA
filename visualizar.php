<?php

require_once "conexao.php"; /*conexão ao banco*/

$erro = null;

$id = $_GET["id"];
/*exemplo: tenho um livro com ID: 5 e clico para visualizar,
abra o visualizar.php e quero o registro de ID 5, isso que esse comando faz*/

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {
        $sql = "SELECT * FROM EMPRESTIMOS WHERE ID_EMPRESTIMOS = ?"; /*"?" será substituido pelo $id*/
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();/*pega o resultado da consulta e guarda na variável $resultado*/

        $linha = $resultado->fetch_assoc(); /*pega uma linha do resultado e guarda na variável $linha*/

        if (empty($linha)) {
            $erro = "Empréstimo não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/biblioteca-css.css" rel="stylesheet">

    <title>Visualizar Empréstimo</title>
</head>
<body>
    <nav>
        <div class="nav_header">
            <div class="nav_logo">
                <a href="index.php">
                    <img src="assets/logo-dark.png" alt="logo" class="logo-dark">
                </a>
            </div>
        </div>
        
        <ul class="nav_link" id="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="cadastro.php">Cadastro</a></li> 
        </ul>
    </nav>

    <header>
        <div class="header_image">
            <img src="assets/aponta.png" alt="header" />
        </div>
        <div class="header_content">
            <?php if (!empty($erro)): ?>
                <div class="mensagem-erro">
                    <?php echo $erro; ?>
                </div>
            <?php else: ?>
            <h2>Detalhes do Empréstimo</h2>
            <br>
                <p>ID: <?php echo htmlspecialchars($linha["ID_EMPRESTIMOS"]); ?></p>
                <p>Título: <?php echo htmlspecialchars($linha["TITULO_LIVRO"]); ?></p>
                <p>Descrição: <?php echo htmlspecialchars($linha["DESCRICAO_ALUNO"]); ?></p>
                <p>Data de devolução: <?php echo htmlspecialchars($linha["DATA_DEVOLUCAO"]); ?></p>
                <p>Registro do livro: <?php echo htmlspecialchars($linha["REGISTRO_LIVRO"]); ?></p>
                <p>Criado em: <?php echo htmlspecialchars($linha["CRIADO_EM"]); ?></p>
            <div class="header_btn">
                <a href="cadastro.php"><button>Volte para os cadastros</button></a>
            </div>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>