<?php

require_once "../conexao.php"; /*conexão ao banco*/

$erro = null;

$id = $_GET["id"] ?? null;
/*exemplo: tenho um aluno com ID: 5 e clico para visualizar,
o visualizar.php recebe esse ID pela URL e busca a avaliação*/

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {
        $sql = "SELECT PAGAMENTO.*, ALUNO.NOME_ALUNO
        FROM PAGAMENTO
        INNER JOIN MATRICULA /*relaciona o pagamento à matrícula correspondente*/
        ON PAGAMENTO.ID_MATRICULA_FK = MATRICULA.ID_MATRICULA
        INNER JOIN ALUNO /*relaciona a matrícula ao aluno que possui ela*/
        ON MATRICULA.ID_ALUNO_FK = ALUNO.ID_ALUNO
        WHERE PAGAMENTO.ID_PAGAMENTO = ?"; /*"?" será substituido pelo $id*/

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();/*pega o resultado da consulta e guarda na variável $resultado*/
        $linha = $resultado->fetch_assoc(); /*pega uma linha do resultado e guarda na variável $linha*/

        if (empty($linha)) {
            $erro = "Pagamento não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/biblioteca-css.css" rel="stylesheet">

    <title>Visualizar Pagamento</title>
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
            <li><a href="cadastro.php">Pagamentos</a></li> 
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
            <h2>Detalhes do Pagamento</h2>
            <div class="card-visualizar">
                <p>ID do pagamento: <?php echo htmlspecialchars($linha["ID_PAGAMENTO"]); ?></p>
                <p>Aluno: <?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?></p>
                <p>ID da matrícula: <?php echo htmlspecialchars($linha["ID_MATRICULA_FK"]); ?></p>
                <p>Valor: R$ <?php echo number_format($linha["VALOR_PAGAMENTO"], 2, ',', '.'); ?></p>
                <p>Data de vencimento: <?php echo htmlspecialchars($linha["DATA_VENCIMENTO"]); ?></p>
                <p>Data de pagamento: <?php echo htmlspecialchars($linha["DATA_PAGAMENTO"] ?? "—"); ?></p>
                <p>Forma de pagamento: <?php echo htmlspecialchars($linha["FORMA_PAGAMENTO"] ?? "—"); ?></p>
                <p>Status: <?php echo htmlspecialchars($linha["STATUS_PAGAMENTO"]); ?></p>
            </div>
            <div class="btn-voltar">
                <a href="cadastro.php" class="botao-voltar">Volte para os pagamentos</a>
            </div>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>