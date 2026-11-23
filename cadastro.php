<?php 
    require_once "conexao.php";/*quando precisamos acessar o banco utilizamos isso e a variável $conexao permite executar comandos no banco*/

    $sql = "SELECT * FROM EMPRESTIMOS";/*READ, mostra os selects*/

    $resultado = $conexao->query($sql);

    /*MySql -> Select * -> variável $resultado*/

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

if (empty($erro)) { /*se não tiver erros, faça o insert*/
    $sql = "INSERT INTO EMPRESTIMOS
    (TITULO_LIVRO, DESCRICAO_ALUNO, DATA_DEVOLUCAO, REGISTRO_LIVRO)
    VALUES (?, ?, ?, ?)"; /*os ? são espaços reservados para os valores*/
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $descricao, $data_devolucao, $registro); /*esse comando liga as variáveis aos "?" e sssi = string, string, string, integer*/
    $stmt->execute(); /*executa o insert*/

    header("Location: cadastro.php"); /*redireciona o usuário novamente para o cadastro*/
    exit;/*para a execução*/
    }
  }
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