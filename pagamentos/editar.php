<?php
require_once "../conexao.php";

$erro = null;

$id = $_GET["id"] ?? null;

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
  $erro = "ID inválido.";
}

if (empty($erro)) {
  $sql = "SELECT PAGAMENTO.*, ALUNO.NOME_ALUNO
  FROM PAGAMENTO
  INNER JOIN MATRICULA
  ON PAGAMENTO.ID_MATRICULA_FK = MATRICULA.ID_MATRICULA
  INNER JOIN ALUNO
  ON MATRICULA.ID_ALUNO_FK = ALUNO.ID_ALUNO
  WHERE PAGAMENTO.ID_PAGAMENTO = ?";

  $stmt = $conexao->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();

  $resultado = $stmt->get_result();
  $linha = $resultado->fetch_assoc();

  if (empty($linha)) {
    $erro = "Pagamento não encontrado.";
  }
}

  if (empty($erro) && $_SERVER["REQUEST_METHOD"] == "POST") {

      $idMatricula = $_POST["id_matricula_fk"];
      $valorPagamento = $_POST["valor_pagamento"];
      $dataVencimento = $_POST["data_vencimento"];
      $dataPagamento = $_POST["data_pagamento"];
      $formaPagamento = $_POST["forma_pagamento"];
      $statusPagamento = $_POST["status_pagamento"];

      // se data estiver vazia(""), salva como NULL
      $dataPagamento = $dataPagamento !== "" ? $dataPagamento : null;

      $formaPagamento = $formaPagamento !== "" ? $formaPagamento : null;

      if (empty($erro)) {

        $sql = "UPDATE PAGAMENTO SET
        ID_MATRICULA_FK = ?,
        VALOR_PAGAMENTO = ?,
        DATA_VENCIMENTO = ?,
        DATA_PAGAMENTO = ?,
        FORMA_PAGAMENTO = ?,
        STATUS_PAGAMENTO = ?
        WHERE ID_PAGAMENTO = ?";

        $stmt = $conexao->prepare($sql); /*prepare o SQL e guarda o statement(stmt) preparado na variável $stmt; $stmt é o prepared statement*/
        $stmt->bind_param("idssssi", $idMatricula, $valorPagamento, $dataVencimento, $dataPagamento, $formaPagamento, $statusPagamento, $id); /* -> acessa o método bind_param() do objeto $stmt; vincula as variáveis aos ? do SQL na mesma ordem*/
        $stmt->execute();/* -> acessa o método execute() do objeto $stmt; executa a consulta*/

        header("Location: cadastro.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
    rel="stylesheet"
    />
    <title>Editar Pagamento</title>
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

<form method="POST">
  <div class="card">
    <h2>Editar Pagamento</h2>
    <div class="linha">

      <div class="coluna">
        <div class="form-group">
          <label>Matrícula</label>
          <select id="id_matricula_fk" name="id_matricula_fk" required>
            <?php
              $sql_matriculas = "SELECT MATRICULA.ID_MATRICULA, ALUNO.NOME_ALUNO, PLANO.VALOR_PLANO
              FROM MATRICULA
              INNER JOIN ALUNO
              ON MATRICULA.ID_ALUNO_FK = ALUNO.ID_ALUNO
              INNER JOIN PLANO
              ON MATRICULA.ID_PLANO_FK = PLANO.ID_PLANO";

              $resultado_matriculas = $conexao->query($sql_matriculas);

              while ($matricula = $resultado_matriculas->fetch_assoc()) {
                $selecionada = $matricula["ID_MATRICULA"] == $linha["ID_MATRICULA_FK"] ? "selected" : "";

                echo "<option value='" . $matricula["ID_MATRICULA"] . "' data-valor='" . $matricula["VALOR_PLANO"] . "' $selecionada>" . $matricula["NOME_ALUNO"] . " — Matrícula " . $matricula["ID_MATRICULA"] . "</option>";
              }
            ?>
          </select>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Valor do Pagamento</label>
          <input type="number" id="valor_pagamento" name="valor_pagamento" step="0.01" min="0" value="<?php echo htmlspecialchars($linha["VALOR_PAGAMENTO"]); ?>" required>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Data de Vencimento</label>
          <input type="date" name="data_vencimento" value="<?php echo htmlspecialchars($linha["DATA_VENCIMENTO"]); ?>" required>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Data do Pagamento</label>
          <input type="date" name="data_pagamento" value="<?php echo htmlspecialchars($linha["DATA_PAGAMENTO"] ?? ""); ?>">
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Forma de Pagamento</label>
          <select name="forma_pagamento">
            <option value="">Selecione</option>
            <option value="Pix" <?php echo $linha["FORMA_PAGAMENTO"] == "Pix" ? "selected" : ""; ?>>Pix</option>
            <option value="Dinheiro" <?php echo $linha["FORMA_PAGAMENTO"] == "Dinheiro" ? "selected" : ""; ?>>Dinheiro</option>
            <option value="Cartão de crédito" <?php echo $linha["FORMA_PAGAMENTO"] == "Cartão de crédito" ? "selected" : ""; ?>>Cartão de crédito</option>
            <option value="Cartão de débito" <?php echo $linha["FORMA_PAGAMENTO"] == "Cartão de débito" ? "selected" : ""; ?>>Cartão de débito</option>
          </select>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Status do Pagamento</label>
          <select name="status_pagamento" required>
            <option value="Pendente" <?php echo $linha["STATUS_PAGAMENTO"] == "Pendente" ? "selected" : ""; ?>>Pendente</option>
            <option value="Pago" <?php echo $linha["STATUS_PAGAMENTO"] == "Pago" ? "selected" : ""; ?>>Pago</option>
          </select>
        </div>
      </div>

      <div class="coluna">
        <input type="submit" value="Salvar Alterações">
      </div>

    </div>
  </div>
</form>

<?php if (!empty($erro)): ?>
    <div class="mensagem-erro">
        <?php echo $erro; ?>
    </div>
<?php endif; ?>

<script>
    //puxa os campos de matrícula e valor do pagamento
    const selectMatricula = document.getElementById("id_matricula_fk");
    const campoValor = document.getElementById("valor_pagamento");

    //detecta quando uma matrícula é selecionada
    selectMatricula.addEventListener("change", function() {
        // pega a opção selecionada
        const opcaoSelecionada = this.options[this.selectedIndex];

        // busca o valor do plano armazenado no data-valor
        const valorPlano = opcaoSelecionada.getAttribute("data-valor");

        // preenche o campo com o valor encontrado
        campoValor.value = valorPlano || "";
    });
</script>

</body>
</html>