<?php 
    require_once "../conexao.php";/*quando precisamos acessar o banco utilizamos isso e a variável $conexao permite executar comandos no banco*/

    $sql_alunos = "SELECT * FROM ALUNO";
    $resultado_alunos = $conexao->query($sql_alunos);

    $sql_planos = "SELECT * FROM PLANO";
    $resultado_planos = $conexao->query($sql_planos);

    /*READERS, mostram os selects*/
    /*
      Temos duas consultas, cada um com sua própria variavel
      Não sobrescrevemos o resultado dos alunos ao buscar os planos
      Usaremos $resultado_alunos para preencher a seleção de alunos e $resultado_planos para a seleção de planos
    */

    $sql_matriculas = "SELECT MATRICULA.*, ALUNO.NOME_ALUNO, PLANO.NOME_PLANO
    FROM MATRICULA
    INNER JOIN ALUNO ON MATRICULA.ID_ALUNO_FK = ALUNO.ID_ALUNO
    INNER JOIN PLANO ON MATRICULA.ID_PLANO_FK = PLANO.ID_PLANO";

    /*busca as matriculas junto com os nomes dos alunos e planos relaciondados*/

$resultado_matriculas = $conexao->query($sql_matriculas);

  if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $idAluno = $_POST["id_aluno_fk"];
    $idPlano = $_POST["id_plano_fk"];
    $dataInicio = $_POST["data_inicio"];
    /*recebem os dado enviado pelo formulário nos campos e atribui a variável $nome, $cpf etc*/

    $sql = "SELECT DURACAO_PLANO FROM PLANO
    WHERE ID_PLANO = ?";

    $stmt = $conexao->prepare($sql);
    
    $stmt->bind_param("i", $idPlano);

    $stmt->execute();

    $resultado = $stmt->get_result();/*resultado da consulta*/
    $linha = $resultado->fetch_assoc();/*transforma a linha retornada em um array associativo, permitindo acessar a coluna pelo nome*/

    $duracaoPlano = $linha["DURACAO_PLANO"];/*pega a duração do plano no banco e armazena em uma variável PHP para poder usar depois*/

    $dataFim = new DateTime($dataInicio);/*cria a data inicial para o cálculo*/
    $dataFim->modify("+$duracaoPlano days");/*adiciona a duração do plano*/
    $dataFim = $dataFim->format("Y-m-d");/*formata a data em uma string*/
    /*calculo da data de término da matrícula*/

    $sql = "INSERT INTO MATRICULA
    (ID_ALUNO_FK, ID_PLANO_FK, DATA_INICIO, DATA_FIM)
    VALUES (?, ?, ?, ?)"; /*os ? são espaços reservados para os valores*/

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("iiss", $idAluno, $idPlano, $dataInicio, $dataFim);
    /*esse comando liga as variáveis aos "?" e iiss = inteiro, inteiro, string, string*/

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
            <li><a href="cadastro.php">Cadastro de matrículas</a></li> 
        </ul>
    </nav>

<form method="POST">
    <div class="card">
        <div class="linha">

            <div class="coluna">
                <div class="form-group">
                    <label>Aluno</label>

                    <select name="id_aluno_fk" required>
                        <option value="">Selecione um aluno</option>

                        <?php while ($aluno = $resultado_alunos->fetch_assoc()) { ?>
                            <option value="<?php echo $aluno["ID_ALUNO"]; ?>">
                                <?php echo htmlspecialchars($aluno["NOME_ALUNO"]); ?>
                            </option>
                        <?php } ?>
                    </select>
<!--
  A consulta que traz os alunos do banco de dados ficou guardada em $resultado_alunos
  fetch_assoc transforma a linha em um array associativo que permite acessar as colunas pelos nomes
-->
                </div>
            </div>

            <div class="coluna">
                <div class="form-group">
                    <label>Plano</label>

                    <select name="id_plano_fk" required>
                        <option value="">Selecione um plano</option>

                        <?php while ($plano = $resultado_planos->fetch_assoc()) { ?>
                            <option value="<?php echo $plano["ID_PLANO"]; ?>">
                                <?php echo htmlspecialchars($plano["NOME_PLANO"]); ?>
                            </option>
                        <?php } ?>
                    </select>
<!--
  A consulta que traz os planos do banco de dados ficou guardada em $resultado_planos
  fetch_assoc() transforma cada linha em um array associativo que permite acessar as colunas pelos nomes
-->
                </div>
            </div>

            <div class="coluna">
                <div class="form-group">
                    <label>Data de início</label>

                    <input type="date" name="data_inicio" required>
                </div>
            </div>

            <div class="coluna">
                <input type="submit" value="Cadastrar Matrícula">
            </div>

        </div>
    </div>
</form>

<?php if (isset($_GET["erro"]) && $_GET["erro"] == "matricula_vinculada"): ?>
    <p class="mensagem-erro">Não é possível excluir: esta matrícula possui pagamentos vinculados.</p>
<?php endif; ?>

<?php if (isset($_GET["erro"]) && $_GET["erro"] == "exclusao"): ?>
    <p class="mensagem-erro">Não foi possível excluir a matrícula.</p>
<?php endif; ?>

<?php if (isset($_GET["erro"]) && $_GET["erro"] == "matricula_nao_encontrada"): ?>
    <p class="mensagem-erro">Matrícula não encontrada.</p>
<?php endif; ?>

<?php if (isset($_GET["erro"]) && $_GET["erro"] == "id_invalido"): ?>
    <p class="mensagem-erro">ID da matrícula inválido.</p>
<?php endif; ?>

<div class="tabela-wrapper">
    <table class="table-content">
        <thead>
            <tr class="table-header-row">
                <th class="table-th">ID</th>
                <th class="table-th">Aluno</th>
                <th class="table-th">Plano</th>
                <th class="table-th">Data de início</th>
                <th class="table-th">Data de término</th>
                <th class="table-th">Status</th>
                <th class="table-th">Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($resultado_matriculas->num_rows > 0) {
                while ($linha = $resultado_matriculas->fetch_assoc()) { ?>
                    <tr class="table-data-row">
                        <td class="table-td"><?php echo $linha["ID_MATRICULA"]; ?></td>
                        <td class="table-td"><?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?></td>
                        <td class="table-td"><?php echo htmlspecialchars($linha["NOME_PLANO"]); ?></td>
                        <td class="table-td"><?php echo $linha["DATA_INICIO"]; ?></td>
                        <td class="table-td"><?php echo $linha["DATA_FIM"]; ?></td>
                        <td class="table-td"><?php echo htmlspecialchars($linha["STATUS_MATRICULA"]); ?></td>

                        <td class="table-td">
                            <div class="actions-container">
                                <a href="visualizar.php?id=<?php echo $linha["ID_MATRICULA"]; ?>" class="action-link">Visualizar</a>
                                <a href="editar.php?id=<?php echo $linha["ID_MATRICULA"]; ?>" class="action-link">Editar</a>
                            <form action="deletar.php" method="POST" onsubmit="return confirm('Deseja excluir esta matrícula?')">
                                <input type="hidden" name="id" value="<?php echo $linha["ID_MATRICULA"]; ?>">
                                <button type="submit" class="action-link delete-link">Deletar</button>
                            </form>
                            </div>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="7" class="mensagem-vazio">Nenhuma matrícula cadastrada.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>