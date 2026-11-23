<?php
require_once "conexao.php";

$erro = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome_aluno"]);
    $cpf = preg_replace('/\D/', '', $_POST["cpf_aluno"]);
    $nascimento = $_POST["data_nascimento_aluno"];
    $telefone = trim($_POST["telefone_aluno"]);
    $email = trim($_POST["email_aluno"]);
    $status = $_POST["status_aluno"];

    $data = DateTime::createFromFormat('Y-m-d', $nascimento);

    if (empty($nome)) {
        $erro = "O nome está vazio.";
    } elseif (strlen($cpf) != 11) {
        $erro = "CPF inválido: precisa ter 11 números.";
    } elseif ($data === false || $data->format('Y-m-d') !== $nascimento) {
        $erro = "A data de nascimento não é válida.";
    } elseif ($data > new DateTime('today')) {
        $erro = "A data de nascimento não pode ser futura.";
    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } elseif (!in_array($status, ["Ativo", "Inativo"])) {
        $erro = "Status inválido.";
    }

    if (empty($erro)) {
        try {
            $sql = "INSERT INTO ALUNO
            (NOME_ALUNO, CPF_ALUNO, DATA_NASCIMENTO_ALUNO, TELEFONE_ALUNO, EMAIL_ALUNO, STATUS_ALUNO)
            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssssss", $nome, $cpf, $nascimento, $telefone, $email, $status);
            $stmt->execute();

            header("Location: alunos.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $erro = "Já existe um aluno com esse CPF.";
            } else {
                $erro = "Erro ao cadastrar o aluno.";
            }
        }
    }
}

$resultado = $conexao->query("SELECT * FROM ALUNO ORDER BY ID_ALUNO DESC");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/cadastro-css.css" rel="stylesheet">

    <title>Cadastro</title>
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

<form method="POST">
<div class="card">
  <h2>Cadastro</h2>
  <div class="linha">
    <div class="coluna">
      <div class="form-group">
        <label>Título do Livro</label>
        <input type="text" name="titulo_livro" placeholder="Digite o Título" required>
      </div>
    </div>
    <div class="coluna">
      <div class="form-group">
        <label>Descrição Aluno</label>
        <textarea name="descricao_aluno" placeholder="Digite a descrição (opcional)"></textarea>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Data devolução</label>
        <input type="date" name="data_devolucao" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Registro do Livro</label>
        <input type="number" name="registro_livro" placeholder="Digite o Registro" required>
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
      <th class="table-th">Título</th>
      <th class="table-th">Descrição</th>
      <th class="table-th">Devolução</th>
      <th class="table-th">Registro</th>
      <th class="table-th">Criado em</th>
      <th class="table-th">Ações</th>
  </tr>
            </thead>
            <tbody>
  <?php
    if ($resultado->num_rows > 0){
      while($linha = $resultado->fetch_assoc()){ /*enquanto tiver outra linha no resultado, pegue essa linha e coloque dentro da variável $linha*/
  ?>  
  <tr class="table-data-row">
    <td class="table-td"><?php echo htmlspecialchars($linha["ID_EMPRESTIMOS"]); ?></td>
    <td class="table-td"><?php echo htmlspecialchars($linha["TITULO_LIVRO"]); ?></td>
    <td class="table-td"><?php echo htmlspecialchars($linha["DESCRICAO_ALUNO"]); ?></td>
    <td class="table-td"><?php echo htmlspecialchars($linha["DATA_DEVOLUCAO"]); ?></td>
    <td class="table-td"><?php echo htmlspecialchars($linha["REGISTRO_LIVRO"]); ?></td>
    <td class="table-td"><?php echo htmlspecialchars($linha["CRIADO_EM"]); ?></td>
    <td class="table-td">
      <div class="actions-container">
        <a href="visualizar.php?id=<?php echo $linha["ID_EMPRESTIMOS"]; ?>" class="action-link">Visualizar</a> <br>
        <a href="editar.php?id=<?php echo $linha["ID_EMPRESTIMOS"]; ?>" class="action-link">Editar</a> <br>
        <a href="deletar.php?id=<?php echo $linha["ID_EMPRESTIMOS"]; ?>" class="action-link delete-link" onclick="return confirm('Deseja excluir?')">Deletar</a>
      </div>
    </td>
  </tr>
      <?php
          }
        } else {
      ?>
        <tr>
          <td class="mensagem-vazio" colspan="7">Nenhum empréstimo cadastrado.</td>
        </tr>
      <?php
        }
      ?>
            </tbody>
        </table>     
    </div>      
</body>
</html>