
<?php
require_once "../conexao.php";

$erro = null;

$id = $_GET["id"] ?? null;

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) {
    $erro = "ID inválido.";
}

if (empty($erro)) {
    $sql = "SELECT * FROM MATRICULA WHERE ID_MATRICULA = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $linha = $resultado->fetch_assoc();

    if (empty($linha)) {
        $erro = "Matrícula não encontrada.";
    }
}

$sql_alunos = "SELECT * FROM ALUNO";
$resultado_alunos = $conexao->query($sql_alunos);

$sql_planos = "SELECT * FROM PLANO";
$resultado_planos = $conexao->query($sql_planos);

if (empty($erro) && $_SERVER["REQUEST_METHOD"] == "POST") {

    $idAluno = $_POST["id_aluno_fk"];
    $idPlano = $_POST["id_plano_fk"];
    $dataInicio = $_POST["data_inicio"];

    /* recebem os dados enviados pelo formulário nos campos e atribuem às variáveis */

    /* verifica os campos obrigatórios */
    if (empty($idAluno) || empty($idPlano) || empty($dataInicio)) {
        $erro = "Preencha todos os campos obrigatórios.";
    }

    /* verifica se o plano selecionado existe e busca sua duração */
    if (empty($erro)) {
        $sql = "SELECT DURACAO_PLANO FROM PLANO WHERE ID_PLANO = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $idPlano);
        $stmt->execute();

        $resultado_plano = $stmt->get_result();
        $plano = $resultado_plano->fetch_assoc();

        if (empty($plano)) {
            $erro = "Plano não encontrado.";
        } else {
            $duracaoPlano = $plano["DURACAO_PLANO"];
        }
    }

    /* calcula a data de término com base na data de início e na duração do plano */
    if (empty($erro)) {
        $dataFim = new DateTime($dataInicio);
        $dataFim->modify("+$duracaoPlano days");
        $dataFim = $dataFim->format("Y-m-d");

        $sql = "UPDATE MATRICULA SET
        ID_ALUNO_FK = ?,
        ID_PLANO_FK = ?,
        DATA_INICIO = ?,
        DATA_FIM = ?
        WHERE ID_MATRICULA = ?";

        $stmt = $conexao->prepare($sql); /* prepare o SQL e guarda o statement (stmt) preparado na variável $stmt */
        $stmt->bind_param("iissi", $idAluno, $idPlano, $dataInicio, $dataFim, $id); /* vincula as variáveis aos ? do SQL na mesma ordem */
        $stmt->execute(); /* executa a consulta */

        header("Location: cadastro.php"); /* redireciona o usuário para cadastro.php */
        exit; /* encerra a execução depois do redirecionamento */
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />

    <title>Editar Matrícula</title>
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
            <li><a href="cadastro.php">Cadastro de Matrículas</a></li>
        </ul>
    </nav>

    <?php if (empty($erro)): ?>
        <form method="POST">
            <div class="card">
                <h2>Editar Matrícula</h2>

                <div class="linha">
                    <div class="coluna">
                        <div class="form-group">
                            <label>Aluno</label>
                            <select name="id_aluno_fk" required>
                                <?php while ($aluno = $resultado_alunos->fetch_assoc()) { ?>
                                    <option value="<?php echo $aluno["ID_ALUNO"]; ?>"
                                        <?php if ($aluno["ID_ALUNO"] == $linha["ID_ALUNO_FK"]) echo "selected"; ?>>
                                        <?php echo htmlspecialchars($aluno["NOME_ALUNO"]); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Plano</label>
                            <select name="id_plano_fk" required>
                                <?php while ($plano = $resultado_planos->fetch_assoc()) { ?>
                                    <option value="<?php echo $plano["ID_PLANO"]; ?>"
                                        <?php if ($plano["ID_PLANO"] == $linha["ID_PLANO_FK"]) echo "selected"; ?>>
                                        <?php echo htmlspecialchars($plano["NOME_PLANO"]); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="coluna">
                        <div class="form-group">
                            <label>Data de início</label>
                            <input type="date" name="data_inicio" value="<?php echo htmlspecialchars($linha["DATA_INICIO"]); ?>" required>
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