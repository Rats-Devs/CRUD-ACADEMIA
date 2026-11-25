
<?php
require_once "../conexao.php";

$erro = "";

// Busca alunos
$alunos = $conexao->query(
    "SELECT ID_ALUNO, NOME_ALUNO
     FROM ALUNO
     ORDER BY NOME_ALUNO"
);

// Busca professores
$professores = $conexao->query(
    "SELECT ID_PROFESSOR, NOME_PROFESSOR
     FROM PROFESSOR
     ORDER BY NOME_PROFESSOR"
);

// Busca exercícios
$exercicios = $conexao->query(
    "SELECT ID_EXERCICIOS, NOME_EXERCICIO, GRUPO_MUSCULAR
     FROM EXERCICIOS
     ORDER BY NOME_EXERCICIO"
);

// Processa o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $aluno = (int) ($_POST["aluno"] ?? 0);
    $professor = (int) ($_POST["professor"] ?? 0);
    $nome = trim($_POST["nome_treino"] ?? "");
    $observacoes = trim($_POST["observacoes"] ?? "");

    $ids = $_POST["exercicio"] ?? [];
    $series = $_POST["series"] ?? [];
    $repeticoes = $_POST["repeticoes"] ?? [];
    $cargas = $_POST["carga"] ?? [];

    // Validação inicial
    if (
        $aluno <= 0 ||
        $professor <= 0 ||
        $nome == "" ||
        !is_array($ids) ||
        empty($ids)
    ) {
        $erro = "Preencha os dados e adicione pelo menos um exercício.";
    }

    // Confere se aluno e professor existem
    if ($erro == "") {

        $stmt = $conexao->prepare(
            "SELECT ID_ALUNO FROM ALUNO WHERE ID_ALUNO = ?"
        );

        $stmt->bind_param("i", $aluno);
        $stmt->execute();

        if ($stmt->get_result()->num_rows == 0) {
            $erro = "Aluno inválido.";
        }

        $stmt = $conexao->prepare(
            "SELECT ID_PROFESSOR FROM PROFESSOR
             WHERE ID_PROFESSOR = ?"
        );

        $stmt->bind_param("i", $professor);
        $stmt->execute();

        if ($stmt->get_result()->num_rows == 0) {
            $erro = "Professor inválido.";
        }
    }

    // Confere exercícios e dados
    $idsValidos = [];

    if ($erro == "") {

        foreach ($ids as $i => $idExercicio) {

            $idExercicio = (int) $idExercicio;
            $serie = (int) ($series[$i] ?? 0);
            $repeticao = trim($repeticoes[$i] ?? "");
            $carga = (float) ($cargas[$i] ?? -1);

            if (
                $idExercicio <= 0 ||
                $serie <= 0 ||
                $repeticao == "" ||
                $carga < 0 ||
                isset($idsValidos[$idExercicio])
            ) {
                $erro = "Confira os exercícios, séries, repetições e cargas.";
                break;
            }

            $idsValidos[$idExercicio] = true;

            $stmt = $conexao->prepare(
                "SELECT ID_EXERCICIOS
                 FROM EXERCICIOS
                 WHERE ID_EXERCICIOS = ?"
            );

            $stmt->bind_param("i", $idExercicio);
            $stmt->execute();

            if ($stmt->get_result()->num_rows == 0) {
                $erro = "Um dos exercícios selecionados não existe.";
                break;
            }
        }
    }

    // Cadastra o treino e seus exercícios
    if ($erro == "") {

        try {

            $conexao->begin_transaction();

            // Cadastro do treino
            $sql = "INSERT INTO TREINO
                (ID_ALUNO_FK, ID_PROFESSOR_FK, NOME_TREINO, OBSERVACOES)
                VALUES (?, ?, ?, ?)";

            $stmt = $conexao->prepare($sql);

            $stmt->bind_param(
                "iiss",
                $aluno,
                $professor,
                $nome,
                $observacoes
            );

            $stmt->execute();

            // Recupera ID do treino
            $idTreino = $conexao->insert_id;

            // Cadastro dos exercícios relacionados
            $sql = "INSERT INTO TREINO_EXERCICIO
                (ID_TREINO_FK, ID_EXERCICIOS_FK, ORDEM_EXERCICIO,
                 SERIES, REPETICOES, CARGA)
                VALUES (?, ?, ?, ?, ?, ?)";

            $stmtExercicio = $conexao->prepare($sql);

            foreach ($ids as $i => $idExercicio) {

                $idExercicio = (int) $idExercicio;
                $ordem = $i + 1;
                $serie = (int) $series[$i];
                $repeticao = trim($repeticoes[$i]);
                $carga = (float) $cargas[$i];

                $stmtExercicio->bind_param(
                    "iiiisd",
                    $idTreino,
                    $idExercicio,
                    $ordem,
                    $serie,
                    $repeticao,
                    $carga
                );

                $stmtExercicio->execute();
            }

            $conexao->commit();

            header("Location: cadastro.php");
            exit;

        } catch (Throwable $e) {

            $conexao->rollback();

            $erro = "Não foi possível cadastrar o treino. Verifique os dados.";
        }
    }
}

