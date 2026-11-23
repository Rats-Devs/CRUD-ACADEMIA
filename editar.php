<?php

require_once "conexao.php";

$id = $_GET["id"];

$sql = "SELECT * FROM EMPRESTIMOS WHERE ID_EMPRESTIMOS = ?";

$stmt = $conexao->prepare($sql); 
/*
= é o operador de atribuição e serve para atribuir um valor dentro de um variável
-> é o operador de acesso a membros de um objeto, como métodos ou propriedades
*/
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

$linha = $resultado->fetch_assoc();

if (empty($linha)) {
    echo "Empréstimo não encontrado.";
    exit;
}

  $erro = null;

  if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $titulo = $_POST["titulo_livro"];
    /*
    recebe o dado enviado pelo formulário no campo titulo_livro e atribui a variável $titulo
    */
      if (empty($titulo)) { /*se o título estiver vazio, faça o que está dentro das {}*/
        $erro = "O título está vazio. Tente novamente."; /*guarda a mensagem de erro*/
      }

    $descricao = $_POST["descricao_aluno"];

    $data_devolucao = $_POST["data_devolucao"];
      if (empty($data_devolucao)) { /*se a data estiver vazia, faça o que está dentro das {}*/
        $erro = "A data está vazia. Tente novamente."; /*guarda a mensagem de erro*/
      } else{

          $data = DateTime::createFromFormat('Y-m-d', $data_devolucao);
            /*
            DateTime é uma classe em PHP para trabalhar com datas.
            :: permite acessar o método createFromFormat() da classe DateTime
            createFromFormat() tenta criar uma data usando o formato indicado
            o formato indicado é: Y-m-d 
            Y-m-d significa:
              Y=ano
              m=mes
              d=dia

            a data recebida do formulário é passada para a função
            e é guardado na variável $data para saber se o PHP conseguiu criar a data,
            se não conseguiu criar a data, o resultado pode ser false
            */
          if ($data === false) {
            /*
            aqui é tipo uma verificação de duas etapas, 
            o que tá guardado na variável $data é EXATAMENTE false?
            === é uma comparação exata e estrita
            */
          $erro = "A data de devolução não é válida. Tente novamente."; /*guarda a mensagem de erro*/
        } else{

            $hoje = new DateTime(date('Y-m-d')); /*cria a data de hoje usando apenas data, mes, ano e dia, não considera o horario*/

            if ($data < $hoje) {
                $erro = "A data de devolução não pode ser anterior a hoje. Tente novamente."; /*guarda a mensagem de erro*/
              }
          }
      }

    $registro = $_POST["registro_livro"];

      if (!filter_var($registro, FILTER_VALIDATE_INT) || $registro < 1) { /*! = NÃO; || = OU */
        /*
        se o registro NÃO for um inteiro válido
        OU se o registro for menor que 1,
        a validação vai falhar
        */
        $erro = "O registro não é válido. Tente novamente";/*guarda a mensagem de erro*/
      }

      if (empty($erro)) {  
          $sql = "UPDATE EMPRESTIMOS SET 
          TITULO_LIVRO = ?,
          DESCRICAO_ALUNO = ?,
          DATA_DEVOLUCAO = ?,
          REGISTRO_LIVRO = ?
          WHERE ID_EMPRESTIMOS = ?";

          $stmt = $conexao->prepare($sql);/*prepare o SQL e guarda o statement(stmt) preparado na variável $stmt; $stmt é o prepared statement*/
          $stmt->bind_param("sssii", $titulo, $descricao, $data_devolucao, $registro, $id); /* -> acessa o método bind_param() do objeto $stmt; vincula as variáveis aos ? do SQL na mesma ordem*/
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

    <link href="css/cadastro-css.css" rel="stylesheet">

    <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
    rel="stylesheet"
    />
    <title>Home</title>
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
          <input type="text" name="titulo_livro" value="<?php echo htmlspecialchars($linha["TITULO_LIVRO"]); ?>" placeholder="Atualize o título" required>
        </div>
      </div>
      <div class="coluna">
        <div class="form-group">
          <label>Descrição Aluno</label>
          <textarea name="descricao_aluno" placeholder="Atualize a descrição (opcional)"><?php echo htmlspecialchars($linha["DESCRICAO_ALUNO"]); ?></textarea>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Data devolução</label>
          <input type="date" name="data_devolucao" value="<?php echo htmlspecialchars($linha["DATA_DEVOLUCAO"]); ?>" required>
        </div>
      </div>

      <div class="coluna">
        <div class="form-group">
          <label>Registro do Livro</label>
          <input type="number" name="registro_livro" value="<?php echo htmlspecialchars($linha["REGISTRO_LIVRO"]); ?>" placeholder="Atualize o Registro" required>
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