<?php
require_once "../conexao.php";

$erro = null;

$id = $_GET["id"] ?? null;

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {
    $sql = "SELECT * FROM ALUNO WHERE ID_ALUNO = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $linha = $resultado->fetch_assoc();

    if (empty($linha)) {
        $erro = "Aluno não encontrado.";
    }
}

  if (empty($erro) && $_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome_aluno"];
    $cpf = $_POST["cpf_aluno"];
    $data_nascimento = $_POST["data_nascimento_aluno"];
    $telefone = $_POST["telefone_aluno"];
    $email = $_POST["email_aluno"];
    /*recebem os dado enviado pelo formulário nos campos e atribui a variável $nome, $cpf etc*/

        /*verifica os campos obrigatorios*/
        if (empty($nome) || empty($cpf) || empty($data_nascimento)) {
            $erro = "Preencha todos os campos obrigatórios.";
        }

        /*valida a data de nascimento*/
        if (empty($erro)) {

        $data = DateTime::createFromFormat('!Y-m-d', $data_nascimento);

        if (!$data || $data->format('Y-m-d') !== $data_nascimento) {
            $erro = "A data de nascimento não é válida.";
        } else {

            $hoje = new DateTime('today');

            if ($data > $hoje) {
                $erro = "A data de nascimento não pode ser futura.";
            }
        }
    }

      if (empty($erro)) {

        $sql = "UPDATE ALUNO SET
        NOME_ALUNO = ?,
        CPF_ALUNO = ?,
        DATA_NASCIMENTO_ALUNO = ?,
        TELEFONE_ALUNO = ?,
        EMAIL_ALUNO = ?
        WHERE ID_ALUNO = ?";

        $stmt = $conexao->prepare($sql);/*prepare o SQL e guarda o statement(stmt) preparado na variável $stmt; $stmt é o prepared statement*/
        $stmt->bind_param("sssssi", $nome, $cpf, $data_nascimento, $telefone, $email, $id);/* -> acessa o método bind_param() do objeto $stmt; vincula as variáveis aos ? do SQL na mesma ordem*/
        $stmt->execute();/* -> acessa o método execute() do objeto $stmt; executa a consulta*/
        header("Location: cadastro.php");/*redireciona o user para o cadastro.php*/
        exit;/*encerra a execução depois do redirecionamento e impede que o código continue sendo executado mesmo depois do redirecionamento*/
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
    <title>Editar Aluno</title>
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

<form method="POST">
  <div class="card">
    <h2>Editar Aluno</h2>
    <div class="linha">
      <div class="coluna">
        <div class="form-group">
          <label>Nome do Aluno</label>
          <input type="text" name="nome_aluno" value="<?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?>" placeholder="Atualize o nome do aluno" required>
        </div>
      </div>
      <div class="coluna">
        <div class="form-group">
          <label>CPF</label>
              <input type="text" name="cpf_aluno" value="<?php echo htmlspecialchars($linha["CPF_ALUNO"]); ?>" maxlength="14" required>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Data de Nascimento</label>
          <input type="date" name="data_nascimento_aluno" value="<?php echo htmlspecialchars($linha["DATA_NASCIMENTO_ALUNO"]); ?>" required>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Telefone</label>
          <input type="tel" name="telefone_aluno" value="<?php echo htmlspecialchars($linha["TELEFONE_ALUNO"]); ?>" maxlength="15">
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>E-mail</label>
          <input type="email" name="email_aluno" value="<?php echo htmlspecialchars($linha["EMAIL_ALUNO"]); ?>">
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

</body>
</html>