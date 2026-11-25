<?php 
    require_once "../conexao.php";/*quando precisamos acessar o banco utilizamos isso e a variável $conexao permite executar comandos no banco*/

    $sql = "SELECT PAGAMENTO.*, ALUNO.NOME_ALUNO
    FROM PAGAMENTO
    INNER JOIN MATRICULA
    ON PAGAMENTO.ID_MATRICULA_FK = MATRICULA.ID_MATRICULA
    INNER JOIN ALUNO
    ON MATRICULA.ID_ALUNO_FK = ALUNO.ID_ALUNO";

    $resultado = $conexao->query($sql);

    $sql_matriculas = "SELECT MATRICULA.ID_MATRICULA, ALUNO.NOME_ALUNO, PLANO.VALOR_PLANO
    FROM MATRICULA
    INNER JOIN ALUNO
    ON MATRICULA.ID_ALUNO_FK = ALUNO.ID_ALUNO
    INNER JOIN PLANO
    ON MATRICULA.ID_PLANO_FK = PLANO.ID_PLANO";

    $resultado_matriculas = $conexao->query($sql_matriculas);
    /*essa consulta retorna o ID da matrícula e o nome do aluno, para que seja possivel a escolha no form*/

  if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $idMatricula = $_POST["id_matricula_fk"];
    $valorPagamento = $_POST["valor_pagamento"];
    $dataVencimento = $_POST["data_vencimento"];
    $dataPagamento = $_POST["data_pagamento"];
    $formaPagamento = $_POST["forma_pagamento"];
    $statusPagamento = $_POST["status_pagamento"];
    /*recebem os dado enviado pelo formulário nos campos e atribui as variáveis*/

    $dataPagamento = $dataPagamento !== "" ? $dataPagamento : null;

    $formaPagamento = $formaPagamento !== "" ? $formaPagamento : null;

      $sql = "INSERT INTO PAGAMENTO
      (ID_MATRICULA_FK, VALOR_PAGAMENTO, DATA_VENCIMENTO, DATA_PAGAMENTO, FORMA_PAGAMENTO, STATUS_PAGAMENTO)
      VALUES (?, ?, ?, ?, ?, ?)"; /*os ? são espaços reservados para os valores*/

      $stmt = $conexao->prepare($sql);

      $stmt->bind_param("idssss", $idMatricula, $valorPagamento, $dataVencimento, $dataPagamento, $formaPagamento, $statusPagamento);
      /*esse comando liga as variáveis aos "?" e i = inteiro, s = string, d = decimal*/

      $stmt->execute();/*executa o insert*/

      header("Location: cadastro.php");/*redireciona o usuário novamente para o cadastro*/
      exit;/*para a execução*/
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <title>Cadastro de Pagamentos</title>
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
    <div class="linha">
      <div class="coluna">
        <div class="form-group">
          <label>Matrícula</label>
            <select id="id_matricula_fk" name="id_matricula_fk" required>
              <option value="" disabled selected>Selecione uma matrícula</option>
                <?php
                  while ($linha = $resultado_matriculas->fetch_assoc()) {
                  echo "<option value='" . $linha["ID_MATRICULA"] . "' data-valor='" . $linha["VALOR_PLANO"] . "'>" . $linha["NOME_ALUNO"] . " — Matrícula " . $linha["ID_MATRICULA"] . "</option>";
                  }
                ?>
            </select>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Valor do pagamento</label>
          <input type="number" id="valor_pagamento" name="valor_pagamento" step="0.01" min="0" required readonly>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Data de vencimento</label>
          <input type="date" name="data_vencimento" required>
        </div>
      </div>

      <div class="coluna">
          <div class="form-group">
              <label>Data de pagamento</label>
              <input type="date" name="data_pagamento">
          </div>
      </div>

      <div class="coluna">
          <div class="form-group">
              <label>Forma de pagamento</label>
              <select name="forma_pagamento">
                  <option value="" disabled selected>Selecione a forma de pagamento</option>
                  <option value="Pix">Pix</option>
                  <option value="Dinheiro">Dinheiro</option>
                  <option value="Cartão de crédito">Cartão de crédito</option>
                  <option value="Cartão de débito">Cartão de débito</option>
              </select>
          </div>
      </div>

      <div class="coluna">
          <div class="form-group">
              <label>Status do pagamento</label>
              <select name="status_pagamento" required>
                  <option value="" disabled selected>Selecione o status</option>
                  <option value="Pendente">Pendente</option>
                  <option value="Pago">Pago</option>
              </select>
          </div>
      </div>

      <div class="coluna">
        <input type="submit" value="Registrar pagamento">
      </div>
    </div>
  </div>
</form>

  <?php if (!empty($erro)): ?>
    <div class="mensagem-erro">
      <?php echo $erro; ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET["erro"]) && $_GET["erro"] == "exclusao"): ?>
      <p class="mensagem-erro">Não foi possível excluir o pagamento.</p>
  <?php endif; ?>

  <?php if (isset($_GET["erro"]) && $_GET["erro"] == "id_invalido"): ?>
      <p class="mensagem-erro">ID do pagamento inválido.</p>
  <?php endif; ?>

    <div class="tabela-wrapper">
        <table class="table-content">
            <thead>
<tr class="table-header-row">
    <th class="table-th">ID</th>
    <th class="table-th">Aluno</th>
    <th class="table-th">Matrícula</th>
    <th class="table-th">Valor</th>
    <th class="table-th">Vencimento</th>
    <th class="table-th">Data do pagamento</th>
    <th class="table-th">Forma de pagamento</th>
    <th class="table-th">Status</th>
    <th class="table-th">Ações</th>
</tr>
            </thead>
            <tbody>
  <?php
    if ($resultado->num_rows > 0){
      while($linha = $resultado->fetch_assoc()){ /*enquanto tiver outra linha no resultado, pegue essa linha e coloque dentro da variável $linha*/
  ?>  
  <tr class="table-data-row">
      <td class="table-td"><?php echo htmlspecialchars($linha["ID_PAGAMENTO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["ID_MATRICULA_FK"]); ?></td>
      <td class="table-td">R$ <?php echo number_format($linha["VALOR_PAGAMENTO"], 2, ',', '.'); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["DATA_VENCIMENTO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["DATA_PAGAMENTO"] ?? "—"); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["FORMA_PAGAMENTO"] ?? "—"); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["STATUS_PAGAMENTO"]); ?></td>
  <td class="table-td">
      <div class="actions-container">
          <a href="visualizar.php?id=<?php echo $linha["ID_PAGAMENTO"]; ?>" class="action-link">Visualizar</a> <br>
          <a href="editar.php?id=<?php echo $linha["ID_PAGAMENTO"]; ?>" class="action-link">Editar</a> <br>
          <form action="deletar.php" method="POST" onsubmit="return confirm('Deseja excluir este pagamento?')">
            <input type="hidden" name="id" value="<?php echo $linha["ID_PAGAMENTO"]; ?>">
            <button type="submit" class="action-link delete-link">Deletar</button>
          </form>
      </div>
  </td>
  </tr>
      <?php
          }
        } else {
      ?>
        <tr>
          <td class="mensagem-vazio" colspan="9">Nenhum pagamento cadastrado.</td>
        </tr>
      <?php
        }
      ?>
            </tbody>
        </table>     
    </div>  
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