// Lista os treinos cadastrados
$sql = "SELECT
            T.ID_TREINO,
            T.NOME_TREINO,
            A.NOME_ALUNO,
            P.NOME_PROFESSOR,
            T.DATA_CRIACAO
        FROM TREINO T
        INNER JOIN ALUNO A
            ON T.ID_ALUNO_FK = A.ID_ALUNO
        INNER JOIN PROFESSOR P
            ON T.ID_PROFESSOR_FK = P.ID_PROFESSOR
        ORDER BY T.ID_TREINO DESC";

$treinos = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../css/cadastro-css.css" rel="stylesheet">

    <title>Treinos</title>
</head>

<body>

<!-- Navegação -->
<nav>
    <div class="nav_header">

        <div class="nav_logo">
            <a href="../index.php">
                <img src="../assets/gym.png"
                     alt="Logo"
                     class="gym-logo">
            </a>
        </div>

    </div>

    <ul class="nav_link">
        <li><a href="../index.php">Home</a></li>
        <li><a href="cadastro.php">Treinos</a></li>
    </ul>
</nav>

<!-- Mensagem de erro -->
<?php if ($erro != ""): ?>

    <p>
        <?php echo htmlspecialchars($erro); ?>
    </p>

<?php endif; ?>

<!-- Formulário de cadastro -->
<form method="POST">

    <div class="card">

        <div class="linha">

            <div class="coluna">

                <div class="form-group">
                    <label>Nome do treino</label>

                    <input
                        type="text"
                        name="nome_treino"
                        maxlength="50"
                        required
                    >
                </div>

            </div>

            <div class="coluna">

                <div class="form-group">
                    <label>Aluno</label>

                    <select name="aluno" required>

                        <option value="">
                            Selecione um aluno
                        </option>

                        <?php while ($aluno = $alunos->fetch_assoc()): ?>

                            <option value="<?php echo $aluno["ID_ALUNO"]; ?>">
                                <?php echo htmlspecialchars($aluno["NOME_ALUNO"]); ?>
                            </option>

                        <?php endwhile; ?>

                    </select>
                </div>

            </div>

            <div class="coluna">

                <div class="form-group">
                    <label>Professor</label>

                    <select name="professor" required>

                        <option value="">
                            Selecione um professor
                        </option>

                        <?php while ($professor = $professores->fetch_assoc()): ?>

                            <option value="<?php echo $professor["ID_PROFESSOR"]; ?>">
                                <?php echo htmlspecialchars($professor["NOME_PROFESSOR"]); ?>
                            </option>

                        <?php endwhile; ?>

                    </select>
                </div>

            </div>

            <div class="coluna">

                <div class="form-group">
                    <label>Observações</label>

                    <input
                        type="text"
                        name="observacoes"
                        maxlength="255"
                    >
                </div>

            </div>

        </div>

    </div>

    <!-- Lista dinâmica de exercícios -->
    <div id="lista-exercicios">

        <div class="exercicio-item">

            <div class="form-group">

                <label>Exercício</label>

                <select name="exercicio[]" required>

                    <option value="">
                        Selecione um exercício
                    </option>

                    <?php
                    $opcoes = $conexao->query(
                        "SELECT ID_EXERCICIOS, NOME_EXERCICIO, GRUPO_MUSCULAR
                         FROM EXERCICIOS
                         ORDER BY NOME_EXERCICIO"
                    );

                    while ($exercicio = $opcoes->fetch_assoc()):
                    ?>

                        <option value="<?php echo $exercicio["ID_EXERCICIOS"]; ?>">

                            <?php
                            echo htmlspecialchars(
                                $exercicio["NOME_EXERCICIO"] .
                                " - " .
                                $exercicio["GRUPO_MUSCULAR"]
                            );
                            ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>

            <div class="form-group">

                <label>Séries</label>

                <input
                    type="number"
                    name="series[]"
                    min="1"
                    required
                >

            </div>

            <div class="form-group">

                <label>Repetições</label>

                <input
                    type="text"
                    name="repeticoes[]"
                    maxlength="30"
                    required
                >

            </div>

            <div class="form-group">

                <label>Carga (kg)</label>

                <input
                    type="number"
                    name="carga[]"
                    min="0"
                    step="0.01"
                    required
                >

            </div>

            <button
                type="button"
                onclick="removerExercicio(this)"
            >
                Remover exercício
            </button>

            <hr>

        </div>

    </div>

    <button type="button" onclick="adicionarExercicio()">
        + Adicionar exercício
    </button>

    <br><br>

    <input type="submit" value="Cadastrar treino">

