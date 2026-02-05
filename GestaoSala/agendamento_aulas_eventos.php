<?php
require_once '../conexao.php';

// Verifica se o parâmetro 'sala_codigo' está presente na URL
if (isset($_GET['sala_codigo'])) {
    // Atribui o valor do parâmetro 'sala_codigo' a uma variável
    $sala_codigo = $_GET['sala_codigo'];
    
    // Consulta o nome da sala com base no código da sala
    $sql_sala_nome = "SELECT sala_nome FROM sala WHERE sala_codigo = ?";
    $stmt = $conn->prepare($sql_sala_nome);
    $stmt->bind_param("i", $sala_codigo); // "i" indica que $sala_codigo é um inteiro
    $stmt->execute();
    $result_sala_nome = $stmt->get_result();
    
    // Verifica se a consulta retornou algum resultado
    if ($result_sala_nome->num_rows > 0) {
        // Obtém o nome da sala
        $row = $result_sala_nome->fetch_assoc();
        $sala_nome = $row['sala_nome'];
    } else {
        // Se não houver resultado, define o nome da sala como vazio
        $sala_nome = '';
    }
} else {
    // Se o parâmetro 'sala_codigo' não estiver presente, redireciona para a página anterior
    header("Location: consultar_sala.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_evento = $_POST['data_evento'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_termino = $_POST['hora_termino'];
    $finalidade_evento = $_POST['finalidade_evento'];
    $professor_codigo = isset($_POST['professor_codigo']) ? $_POST['professor_codigo'] : null;

    // Insere os dados na tabela de agendamento de aulas
    $sql = "INSERT INTO agendamento_aula (fk_sala_sala_codigo, agendamento_aula_data_da_aula, agendamento_aula_hora_de_inicio, agendamento_aula_hora_de_termino, fk_professor_professor_codigo) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $sala_codigo, $data_evento, $hora_inicio, $hora_termino, $professor_codigo);
    
    if ($stmt->execute()) {
        echo "Aula agendada com sucesso.";
    } else {
        echo "Erro ao agendar a aula: " . $stmt->error;
    }
}

$sql_professores = "SELECT professor_codigo, professor_nome FROM professor";
$result_professores = $conn->query($sql_professores);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Aulas/Eventos</title>
    
    <style>
        body {
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(113,20,244,1) 0%, rgba(141,126,219,1) 0%, rgba(138,126,219,1) 82%);
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        
        .container {
            width: 40%;
            margin: 0 auto;
            background-color: #247D9E;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: white;
        }
        
        form {
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #cccccc;
        }
        
        select,
        input[type="date"],
        input[type="time"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 4px;
            background-color: #3d5275;
            color: #fff;
        }
        
        input[type="submit"] {
            background-color: #3d5275;
            cursor: pointer;
        }
        
        input[type="submit"]:hover {
            background-color: #666666;
        }
        
        a {
            text-decoration: none;
            font-weight: bold;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Agendamento de Aulas/Eventos</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <form method="post" action="">
        <label for="sala_codigo">Sala:</label>
        <div><?php echo $sala_nome; ?></div>

            <br><br>
            <label for="data_evento">Data do Evento:</label>
            <input type="date" name="data_evento" required>
            <br><br>
            <label for="hora_inicio">Hora de Início:</label>
            <input type="time" name="hora_inicio" required>
            <br><br>
            <label for="hora_termino">Hora de Término:</label>
            <input type="time" name="hora_termino" required>
            <br><br>
            <label for="finalidade_evento">Finalidade do Evento:</label>
            <textarea name="finalidade_evento" rows="4" cols="50"></textarea>
            <br><br>
            <label for="professor_codigo">Professor Responsável:</label>
            <select name="professor_codigo">
                <option value="">Selecione...</option>
                <?php
                // Exibe as opções de professor disponíveis
                if ($result_professores->num_rows > 0) {
                    while($row = $result_professores->fetch_assoc()) {
                        echo "<option value='".$row["professor_codigo"]."'>".$row["professor_nome"]."</option>";
                    }
                }
                ?>
            </select>
            <br><br>
            <input type="submit" value="Agendar">
        </form>
    </div>
</body>
</html>

<?php
// Fechar conexão com o banco de dados
$conn->close();
?>
