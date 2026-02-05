<?php
session_start();
require_once '../conexao.php'; 

// Verifica se o ID do curso foi passado na URL
if(isset($_GET['id'])) {
    $curso_id = $_GET['id'];
    
    // Consulta SQL para obter as informações do curso com base no ID
    $sql_curso = "SELECT * FROM curso WHERE curso_codigo = '$curso_id'";
    $result_curso = $conn->query($sql_curso);
    
    // Verifica se o curso foi encontrado
    if($result_curso->num_rows > 0) {
        // Recupera os dados do curso
        $curso = $result_curso->fetch_assoc();
    } else {
        // Se o curso não for encontrado, redireciona de volta para a página de consulta de curso
        header("Location: consultar.curso.php");
        exit();
    }
} else {
    // Se nenhum ID de curso foi passado, redireciona de volta para a página de consulta de curso
    header("Location: consultar.curso.php");
    exit();
}

// Processar atualização do curso
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $curso_codigo = $_POST['curso_codigo'];
    $curso_nome = $_POST['curso_nome'];
    $curso_situacao = $_POST['curso_situacao'];
    $curso_descricao = $_POST['curso_descricao'];
    
    
    // Atualiza os dados do curso no banco de dados
    $sql = "UPDATE curso SET curso_nome='$curso_nome', curso_situacao='$curso_situacao', curso_descricao='$curso_descricao' WHERE curso_codigo='$curso_codigo'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Curso atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar o curso: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Atualizar Curso</title>
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
        <h2>Atualizar Curso</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a>
        <a href='consultar.curso.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../icons/search_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>Pesquisar</a>
        <form method="post" action="">
            <!-- Campo oculto para armazenar o ID do curso -->
            <input type="hidden" name="curso_codigo" value="<?php echo $curso['curso_codigo']; ?>">
            <label for="curso_nome">Nome do Curso:</label>
            <input type="text" id="curso_nome" name="curso_nome" value="<?php echo $curso['curso_nome']; ?>" required>
            
            <label for="curso_situacao">Situação do Curso:</label>
            <select id="curso_situacao" name="curso_situacao">
                <option value="Ativo" <?php if($curso['curso_situacao'] == 'Ativo') echo 'selected'; ?>>Ativo</option>
                <option value="Inativo" <?php if($curso['curso_situacao'] == 'Inativo') echo 'selected'; ?>>Inativo</option>
            </select>
            
            <label for="curso_descricao">Descrição do Curso:</label>
            <textarea id="curso_descricao" name="curso_descricao" rows="4" cols="50" required><?php echo $curso['curso_descricao']; ?></textarea>            
                        
            <input type="submit" value="Atualizar Curso">
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
