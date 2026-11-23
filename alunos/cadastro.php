<?php 
    require_once "../conexao.php";/*quando precisamos acessar o banco utilizamos isso e a variável $conexao permite executar comandos no banco*/

    $sql = "SELECT * FROM ALUNO";/*READ, mostra os selects*/

    $resultado = $conexao->query($sql);

    /*MySql -> Select * -> variável $resultado*/

  if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $nome = $_POST["nome_aluno"];
    $cpf = $_POST["cpf_aluno"];
    $data_nascimento = $_POST["data_nascimento_aluno"];
    $telefone = $_POST["telefone_aluno"];
    $email = $_POST["email_aluno"];
    /*recebem os dado enviado pelo formulário nos campos e atribui a variável $nome, $cpf etc*/

      $sql = "INSERT INTO ALUNO
      (NOME_ALUNO, CPF_ALUNO, DATA_NASCIMENTO_ALUNO, TELEFONE_ALUNO, EMAIL_ALUNO)
      VALUES (?, ?, ?, ?, ?)"; /*os ? são espaços reservados para os valores*/

      $stmt = $conexao->prepare($sql);

      $stmt->bind_param("sssss", $nome, $cpf, $data_nascimento, $telefone, $email);
      /*esse comando liga as variáveis aos "?" e sssss = string, string, string, string, string*/

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
              <a href="index.php">
                <img src="../assets/logo-dark.png" alt="logo" class="logo-dark">
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
  <h2>Cadastro</h2>
  <div class="linha">
    <div class="coluna">
      <div class="form-group">
        <label>Nome do Aluno</label>
        <input type="text" name="nome_aluno" placeholder="Digite o nome do aluno" required>
      </div>
    </div>
    <div class="coluna">
      <div class="form-group">
        <label>CPF aluno</label>
        <input type="text" id="cpf" name="cpf_aluno" placeholder="Digite o CPF do aluno" maxlenght="14" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Data de nascimento do Aluno</label>
        <input type="date" name="data_nascimento_aluno" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Telefone do aluno</label>
        <input type="tel" id="telefone" name="telefone_aluno" placeholder="ex: (11) 99999-9999" maxlenght="15" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Email do aluno</label>
        <input type="email" name="email_aluno" placeholder="ex: juca@gmail.com" required>
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
    <th class="table-th">Nome</th>
    <th class="table-th">CPF</th>
    <th class="table-th">Nascimento</th>
    <th class="table-th">Telefone</th>
    <th class="table-th">E-mail</th>
    <th class="table-th">Cadastro</th>
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
      <td class="table-td"><?php echo htmlspecialchars($linha["ID_ALUNO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["NOME_ALUNO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["CPF_ALUNO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["DATA_NASCIMENTO_ALUNO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["TELEFONE_ALUNO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["EMAIL_ALUNO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["DATA_CADASTRO_ALUNO"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["STATUS_ALUNO"]); ?></td>
    <td class="table-td">
      <div class="actions-container">
        <a href="visualizar.php?id=<?php echo $linha["ID_ALUNO"]; ?>" class="action-link">Visualizar</a> <br>
        <a href="editar.php?id=<?php echo $linha["ID_ALUNO"]; ?>" class="action-link">Editar</a> <br>
        <a href="deletar.php?id=<?php echo $linha["ID_ALUNO"]; ?>" class="action-link delete-link" onclick="return confirm('Deseja excluir?')">Deletar</a>
      </div>
    </td>
  </tr>
      <?php
          }
        } else {
      ?>
        <tr>
          <td class="mensagem-vazio" colspan="9">Nenhum Aluno cadastrado.</td>
        </tr>
      <?php
        }
      ?>
            </tbody>
        </table>     
    </div>      

<script>
const campoCPF = document.getElementById("cpf");/*guarda o campo de CPF na variável campoCPF para poder manipular*/

campoCPF.addEventListener("input", function() {/*adiciona um evento que sempre que alguem digitar, apaagar ou alterar o CPF essa função sera executada*/
    let cpf = campoCPF.value.replace(/\D/g, "");/*substitui oq não for número por "", ou seja, remove*/

    cpf = cpf.substring(0, 11);/*limita a entrada de numeros para 11*/

    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    /*adiciona os pontos e o hifen*/

    campoCPF.value = cpf;
});

const campoTelefone = document.getElementById("telefone");

campoTelefone.addEventListener("input", function() {
    let telefone = campoTelefone.value.replace(/\D/g, "");

    telefone = telefone.substring(0, 11);

    telefone = telefone.replace(/^(\d{2})(\d)/, "($1) $2");
    telefone = telefone.replace(/(\d{5})(\d)/, "$1-$2");

    campoTelefone.value = telefone;
});
</script>

</body>
</html>