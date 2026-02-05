<?php
// Iniciamos uma sessão para gerenciar informações do usuário
session_start(); 
require_once '../conexao.php'; // Certifique-se de incluir o arquivo de conexão

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $curso_nome = $_POST['curso_nome'];
    $curso_situacao = $_POST['curso_situacao'];
    $curso_descricao = $_POST['curso_descricao'];
    
    // Insere os dados na tabela 'curso'
    $sql = "INSERT INTO curso (curso_nome, curso_situacao, curso_descricao) VALUES ('$curso_nome', '$curso_situacao', '$curso_descricao')";
    
    if ($conn->query($sql) === TRUE) {
        // Define uma mensagem de sucesso para ser exibida após o redirecionamento
        $_SESSION['success_message'] = "Curso cadastrado com sucesso!";
        // Redireciona após 2 segundos
        header("refresh:2; url=../pag-inicial.html");
    } else {
        echo "Erro ao cadastrar o curso: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Curso</title>
    <style>
        body {
            background: rgb(238,174,202);
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
        <h2>Cadastro de Curso</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <form method="post" action="">
           
            <label for="curso_nome">Nome do Curso:</label><br>
            <input type="text" id="curso_nome" name="curso_nome" required><br><br>
            
            <label for="curso_situacao">Situação do Curso:</label><br>
            <select id="curso_situacao" name="curso_situacao">
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
            </select><br><br>
            
            <label for="curso_descricao">Descrição do Curso:</label><br>
            <textarea id="curso_descricao" name="curso_descricao" rows="4" cols="50" required></textarea><br><br>           
            
            <input type="submit" value="Cadastrar Curso">           
            
        </form>
        
        <?php
        // Verifica se existe uma mensagem de sucesso para exibir
        if(isset($_SESSION['success_message'])) {
            echo "<p>{$_SESSION['success_message']}</p>";
            unset($_SESSION['success_message']); // Limpa a mensagem após exibi-la
        }
        ?>
                 
    </div>
</body>
</html>

<?php
// Fecha a conexão
$conn->close();
?>
