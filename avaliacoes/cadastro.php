<?php 
  require_once "../conexao.php";/*quando precisamos acessar o banco utilizamos isso e a variável $conexao permite executar comandos no banco*/

  $sql = "SELECT ID_ALUNO, NOME_ALUNO FROM ALUNO ORDER BY NOME_ALUNO";
  /*busca os alunos cadastrados e organiza os nomes em ordem alfabética.*/

  $resultado = $conexao->query($sql);/*preenche o <select> do formulário*/

  $sqlAvaliacoes = "SELECT
    A.*,
    AL.NOME_ALUNO
    FROM AVALIACAO_FISICA A
    INNER JOIN ALUNO AL
    ON A.ID_ALUNO_FK = AL.ID_ALUNO
    ORDER BY A.DATA_AVALIACAO DESC";
  /*
    Segunda consulta:
    A.* pega todas as colunas da tabela avaliações
    AL.NOME_ALUNO pega especificamente o nome do aluno

    FROM AVALIACAO_FISICA A:
    a tabela principal da consulta será AVALIACAO_FISICA e A é um apelido
    em vez de escrever AVALIACAO_FISICA.PESO, pode escrever A.PESO

    INNER JOIN ALUNO AL:
    adiciona a tabela ALUNO à consulta
    AL é o apelido dessa tabela
    precisa da adição dessa tabela pq a avaliação guarda ID_ALUNO_FK e não necessariamente o nome do aluno

    ON A.ID_ALUNO_FK = AL.ID_ALUNO:
    condição do relacionamento, basicamente diz:
    -Pegue o ID_ALUNO_FK da avaliação.
    -Compare com o ID_ALUNO da tabela de alunos.
    -Quando forem iguais, combine os registros.

  */

  $resultadoAvaliacoes = $conexao->query($sqlAvaliacoes);
  /*executa a consulta no PHP*/

  if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $id_aluno = $_POST["id_aluno_fk"];

    $peso = $_POST["peso"];
    $altura = $_POST["altura"];

    $percentualGordura = $_POST["percentual_gordura"];
    $massaMagra = $_POST["massa_magra"];

    $braco = $_POST["braco"];
    $cintura = $_POST["cintura"];
    $quadril = $_POST["quadril"];
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

    $sql = "INSERT INTO AVALIACAO_FISICA
    (ID_ALUNO_FK, PESO, ALTURA, PERCENTUAL_GORDURA, MASSA_MAGRA, BRACO, CINTURA, QUADRIL)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; /*os ? são espaços reservados para os valores*/

      $stmt = $conexao->prepare($sql);
      /*prepara a consulta*/

      $stmt->bind_param("iddddddd", $id_aluno, $peso, $altura, $percentualGordura, $massaMagra, $braco, $cintura, $quadril);
      /*esse comando liga as variáveis aos "?" e i = inteiro, d = decimal, "iddddddd"*/

      $stmt->execute();
      /*executa o insert*/

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

    <title>Cadastro</title>
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
            <li><a href="cadastro.php">Avaliações Físicas</a></li> 
        </ul>
    </nav>

<form method="POST">
<div class="card">
  <div class="linha">
    <div class="coluna">
      <div class="form-group">
        <label>Aluno</label>

        <select name="id_aluno_fk" required>
            <option value="">Selecione o aluno</option>

            <?php
            while ($aluno = $resultado->fetch_assoc()) {
                echo "<option value='" . $aluno["ID_ALUNO"] . "'>" . $aluno["NOME_ALUNO"] . "</option>";
            }
            ?>
            <!--Vai percorrer os registros da tabela ALUNO, adicionando o ID do aluno no value e exibindo o nome dele na opção-->
        </select>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Peso (kg)</label>
        <input type="number" name="peso" min="0.01" max="635" step="0.01" required>
        <!--step permite valores com duas casas decimais-->
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Altura (m)</label>
        <input type="number" name="altura" min="0.50" max="2.72" step="0.01" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Percentual de gordura (%)</label>
        <input type="number" name="percentual_gordura" min="0" max="100" step="0.01">
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Massa magra (kg)</label>
        <input type="number" name="massa_magra" min="0.01" max="635" step="0.01">
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Braço (cm)</label>
        <input type="number" name="braco" min="0.01" max="200" step="0.01">
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Cintura (cm)</label>
        <input type="number" name="cintura" min="0.01" max="300" step="0.01">
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Quadril (cm)</label>
        <input type="number" name="quadril" min="0.01" max="300" step="0.01">
      </div>
    </div>

    <div class="coluna">
      <input type="submit" value="Cadastrar">
    </div>
  </div>
</div>
</form>

  <?php if (!empty($erro)): ?>
    <div class="mensagem-erro">
      <?php echo $erro; ?>
    </div>
  <?php endif; ?>

    <div class="tabela-wrapper">
        <table class="table-content">
            <thead>
<tr class="table-header-row">
    <th class="table-th">ID</th>
    <th class="table-th">Aluno</th>
    <th class="table-th">Peso (kg)</th>
    <th class="table-th">Altura (m)</th>
    <th class="table-th">Gordura (%)</th>
    <th class="table-th">Massa magra (kg)</th>
    <th class="table-th">Braço (cm)</th>
    <th class="table-th">Cintura (cm)</th>
    <th class="table-th">Quadril (cm)</th>
    <th class="table-th">Data</th>
    <th class="table-th">Ações</th>
</tr>
</thead>

<tbody>
        <?php
        if ($resultadoAvaliacoes->num_rows > 0) {
            while ($linha = $resultadoAvaliacoes->fetch_assoc()) {
        ?>
          <tr class="table-data-row">
            <td class="table-td"><?php echo htmlspecialchars($linha["ID_AVALIACAO"]); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["PESO"]); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["ALTURA"]); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["PERCENTUAL_GORDURA"] ?? "-"); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["MASSA_MAGRA"]  ?? "-"); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["BRACO"]  ?? "-"); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["CINTURA"]  ?? "-"); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["QUADRIL"]  ?? "-"); ?></td>
            <td class="table-td"><?php echo htmlspecialchars($linha["DATA_AVALIACAO"]); ?></td>
            <!-- ?? "-": Se existir um valor ele será utilizado, se for NULL vai ser utilizado "-" -->
          <td class="table-td">
            <div class="actions-container">
              <a href="visualizar.php?id=<?php echo $linha["ID_AVALIACAO"]; ?>" class="action-link">Visualizar</a> <br>
              <a href="editar.php?id=<?php echo $linha["ID_AVALIACAO"]; ?>" class="action-link">Editar</a> <br>
              <a href="deletar.php?id=<?php echo $linha["ID_AVALIACAO"]; ?>" class="action-link delete-link" onclick="return confirm('Deseja excluir?')">Deletar</a>
            </div>
          </td>
          </tr>
            <?php
                }
            } else {
            ?>
                <tr>
                    <td class="mensagem-vazio" colspan="11">Nenhuma avaliação cadastrada.</td>
                </tr>
            <?php
            }
            ?>
      </tbody>
    </table>
  </div>
</body>
</html>