</form>

<!-- Tabela de treinos cadastrados -->
<h2>Treinos cadastrados</h2>

<div class="tabela-wrapper">

    <table class="table-content">

        <thead>

            <tr class="table-header-row">

                <th class="table-th">ID</th>
                <th class="table-th">Treino</th>
                <th class="table-th">Aluno</th>
                <th class="table-th">Professor</th>
                <th class="table-th">Data</th>
                <th class="table-th">Ações</th>

            </tr>

        </thead>

        <tbody>

            <?php while ($treino = $treinos->fetch_assoc()): ?>

                <tr class="table-data-row">

                    <td class="table-td">
                        <?php echo $treino["ID_TREINO"]; ?>
                    </td>

                    <td class="table-td">
                        <?php echo htmlspecialchars($treino["NOME_TREINO"]); ?>
                    </td>

                    <td class="table-td">
                        <?php echo htmlspecialchars($treino["NOME_ALUNO"]); ?>
                    </td>

                    <td class="table-td">
                        <?php echo htmlspecialchars($treino["NOME_PROFESSOR"]); ?>
                    </td>

                    <td class="table-td">
                        <?php echo htmlspecialchars($treino["DATA_CRIACAO"]); ?>
                    </td>

                    <!-- Ações organizadas -->
                    <td class="table-td">

                        <div class="acoes-treino">

                            <a href="visualizar.php?id=<?php echo $treino["ID_TREINO"]; ?>">
                                Visualizar
                            </a>

                            <a href="editar.php?id=<?php echo $treino["ID_TREINO"]; ?>">
                                Editar
                            </a>

                            <form
                                action="deletar.php"
                                method="POST"
                                onsubmit="return confirm('Excluir este treino?')"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?php echo $treino["ID_TREINO"]; ?>"
                                >

                                <button type="submit">
                                    Excluir
                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            <?php endwhile; ?>

        </tbody>

    </table>

</div>

<!-- JavaScript -->
<script>

// Adiciona um novo exercício
function adicionarExercicio() {

    const lista = document.getElementById("lista-exercicios");

    const primeiro = lista.querySelector(".exercicio-item");

    const novo = primeiro.cloneNode(true);

    // Limpa os campos
    novo.querySelectorAll("input").forEach(function(campo) {
        campo.value = "";
    });

    novo.querySelector("select").selectedIndex = 0;

    lista.appendChild(novo);
}

// Remove um exercício
function removerExercicio(botao) {

    const lista = document.getElementById("lista-exercicios");

    const exercicios = lista.querySelectorAll(".exercicio-item");

    if (exercicios.length > 1) {

        botao.closest(".exercicio-item").remove();

    } else {

        alert("O treino precisa ter pelo menos um exercício.");

    }
}

</script>

</body>
</html>
