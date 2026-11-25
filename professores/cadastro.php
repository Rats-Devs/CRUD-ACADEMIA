
<?php 
    require_once "../conexao.php"; /*quando precisamos acessar o banco utilizamos isso e a variável $conexao permite executar comandos no banco*/

    $sql = "SELECT * FROM PROFESSOR"; /*READ, mostra os selects*/

    $resultado = $conexao->query($sql);

    /*MySql -> Select * -> variável $resultado*/

    if ($_SERVER["REQUEST_METHOD"] == "POST"){

        $nome = $_POST["nome_professor"];
        $cpf = $_POST["cpf_professor"];
        $cref = $_POST["cref"];
        $telefone = $_POST["telefone_professor"];
        $email = $_POST["email_professor"];
        /*recebem os dados enviados pelo formulário nos campos e atribui a variável $nome, $cpf etc*/

        $sql = "INSERT INTO PROFESSOR
        (NOME_PROFESSOR, CPF_PROFESSOR, CREF, TELEFONE_PROFESSOR, EMAIL_PROFESSOR)
        VALUES (?, ?, ?, ?, ?)"; /*os ? são espaços reservados para os valores*/

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param("sssss", $nome, $cpf, $cref, $telefone, $email);
        /*esse comando liga as variáveis aos "?" e sssss = string, string, string, string, string*/

        $stmt->execute(); /*executa o insert*/

        header("Location: cadastro.php"); /*redireciona o usuário novamente para o cadastro*/
        exit; /*para a execução*/
    }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <title>Cadastro de Professores</title>
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

<form method="POST">
<div class="card">
  <div class="linha">
    <div class="coluna">
      <div class="form-group">
        <label>Nome do Professor</label>
        <input type="text" name="nome_professor" placeholder="Digite o nome do professor" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>CPF do Professor</label>
        <input type="text" id="cpf" name="cpf_professor" placeholder="Digite o CPF do professor" maxlength="14" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>CREF</label>
        <input type="text" id="cref" name="cref" placeholder="Ex: 012345-G/SP" maxlength="11" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>Telefone do Professor</label>
        <input type="tel" id="telefone" name="telefone_professor" placeholder="Ex: (11) 99999-9999" maxlength="15" required>
      </div>
    </div>

    <div class="coluna">
      <div class="form-group">
        <label>E-mail do Professor</label>
        <input type="email" name="email_professor" placeholder="Ex: juca@gmail.com" required>
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

<?php if (isset($_GET["erro"]) && $_GET["erro"] == "professor_vinculado"): ?>
    <p class="mensagem-erro">
        Não é possível excluir: este professor está vinculado a um ou mais treinos.
    </p>
<?php endif; ?>

    <div class="tabela-wrapper">
        <table class="table-content">
            <thead>
                <tr class="table-header-row">
                    <th class="table-th">ID</th>
                    <th class="table-th">Nome</th>
                    <th class="table-th">CPF</th>
                    <th class="table-th">CREF</th>
                    <th class="table-th">Telefone</th>
                    <th class="table-th">E-mail</th>
                    <th class="table-th">Ações</th>
                </tr>
            </thead>
            <tbody>
  <?php
    if ($resultado->num_rows > 0){
      while($linha = $resultado->fetch_assoc()){ /*enquanto tiver outra linha no resultado, pegue essa linha e coloque dentro da variável $linha*/
  ?>  
  <tr class="table-data-row">
      <td class="table-td"><?php echo htmlspecialchars($linha["ID_PROFESSOR"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["NOME_PROFESSOR"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["CPF_PROFESSOR"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["CREF"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["TELEFONE_PROFESSOR"]); ?></td>
      <td class="table-td"><?php echo htmlspecialchars($linha["EMAIL_PROFESSOR"]); ?></td>
    <td class="table-td">
      <div class="actions-container">
        <a href="visualizar.php?id=<?php echo $linha["ID_PROFESSOR"]; ?>" class="action-link">Visualizar</a> <br>
        <a href="editar.php?id=<?php echo $linha["ID_PROFESSOR"]; ?>" class="action-link">Editar</a> <br>
        <form action="deletar.php" method="POST" onsubmit="return confirm('Deseja excluir?')">
            <input type="hidden" name="id" value="<?php echo $linha["ID_PROFESSOR"]; ?>">
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
          <td class="mensagem-vazio" colspan="7">Nenhum professor cadastrado.</td>
        </tr>
      <?php
        }
      ?>
            </tbody>
        </table>    
    </div>      

<script>
const campoCPF = document.getElementById("cpf"); /*guarda o campo de CPF na variável campoCPF para poder manipular*/

campoCPF.addEventListener("input", function(){ /*adiciona um evento que sempre que alguém digitar, apagar ou alterar o CPF essa função será executada*/
    let cpf = campoCPF.value.replace(/\D/g, ""); /*substitui o que não for número por "", ou seja, remove*/

    cpf = cpf.substring(0, 11); /*limita a entrada de números para 11*/

    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    /*adiciona os pontos e o hífen*/

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

const campoCREF = document.getElementById("cref");

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
</script>

</body>
</html>