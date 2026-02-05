<?php
// Iniciamos uma sessão para gerenciar informações do usuário
session_start(); 
require_once '../conexao.php'; // Certifique-se de incluir o arquivo de conexão

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $disciplina_nome = $_POST['disciplina_nome'];
    $disciplina_carga_horaria = $_POST['disciplina_carga_horaria'];
    $disciplina_transversal = $_POST['disciplina_transversal'];
    $disciplina_ementa = $_POST['disciplina_ementa'];
    $disciplina_bibliografia = $_POST['disciplina_bibliografia'];
    
    // Insere os dados na tabela 'disciplina'
    $sql = "INSERT INTO disciplina (disciplina_nome, disciplina_carga_horaria, disciplina_transversal, disciplina_ementa, disciplina_bibliografia) VALUES ('$disciplina_nome', '$disciplina_carga_horaria', '$disciplina_transversal', '$disciplina_ementa', '$disciplina_bibliografia')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Disciplina cadastrada com sucesso!";
    } else {
        echo "Erro ao cadastrar a disciplina: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Disciplina</title>
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
        label {
            display: block;
            margin-bottom: 5px;
            color: #cccccc;
        }
        input[type="text"], input[type="number"], textarea, select, input[type="submit"] {
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Disciplina</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <form method="post" action="">
           
            <label for="disciplina_nome">Nome da Disciplina:</label><br>
            <input type="text" id="disciplina_nome" name="disciplina_nome" required><br><br>
            
            <label for="disciplina_carga_horaria">Carga Horária:</label><br>
            <input type="number" id="disciplina_carga_horaria" name="disciplina_carga_horaria" required><br><br>
            
            <label for="disciplina_transversal">Transversal (Sim/Não):</label><br>
            <select id="disciplina_transversal" name="disciplina_transversal">
                <option value="Sim">Sim</option>
                <option value="Não">Não</option>
            </select><br><br>
            
            <label for="disciplina_ementa">Ementa:</label><br>
            <textarea id="disciplina_ementa" name="disciplina_ementa" rows="4" cols="50"></textarea><br><br>
            
            <label for="disciplina_bibliografia">Bibliografia:</label><br>
            <input type="text" id="disciplina_bibliografia" name="disciplina_bibliografia" required><br><br>
            
            <input type="submit" value="Cadastrar Disciplina">
            
        </form>
    </div>
</body>
</html>

<?php
// Fecha a conexão
$conn->close();
?>
