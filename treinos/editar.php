
<?php
require_once "../conexao.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID inválido.");
}

// Busca o treino
$sql = "SELECT * FROM TREINO WHERE ID_TREINO = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$treino = $stmt->get_result()->fetch_assoc();

if (!$treino) {
    die("Treino não encontrado.");
}

// Processa a edição
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $aluno = (int) $_POST["aluno"];
    $professor = (int) $_POST["professor"];
    $nome = trim($_POST["nome_treino"]);
    $observacoes = trim($_POST["observacoes"] ?? "");

    $ids = $_POST["exercicio"] ?? [];
    $series = $_POST["series"] ?? [];
    $repeticoes = $_POST["repeticoes"] ?? [];
    $cargas = $_POST["carga"] ?? [];

    if (
        $aluno <= 0 ||
        $professor <= 0 ||
        $nome == "" ||
        empty($ids)
    ) {
        $erro = "Preencha os dados e adicione pelo menos um exercício.";
    }

    // Verifica aluno e professor
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
            "SELECT ID_PROFESSOR FROM PROFESSOR WHERE ID_PROFESSOR = ?"
        );
        $stmt->bind_param("i", $professor);
        $stmt->execute();

        if ($stmt->get_result()->num_rows == 0) {
            $erro = "Professor inválido.";
        }
    }

    // Valida os exercícios
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
                $erro = "Confira os exercícios e suas informações.";
                break;
            }

            $idsValidos[$idExercicio] = true;

            $stmt = $conexao->prepare(
                "SELECT ID_EXERCICIOS FROM EXERCICIOS
                 WHERE ID_EXERCICIOS = ?"
            );
            $stmt->bind_param("i", $idExercicio);
            $stmt->execute();

            if ($stmt->get_result()->num_rows == 0) {
                $erro = "Exercício não encontrado.";
                break;
            }
        }
    }

    if ($erro == "") {

        try {

            $conexao->begin_transaction();

            // Atualiza os dados principais do treino
            $sql = "UPDATE TREINO SET
                ID_ALUNO_FK = ?,
                ID_PROFESSOR_FK = ?,
                NOME_TREINO = ?,
                OBSERVACOES = ?
                WHERE ID_TREINO = ?";

            $stmt = $conexao->prepare($sql);

            $stmt->bind_param(
                "iissi",
                $aluno,
                $professor,
                $nome,
                $observacoes,
                $id
            );

            $stmt->execute();

            // Remove os exercícios antigos do treino
            $sql = "DELETE FROM TREINO_EXERCICIO
                    WHERE ID_TREINO_FK = ?";

            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Insere novamente os exercícios atualizados
            $sql = "INSERT INTO TREINO_EXERCICIO
                (ID_TREINO_FK, ID_EXERCICIOS_FK, ORDEM_EXERCICIO,
                 SERIES, REPETICOES, CARGA)
                VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conexao->prepare($sql);

            foreach ($ids as $i => $idExercicio) {

                $idExercicio = (int) $idExercicio;
                $ordem = $i + 1;
                $serie = (int) $series[$i];
                $repeticao = trim($repeticoes[$i]);
                $carga = (float) $cargas[$i];

                $stmt->bind_param(
                    "iiiisd",
                    $id,
                    $idExercicio,
                    $ordem,
                    $serie,
                    $repeticao,
                    $carga
                );

                $stmt->execute();
            }

            $conexao->commit();

            header("Location: cadastro.php");
            exit;

        } catch (Throwable $e) {

            $conexao->rollback();
            $erro = "Erro ao atualizar o treino. Confira os dados.";
        }
    }
}

