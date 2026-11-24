
<?php
require_once "../conexao.php";

$erro = null;

$id = $_GET["id"] ?? null;

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {
    $sql = "SELECT * FROM PROFESSOR WHERE ID_PROFESSOR = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $linha = $resultado->fetch_assoc();

    if (empty($linha)) {
        $erro = "Professor não encontrado.";
    }
}

if (empty($erro) && $_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome_professor"];
    $cpf = $_POST["cpf_professor"];
    $cref = $_POST["cref"];
    $telefone = $_POST["telefone_professor"];
    $email = $_POST["email_professor"];
    /*recebem os dados enviados pelo formulário nos campos e atribui a variável $nome, $cpf etc*/

    /*verifica os campos obrigatórios*/
    if (empty($nome) || empty($cpf) || empty($cref)) {
        $erro = "Preencha todos os campos obrigatórios.";
    }

    if (empty($erro)) {

        $sql = "UPDATE PROFESSOR SET
        NOME_PROFESSOR = ?,
        CPF_PROFESSOR = ?,
        CREF = ?,
        TELEFONE_PROFESSOR = ?,
        EMAIL_PROFESSOR = ?
        WHERE ID_PROFESSOR = ?";

        $stmt = $conexao->prepare($sql); /*prepare o SQL e guarda o statement(stmt) preparado na variável $stmt; $stmt é o prepared statement*/
        $stmt->bind_param("sssssi", $nome, $cpf, $cref, $telefone, $email, $id); /* -> acessa o método bind_param() do objeto $stmt; vincula as variáveis aos ? do SQL na mesma ordem*/
        $stmt->execute(); /* -> acessa o método execute() do objeto $stmt; executa a consulta*/
        header("Location: cadastro.php"); /*redireciona o user para o cadastro.php*/
        exit; /*encerra a execução depois do redirecionamento e impede que o código continue sendo executado mesmo depois do redirecionamento*/
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
    <title>Editar Professor</title>
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
            <li><a href="cadastro.php">Cadastro de professores</a></li>
        </ul>
    </nav>

<?php if (empty($erro)): ?>
<form method="POST">
  <div class="card">
    <h2>Editar Professor</h2>
    <div class="linha">
      <div class="coluna">
        <div class="form-group">
          <label>Nome do Professor</label>
          <input type="text" name="nome_professor" value="<?php echo htmlspecialchars($linha["NOME_PROFESSOR"]); ?>" placeholder="Atualize o nome do professor" required>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>CPF do Professor</label>
          <input type="text" id="cpf" name="cpf_professor" value="<?php echo htmlspecialchars($linha["CPF_PROFESSOR"]); ?>" maxlength="14" required>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>CREF do Professor</label>
          <input type="text" id="cref" name="cref" value="<?php echo htmlspecialchars($linha["CREF"]); ?>" maxlength="11" required>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Telefone do Professor</label>
          <input type="tel" id="telefone" name="telefone_professor" value="<?php echo htmlspecialchars($linha["TELEFONE_PROFESSOR"]); ?>" maxlength="15">
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>E-mail do Professor</label>
          <input type="email" name="email_professor" value="<?php echo htmlspecialchars($linha["EMAIL_PROFESSOR"]); ?>">
        </div>
      </div>

      <div class="coluna">
        <input type="submit" value="Salvar Alterações">
      </div>
    </div>
  </div>
</form>
<?php endif; ?>

<?php if (!empty($erro)): ?>
    <div class="mensagem-erro">
        <?php echo $erro; ?>
    </div>
<?php endif; ?>

<script>
const campoCPF = document.getElementById("cpf");

if (campoCPF) {
    campoCPF.addEventListener("input", function() {
        let cpf = campoCPF.value.replace(/\D/g, "");

        cpf = cpf.substring(0, 11);

        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

        campoCPF.value = cpf;
    });
}

const campoTelefone = document.getElementById("telefone");

if (campoTelefone) {
    campoTelefone.addEventListener("input", function() {
        let telefone = campoTelefone.value.replace(/\D/g, "");

        telefone = telefone.substring(0, 11);

        telefone = telefone.replace(/^(\d{2})(\d)/, "($1) $2");
        telefone = telefone.replace(/(\d{5})(\d)/, "$1-$2");

        campoTelefone.value = telefone;
    });
}

const campoCREF = document.getElementById("cref");

if (campoCREF) {
    campoCREF.addEventListener("input", function() {
        let valor = campoCREF.value.toUpperCase();

        let numeros = valor.replace(/\D/g, "").substring(0, 6);
        let uf = valor.replace(/[^A-Z]/g, "").replace("G", "").substring(0, 2);

        if (numeros.length === 6) {
            campoCREF.value = numeros + "-G/" + uf;
        } else {
            campoCREF.value = numeros;
        }
    });
}
</script>

</body>
</html>
