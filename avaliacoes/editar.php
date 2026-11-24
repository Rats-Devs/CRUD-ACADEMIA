<?php
  require_once "../conexao.php";

  $erro = null;

  $id = $_GET["id"] ?? null;

  if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
      $erro = "ID inválido.";
  }

  /*busca os alunos para preencher o select*/
  $sqlAlunos = "SELECT ID_ALUNO, NOME_ALUNO FROM ALUNO ORDER BY NOME_ALUNO";
  $resultadoAlunos = $conexao->query($sqlAlunos);

  /*busca a avaliação que será editada*/
  if (empty($erro)) {
      $sql = "SELECT * FROM AVALIACAO_FISICA WHERE ID_AVALIACAO = ?";
      $stmt = $conexao->prepare($sql);
      $stmt->bind_param("i", $id);
      $stmt->execute();

      $resultado = $stmt->get_result();
      $linha = $resultado->fetch_assoc();

      if (empty($linha)) {
          $erro = "Avaliação não encontrada.";
      }
  }

  /*verifica se o formulário foi enviado pelo método POST*/
  if (empty($erro) && $_SERVER["REQUEST_METHOD"] == "POST") {

    $id_aluno = $_POST["id_aluno_fk"];

    $peso = $_POST["peso"];
    $altura = $_POST["altura"];

    $percentualGordura = $_POST["percentual_gordura"];
    $massaMagra = $_POST["massa_magra"];

    $braco = $_POST["braco"];
    $cintura = $_POST["cintura"];
    $quadril = $_POST["quadril"];

    $dataAvaliacao = $_POST["data_avaliacao"] ?? "";
    /*recebem os dado enviado pelo formulário nos campos e atribui as variáveis*/

    $percentualGordura = $percentualGordura !== "" ? $percentualGordura : null;
    $massaMagra = $massaMagra !== "" ? $massaMagra : null;

    $braco = $braco !== "" ? $braco : null;
    $cintura = $cintura !== "" ? $cintura : null;
    $quadril = $quadril !== "" ? $quadril : null;
    /* 
      tratamento de NULL
      exemplo: $braco = $braco !== "" ? $braco : null;
      se $braco for diferente de "", vai manter o valor recebido
      se $braco for igual a "", transforma em null
    */

      /*verifica os campos obrigatórios*/
      if ($id_aluno === "" || $peso === "" || $altura === "" || $dataAvaliacao === "") {
          $erro = "Preencha todos os campos obrigatórios.";
      }

      /*valida o ID do aluno*/
      if (empty($erro)) {
          if (!filter_var($id_aluno, FILTER_VALIDATE_INT) || $id_aluno < 1) {
              $erro = "Aluno inválido.";
          }
      }

      /*valida a data da avaliação*/
      if (empty($erro)) {
          $data = DateTime::createFromFormat('!Y-m-d', $dataAvaliacao);

          if (!$data || $data->format('Y-m-d') !== $dataAvaliacao) {
              $erro = "A data da avaliação não é válida.";
          } else {
              $hoje = new DateTime('today');

              if ($data > $hoje) {
                  $erro = "A data da avaliação não pode ser futura.";
              }
          }
      }

      if (empty($erro)) {
        $sql = "UPDATE AVALIACAO_FISICA SET 
        ID_ALUNO_FK = ?, 
        PESO = ?, 
        ALTURA = ?, 
        PERCENTUAL_GORDURA = ?, 
        MASSA_MAGRA = ?, 
        BRACO = ?, 
        CINTURA = ?, 
        QUADRIL = ?, 
        DATA_AVALIACAO = ? 
        WHERE ID_AVALIACAO = ?";

          $stmt = $conexao->prepare($sql); /* prepara o SQL e guarda o statement em $stmt */
          $stmt->bind_param("idddddddsi", $id_aluno, $peso, $altura, $percentualGordura, $massaMagra, $braco, $cintura, $quadril, $dataAvaliacao, $id); /* vincula as variáveis aos ? na mesma ordem */
          $stmt->execute(); /* executa a atualização */

          header("Location: cadastro.php"); /* redireciona para o cadastro */
          exit; /* encerra a execução após o redirecionamento */
      }
  }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet"/>
    
    <title>Editar Avaliação</title>
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

    <?php if (empty($erro)): ?>
        <form method="POST">
            <div class="card">
                <h2>Editar Avaliação Física</h2>
                <div class="linha">
                    <div class="coluna">
                        <div class="form-group">
                            <label>Aluno</label>
                            <select name="id_aluno_fk" required>
                                <option value="">Selecione o aluno</option>

                                <?php while ($aluno = $resultadoAlunos->fetch_assoc()): ?>
                                    <option value="<?php echo $aluno["ID_ALUNO"]; ?>" <?php echo ($aluno["ID_ALUNO"] == $linha["ID_ALUNO_FK"]) ? "selected" : ""; ?>>
                                        <?php echo htmlspecialchars($aluno["NOME_ALUNO"]); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Peso (kg)</label>
                            <input type="number" name="peso" min="0.01" max="635" step="0.01" value="<?php echo htmlspecialchars($linha["PESO"]); ?>" required>
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Altura (m)</label>
                            <input type="number" name="altura" min="0.50" max="2.72" step="0.01" value="<?php echo htmlspecialchars($linha["ALTURA"]); ?>" required>
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Percentual de gordura (%)</label>
                            <input type="number" name="percentual_gordura" min="0" max="100" step="0.01" value="<?php echo htmlspecialchars($linha["PERCENTUAL_GORDURA"] ?? ""); ?>">
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Massa magra (kg)</label>
                            <input type="number" name="massa_magra" min="0.01" max="635" step="0.01" value="<?php echo htmlspecialchars($linha["MASSA_MAGRA"] ?? ""); ?>">
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Braço (cm)</label>
                            <input type="number" name="braco" min="0.01" max="200" step="0.01" value="<?php echo htmlspecialchars($linha["BRACO"] ?? ""); ?>">
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Cintura (cm)</label>
                            <input type="number" name="cintura" min="0.01" max="300" step="0.01" value="<?php echo htmlspecialchars($linha["CINTURA"] ?? ""); ?>">
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Quadril (cm)</label>
                            <input type="number" name="quadril" min="0.01" max="300" step="0.01" value="<?php echo htmlspecialchars($linha["QUADRIL"] ?? ""); ?>">
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Data da avaliação</label>
                            <input type="date" name="data_avaliacao" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($linha["DATA_AVALIACAO"]); ?>" required>
                        </div>
                    </div>

                    <div class="coluna">
                        <input type="submit" value="Salvar Alterações">
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="mensagem-erro">
            <?php echo $erro; ?>
        </div>
    <?php endif; ?>
</body>
</html>