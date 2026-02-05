<?php
// Incluir arquivo de conexão com o banco de dados
include '../conexao.php';

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se foi passado um ID de professor válido pela URL
    if (!isset($_POST['professorCodigo']) || !is_numeric($_POST['professorCodigo'])) {
        echo "ID de professor inválido.";
        exit;
    }

    // Obtém o código do professor e as disciplinas selecionadas do formulário
    $professor_id = $_POST['professorCodigo'];
    $disciplinas_selecionadas = isset($_POST['disciplinas']) ? $_POST['disciplinas'] : array();

    // Deleta todos os vínculos atuais do professor com as disciplinas
    $sql_delete = "DELETE FROM professor_capacidade WHERE fk_professor_professor_codigo = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $professor_id);
    $stmt_delete->execute();


    // Insere os novos vínculos no banco de dados
    $sql_insert = "INSERT INTO professor_capacidade (fk_professor_professor_codigo, fk_disciplina_disciplina_codigo) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    foreach ($disciplinas_selecionadas as $disciplina_id) {
        $stmt_insert->bind_param("ii", $professor_id, $disciplina_id);
        $stmt_insert->execute();
    }


    // Redireciona de volta para a página de consulta de professores
    header("Location: read.php");
    exit();
}

// Verifica se foi passado um ID de professor válido pela URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID inválido.";
    exit;
}

$professor_id = $_GET['id'];

// Query para selecionar todas as disciplinas
$sql_disciplinas = "SELECT * FROM disciplina";
$result_disciplinas = $conn->query($sql_disciplinas);

// Verifica se houve algum erro na execução da query
if (!$result_disciplinas) {
    echo "Erro ao buscar disciplinas: " . $conn->error;
    exit;
}

// Função para verificar se uma disciplina está vinculada ao professor
function disciplinaEstaVinculada($conn, $professor_id, $disciplina_id) {
    $sql_verifica_vinculo = "SELECT * FROM professor_capacidade WHERE fk_professor_professor_codigo = ? AND fk_disciplina_disciplina_codigo = ?";
    $stmt = $conn->prepare($sql_verifica_vinculo);
    $stmt->bind_param("ii", $professor_id, $disciplina_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Vincular Disciplinas ao Professor</title>
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
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="checkbox"] {
            margin-right: 10px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Vincular Disciplinas ao Professor</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php
            // Exibe todas as disciplinas como checkboxes
            while ($row_disciplina = $result_disciplinas->fetch_assoc()) {
                $disciplina_id = $row_disciplina['disciplina_codigo'];
                $disciplina_nome = $row_disciplina['disciplina_nome'];
                $checked = disciplinaEstaVinculada($conn, $professor_id, $disciplina_id) ? 'checked' : '';
                echo "<label><input type='checkbox' name='disciplinas[]' value='$disciplina_id' $checked> $disciplina_nome</label>";
            }
            ?>
            <input type="hidden" name="professorCodigo" value="<?php echo $professor_id; ?>">
            <button type="submit">Salvar Vínculo</button>
        </form>
    </div>
</body>
</html>

<?php
// Fechar conexão com o banco de dados
$conn->close();
?>
