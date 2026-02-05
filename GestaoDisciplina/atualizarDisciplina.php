<?php
session_start();
require_once '../conexao.php'; 

// Verificar se um código de disciplina foi passado via parâmetro GET
if(isset($_GET['id'])) {
    $disciplina_codigo = $_GET['id'];
    
    // Consultar o banco de dados para obter as informações da disciplina
    $sql = "SELECT * FROM disciplina WHERE disciplina_codigo='$disciplina_codigo'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Preencher os campos do formulário com as informações da disciplina
        $row = $result->fetch_assoc();
        $disciplina_nome = $row['disciplina_nome'];
        $disciplina_carga_horaria = $row['disciplina_carga_horaria'];
        $disciplina_transversal = $row['disciplina_transversal'];
        $disciplina_ementa = $row['disciplina_ementa'];
        $disciplina_bibliografia = $row['disciplina_bibliografia'];
    } else {
        echo "Nenhuma disciplina encontrada com o código fornecido.";
        exit(); // Saia do script se não houver disciplina encontrada
    }
} else {
    echo "Código da disciplina não fornecido.";
    exit(); // Saia do script se nenhum código de disciplina foi fornecido
}

// Processar atualização da disciplina
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $disciplina_nome = $_POST['disciplina_nome'];
    $disciplina_carga_horaria = $_POST['disciplina_carga_horaria'];
    $disciplina_transversal = $_POST['disciplina_transversal'];
    $disciplina_ementa = $_POST['disciplina_ementa'];
    $disciplina_bibliografia = $_POST['disciplina_bibliografia'];
    
    // Atualiza os dados da disciplina no banco de dados
    $sql = "UPDATE disciplina SET disciplina_nome='$disciplina_nome', disciplina_carga_horaria='$disciplina_carga_horaria', disciplina_transversal='$disciplina_transversal', disciplina_ementa='$disciplina_ementa', disciplina_bibliografia='$disciplina_bibliografia' WHERE disciplina_codigo='$disciplina_codigo'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Disciplina atualizada com sucesso!";
    } else {
        echo "Erro ao atualizar a disciplina: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Atualizar Disciplina</title>
    <style>
        body {
            background: rgb(238,174,202);
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(113,20,244,1) 0%, rgba(141,126,219,1) 0%, rgba(138,126,219,1) 82%);
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px; /* Adiciona espaço acima do formulário */
            margin: 0;
        }
        .container {
            width: 40%;
            margin: 0 auto; /* Centraliza horizontalmente */
            background-color: #247D9E;
            padding: 20px; /* Adiciona espaço interno ao contêiner */
            border-radius: 10px; /* Adiciona cantos arredondados */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adiciona sombra */
        }
        form {
            text-align: left; /* Alinha o texto do formulário à esquerda */
        }
        label {
            display: block; /* Faz com que as etiquetas ocupem toda a largura do contêiner */
            margin-bottom: 5px; /* Adiciona espaço entre as etiquetas */
            color: #cccccc;
        }
        input[type="text"], input[type="number"], textarea, select, input[type="submit"] {
            width: 100%; /* Faz com que os campos de entrada ocupem toda a largura do contêiner */
            padding: 10px; /* Adiciona espaço interno aos campos de entrada */
            margin-bottom: 10px; /* Adiciona espaço entre os campos de entrada */
            border: none; /* Remove a borda padrão */
            border-radius: 4px; /* Adiciona cantos arredondados */
            background-color: #3d5275; /* Cor de fundo dos campos de entrada */
            color: #fff; /* Cor do texto */
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
        <h2>Atualizar Disciplina</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <form method="post" action="">
            <!-- Campos do formulário preenchidos com as informações da disciplina -->
            <label for="disciplina_codigo">Código da Disciplina:</label>
            <input type="text" id="disciplina_codigo" name="disciplina_codigo" value="<?php echo $disciplina_codigo; ?>" readonly>
            
            <label for="disciplina_nome">Nome da Disciplina:</label>
            <input type="text" id="disciplina_nome" name="disciplina_nome" value="<?php echo $disciplina_nome; ?>" required>
            
            <label for="disciplina_carga_horaria">Carga Horária:</label>
            <input type="number" id="disciplina_carga_horaria" name="disciplina_carga_horaria" value="<?php echo $disciplina_carga_horaria; ?>" required>
            
            <label for="disciplina_transversal">Transversal:</label>
            <input type="text" id="disciplina_transversal" name="disciplina_transversal" value="<?php echo $disciplina_transversal; ?>" required>
            
            <label for="disciplina_ementa">Ementa:</label>
            <textarea id="disciplina_ementa" name="disciplina_ementa" rows="4" required><?php echo $disciplina_ementa; ?></textarea>
            
            <label for="disciplina_bibliografia">Bibliografia:</label>
            <input type="text" id="disciplina_bibliografia" name="disciplina_bibliografia" value="<?php echo $disciplina_bibliografia; ?>" required>
            
            <input type="submit" value="Atualizar Disciplina">            
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>