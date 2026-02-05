<?php
// Incluir arquivo de conexão com o banco de dados
include '../conexao.php';

// Função para limpar e validar dados de entrada
function validarDados($conn, $dados) {
    return htmlspecialchars(strip_tags(mysqli_real_escape_string($conn, $dados)));
}

// Verifica se foi passado um ID de professor válido pela URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID inválido.";
    exit;
}

$professor_id = validarDados($conn, $_GET['id']);

// Verifica se o parâmetro delete_id está presente na URL para excluir um vínculo específico
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = validarDados($conn, $_GET['delete_id']);

    // Query para deletar o vínculo específico
    $sql_delete = "DELETE FROM professor_capacidade WHERE fk_professor_professor_codigo = ? AND fk_disciplina_disciplina_codigo = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $professor_id, $delete_id);
    if ($stmt_delete->execute()) {
        // Redirecionamento após 2 segundos
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'read.php?id=$professor_id';
                }, 2000);
              </script>";
    } else {
        echo "Erro ao excluir o vínculo: " . $conn->error;
    }
}

// Query para selecionar as disciplinas vinculadas ao professor
$sql_vinculos = "SELECT p.professor_nome, d.disciplina_nome, pc.fk_disciplina_disciplina_codigo
                FROM professor_capacidade pc
                INNER JOIN professor p ON p.professor_codigo = pc.fk_professor_professor_codigo
                INNER JOIN disciplina d ON d.disciplina_codigo = pc.fk_disciplina_disciplina_codigo
                WHERE pc.fk_professor_professor_codigo = ?";
$stmt = $conn->prepare($sql_vinculos);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vínculos do Professor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .delete-link img {
            width: 35px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Professor Capacidade</h2>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <span><strong>Disciplina:</strong> <?php echo htmlspecialchars($row['disciplina_nome']); ?></span>
                        <a href="?id=<?php echo $professor_id; ?>&delete_id=<?php echo $row['fk_disciplina_disciplina_codigo']; ?>" class="delete-link" onclick="return confirm('Tem certeza de que deseja excluir este vínculo?')">
                            <img src="../icons/delete_FILL0_wght400_GRAD0_opsz24.svg" alt="Excluir" title="Excluir">
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Nenhum vínculo encontrado para este professor.</p>
        <?php endif; ?>
        <a href="read.php" style="display: block; text-align: center; margin-top: 20px;">Voltar para a página anterior</a>
    </div>
</body>
</html>

<?php
// Fechar conexão com o banco de dados
$conn->close();
?>