// Busca novamente os dados atualizados do treino
$stmt = $conexao->prepare("SELECT * FROM TREINO WHERE ID_TREINO = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$treino = $stmt->get_result()->fetch_assoc();

// Busca alunos, professores e exercícios
$alunos = $conexao->query(
    "SELECT ID_ALUNO, NOME_ALUNO FROM ALUNO ORDER BY NOME_ALUNO"
);

$professores = $conexao->query(
    "SELECT ID_PROFESSOR, NOME_PROFESSOR FROM PROFESSOR ORDER BY NOME_PROFESSOR"
);

$opcoes = $conexao->query(
    "SELECT ID_EXERCICIOS, NOME_EXERCICIO, GRUPO_MUSCULAR
     FROM EXERCICIOS ORDER BY NOME_EXERCICIO"
);

// Busca os exercícios atuais do treino
$sql = "SELECT * FROM TREINO_EXERCICIO
        WHERE ID_TREINO_FK = ?
        ORDER BY ORDEM_EXERCICIO";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$atuais = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link href="../css/cadastro-css.css" rel="stylesheet">
    <title>Editar Treino</title>
</head>
<body>

<nav>
    <div class="nav_header">
        <div class="nav_logo">
            <a href="../index.php">
                <img src="../assets/gym.png" alt="Logo" class="gym-logo">
            </a>
        </div>
    </div>

    <ul class="nav_link">
        <li><a href="../index.php">Home</a></li>
        <li><a href="cadastro.php">Treinos</a></li>
    </ul>
</nav>

<?php if ($erro != ""): ?>
    <p><?php echo htmlspecialchars($erro); ?></p>
<?php endif; ?>

<form method="POST">

<div class="card">
<div class="linha">

    <div class="coluna">
        <div class="form-group">
            <label>Nome do treino</label>
            <input type="text" name="nome_treino"
                value="<?php echo htmlspecialchars($treino["NOME_TREINO"]); ?>"
                required>
        </div>
    </div>

    <div class="coluna">
        <div class="form-group">
            <label>Aluno</label>
            <select name="aluno" required>

                <?php while ($aluno = $alunos->fetch_assoc()): ?>

                    <option value="<?php echo $aluno["ID_ALUNO"]; ?>"
                        <?php echo $treino["ID_ALUNO_FK"] == $aluno["ID_ALUNO"] ? "selected" : ""; ?>>

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

                <?php while ($professor = $professores->fetch_assoc()): ?>

                    <option value="<?php echo $professor["ID_PROFESSOR"]; ?>"
                        <?php echo $treino["ID_PROFESSOR_FK"] == $professor["ID_PROFESSOR"] ? "selected" : ""; ?>>

                        <?php echo htmlspecialchars($professor["NOME_PROFESSOR"]); ?>

                    </option>

                <?php endwhile; ?>

            </select>
        </div>
    </div>

    <div class="coluna">
        <div class="form-group">
            <label>Observações</label>
            <input type="text" name="observacoes"
                value="<?php echo htmlspecialchars($treino["OBSERVACOES"] ?? ""); ?>">
        </div>
    </div>

</div>
</div>

<div id="lista-exercicios">

<?php foreach ($atuais as $atual): ?>

<div class="exercicio-item">

    <div class="form-group">
        <label>Exercício</label>

        <select name="exercicio[]" required>

            <?php
            $opcoes->data_seek(0);

            while ($opcao = $opcoes->fetch_assoc()):
            ?>

                <option value="<?php echo $opcao["ID_EXERCICIOS"]; ?>"
                    <?php echo $atual["ID_EXERCICIOS_FK"] == $opcao["ID_EXERCICIOS"] ? "selected" : ""; ?>>

                    <?php echo htmlspecialchars($opcao["NOME_EXERCICIO"]); ?>

                </option>

            <?php endwhile; ?>

        </select>
    </div>

    <div class="form-group">
        <label>Séries</label>
        <input type="number" name="series[]" min="1"
            value="<?php echo $atual["SERIES"]; ?>" required>
    </div>

    <div class="form-group">
        <label>Repetições</label>
        <input type="text" name="repeticoes[]" maxlength="30"
            value="<?php echo htmlspecialchars($atual["REPETICOES"]); ?>" required>
    </div>

    <div class="form-group">
        <label>Carga (kg)</label>
        <input type="number" name="carga[]" min="0" step="0.01"
            value="<?php echo $atual["CARGA"]; ?>" required>
    </div>

    <button type="button" onclick="removerExercicio(this)">
        Remover
    </button>

    <hr>

</div>

<?php endforeach; ?>

</div>

<button type="button" onclick="adicionarExercicio()">
    + Adicionar exercício
</button>

<br><br>

<input type="submit" value="Salvar alterações">

</form>

<script>

function adicionarExercicio() {

    const lista = document.getElementById("lista-exercicios");

    const primeiro = lista.querySelector(".exercicio-item");
    const novo = primeiro.cloneNode(true);

    novo.querySelectorAll("input").forEach(function(campo) {
        campo.value = "";
    });

    novo.querySelector("select").selectedIndex = 0;

    lista.appendChild(novo);
}

function removerExercicio(botao) {

    const lista = document.getElementById("lista-exercicios");

    if (lista.querySelectorAll(".exercicio-item").length > 1) {
        botao.closest(".exercicio-item").remove();
    } else {
        alert("O treino precisa ter pelo menos um exercício.");
    }
}

</script>

</body>
</html>
