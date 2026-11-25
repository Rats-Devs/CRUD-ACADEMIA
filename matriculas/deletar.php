
<?php

require_once "../conexao.php"; /* conexão com o banco de dados */

$id = $_GET["id"] ?? null;

if (!filter_var($id, FILTER_VALIDATE_INT) || $id < 1) { /* ! = NÃO; || = OU */
    /*
        se o id NÃO for um inteiro válido
        OU se o id for menor que 1,
        a validação vai falhar
    */
    echo "ID inválido."; /* mensagem de erro */
    exit;
}

/* verifica se a matrícula existe antes de tentar excluir */
$sql = "SELECT ID_MATRICULA FROM MATRICULA WHERE ID_MATRICULA = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$linha = $resultado->fetch_assoc();

if (empty($linha)) {
    echo "Matrícula não encontrada.";
    exit;
}

/* apaga da tabela MATRICULA o registro cujo ID_MATRICULA seja igual ao valor colocado no ? */
$sql = "DELETE FROM MATRICULA WHERE ID_MATRICULA = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: cadastro.php");
exit;

?>