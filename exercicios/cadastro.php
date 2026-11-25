
<?php

require_once "../conexao.php";

$erro = "";

// Processa o cadastro
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome_exercicio"] ?? "");
    $grupo = trim($_POST["grupo_muscular"] ?? "");
    $descricao = trim($_POST["descricao_exercicio"] ?? "");
    $equipamentos = trim($_POST["equipamentos"] ?? "");

    // Validação dos campos
    if ($nome == "" || $grupo == "") {

        $erro = "Preencha o nome e o grupo muscular.";

    } elseif (
        strlen($nome) > 100 ||
        strlen($grupo) > 50 ||
        strlen($descricao) > 255 ||
        strlen($equipamentos) > 255
    ) {

        $erro = "Um dos campos ultrapassou o limite permitido.";

    } else {

        try {

            $sql = "INSERT INTO EXERCICIOS
                    (
                        NOME_EXERCICIO,
                        GRUPO_MUSCULAR,
                        DESCRICAO_EXERCICIO,
                        EQUIPAMENTOS
                    )
                    VALUES (?, ?, ?, ?)";

            $stmt = $conexao->prepare($sql);

            $stmt->bind_param(
                "ssss",
                $nome,
                $grupo,
                $descricao,
                $equipamentos
            );

            $stmt->execute();

            header("Location: cadastro.php?sucesso=1");
            exit;

        } catch (mysqli_sql_exception $e) {

            $erro = "Não foi possível cadastrar o exercício.";

        }
    }
}


// Lista os exercícios cadastrados
$sql = "SELECT
            ID_EXERCICIOS,
            NOME_EXERCICIO,
            GRUPO_MUSCULAR,
            DESCRICAO_EXERCICIO,
            EQUIPAMENTOS
        FROM EXERCICIOS
        ORDER BY ID_EXERCICIOS DESC";

$exercicios = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <title>Exercícios</title>

</head>

<body>

<!-- Navegação -->

<nav>

    <div class="nav_header">

        <div class="nav_logo">

            <a href="../index.php">

                <img
                    src="../assets/gym.png"
                    alt="Logo"
                    class="gym-logo"
                >

            </a>

        </div>

    </div>

    <ul class="nav_link">

        <li>
            <a href="../index.php">Home</a>
        </li>

        <li>
            <a href="cadastro.php">Exercícios</a>
        </li>

        <li>
            <a href="../treinos/cadastro.php">Treinos</a>
        </li>

    </ul>

</nav>


<!-- Mensagens -->

<?php if ($erro != ""): ?>

    <p class="mensagem-erro">

        <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>

    </p>

<?php endif; ?>


<?php if (isset($_GET["sucesso"]) && $_GET["sucesso"] == "1"): ?>

    <p class="mensagem-vazio">

        Exercício cadastrado com sucesso!

    </p>

<?php endif; ?>


<?php if (isset($_GET["sucesso"]) && $_GET["sucesso"] == "excluido"): ?>

    <p class="mensagem-vazio">

        Exercício excluído com sucesso!

    </p>

<?php endif; ?>


<?php if (isset($_GET["erro"]) && $_GET["erro"] == "exercicio_vinculado"): ?>

    <p class="mensagem-erro">

        Não é possível excluir: este exercício está vinculado a um ou mais treinos.

    </p>

<?php endif; ?>


<?php if (isset($_GET["erro"]) && $_GET["erro"] == "exclusao"): ?>

    <p class="mensagem-erro">

        Ocorreu um erro ao tentar excluir o exercício.

    </p>

<?php endif; ?>


<?php if (isset($_GET["erro"]) && $_GET["erro"] == "id_invalido"): ?>

    <p class="mensagem-erro">

        ID de exercício inválido.

    </p>

<?php endif; ?>


<!-- Formulário de cadastro -->

<form method="POST">

    <div class="card">

        <div class="linha">

            <div class="coluna">

                <div class="form-group">

                    <label>Nome do exercício</label>

                    <input
                        type="text"
                        name="nome_exercicio"
                        maxlength="100"
                        required
                    >

                </div>

            </div>


            <div class="coluna">

                <div class="form-group">

                    <label>Grupo muscular</label>

                    <select name="grupo_muscular" required>

                        <option value="">
                            Selecione um grupo muscular
                        </option>

                        <option value="Peito">Peito</option>

                        <option value="Costas">Costas</option>

                        <option value="Bíceps">Bíceps</option>

                        <option value="Tríceps">Tríceps</option>

                        <option value="Pernas">Pernas</option>

                        <option value="Ombros">Ombros</option>

                        <option value="Abdômen">Abdômen</option>

                        <option value="Panturrilha">Panturrilha</option>

                    </select>

                </div>

            </div>


            <div class="coluna">

                <div class="form-group">

                    <label>Equipamentos</label>

                    <input
                        type="text"
                        name="equipamentos"
                        maxlength="255"
                        placeholder="Ex: Barra, halteres, polia..."
                    >

                </div>

            </div>


            <div class="coluna">

                <div class="form-group">

                    <label>Descrição do exercício</label>

                    <input
                        type="text"
                        name="descricao_exercicio"
                        maxlength="255"
                        placeholder="Descreva o exercício"
                    >

                </div>

            </div>

        </div>

    </div>


    <br>

    <input type="submit" value="Cadastrar exercício">

</form>


<!-- Tabela de exercícios -->

<div class="tabela-wrapper">

    <table class="table-content">

        <thead>

            <tr class="table-header-row">

                <th class="table-th">ID</th>

                <th class="table-th">Exercício</th>

                <th class="table-th">Grupo muscular</th>

                <th class="table-th">Equipamento</th>

                <th class="table-th">Descrição</th>

                <th class="table-th">Ações</th>

            </tr>

        </thead>


        <tbody>

            <?php if ($exercicios->num_rows > 0): ?>

                <?php while ($exercicio = $exercicios->fetch_assoc()): ?>

                    <tr class="table-data-row">

                        <td class="table-td">

                            <?php echo $exercicio["ID_EXERCICIOS"]; ?>

                        </td>


                        <td class="table-td">

                            <?php echo htmlspecialchars($exercicio["NOME_EXERCICIO"], ENT_QUOTES, 'UTF-8'); ?>

                        </td>


                        <td class="table-td">

                            <?php echo htmlspecialchars($exercicio["GRUPO_MUSCULAR"], ENT_QUOTES, 'UTF-8'); ?>

                        </td>


                        <td class="table-td">

                            <?php echo htmlspecialchars($exercicio["EQUIPAMENTOS"] ?? "", ENT_QUOTES, 'UTF-8'); ?>

                        </td>


                        <td class="table-td">

                            <?php echo htmlspecialchars($exercicio["DESCRICAO_EXERCICIO"] ?? "", ENT_QUOTES, 'UTF-8'); ?>

                        </td>


                        <td class="table-td">

                            <div class="acoes-treino">

                                <a href="visualizar.php?id=<?php echo $exercicio["ID_EXERCICIOS"]; ?>">
                                    Visualizar
                                </a>

                                <a href="editar.php?id=<?php echo $exercicio["ID_EXERCICIOS"]; ?>">
                                    Editar
                                </a>

                                <form
                                    action="deletar.php"
                                    method="POST"
                                    onsubmit="return confirm('Excluir este exercício?')"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo $exercicio["ID_EXERCICIOS"]; ?>"
                                    >

                                    <button type="submit">
                                        Excluir
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td colspan="6" class="table-td">
                        Nenhum exercício cadastrado.
                    </td>

                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>


</body>

</html>
