<?php
session_start();
require_once '../conexao.php'; 

// Verifica se o ID do aluno foi fornecido na URL
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta o aluno com base no ID fornecido
    $sql_aluno = "SELECT * FROM aluno WHERE aluno_codigo = '$id'";
    $result_aluno = $conn->query($sql_aluno);

    // Verifica se o aluno foi encontrado
    if ($result_aluno->num_rows == 1) {
        $aluno = $result_aluno->fetch_assoc();
    } else {
        // Redireciona de volta para a página de consulta de alunos se o aluno não for encontrado
        header("Location: consulta_alunos.php");
        exit();
    }
} else {
    // Redireciona de volta para a página de consulta de alunos se o ID do aluno não for fornecido
    header("Location: consulta_alunos.php");
    exit();
}

// Verifica se o formulário de edição foi submetido
if(isset($_POST['editar_aluno'])) {
    // Recupera os dados do formulário
    $nome = $_POST['aluno_nome'];
    $sexo = $_POST['aluno_sexo'];
    $idade = $_POST['aluno_idade'];
    $email = $_POST['aluno_email'];
    $telefone = $_POST['aluno_telefone'];
    $rg = $_POST['aluno_rg'];
    $cpf = $_POST['aluno_cpf'];
    $data_nascimento = $_POST['aluno_dt_nascimento'];
    $endereco = $_POST['aluno_endereco'];

    // Verifica se o formulário de edição foi submetido
if(isset($_POST['editar_aluno'])) {
    // Recupera os dados do formulário
    $nome = $_POST['aluno_nome'];
    $sexo = $_POST['aluno_sexo'];
    $idade = $_POST['aluno_idade'];
    $email = $_POST['aluno_email'];
    $telefone = $_POST['aluno_telefone'];
    $rg = $_POST['aluno_rg'];
    $cpf = $_POST['aluno_cpf'];
    $data_nascimento = $_POST['aluno_dt_nascimento'];
    $endereco = $_POST['aluno_endereco'];

    // Verifica se uma nova foto foi enviada
    if(isset($_FILES['aluno_foto']) && $_FILES['aluno_foto']['size'] > 0) {
        // Remove a foto antiga
        unlink($aluno['aluno_foto_path']);
        
        // Move a nova foto para o diretório de destino
        $arquivo = $_FILES['aluno_foto'];
        $pasta = "../arquivos/";
        $novoNomeDoArquivo = uniqid();
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $novoCaminho = $pasta . $novoNomeDoArquivo . "." . $extensao;
        move_uploaded_file($arquivo["tmp_name"], $novoCaminho);

        // Atualiza o nome e o caminho da foto no banco de dados
        $sql_update_foto = "UPDATE aluno SET aluno_foto_nome = '$arquivo[name]', aluno_foto_path = '$novoCaminho', aluno_foto_data_upload = NOW() WHERE aluno_codigo = '$id'";
        $conn->query($sql_update_foto);
    }

    // Atualiza os dados do aluno no banco de dados
    $sql_update_aluno = "UPDATE aluno SET aluno_nome = '$nome', aluno_sexo = '$sexo', aluno_idade = '$idade', aluno_email = '$email', aluno_telefone = '$telefone', aluno_rg = '$rg', aluno_cpf = '$cpf', aluno_dt_nascimento = '$data_nascimento', aluno_endereco = '$endereco' WHERE aluno_codigo = '$id'";
    if($conn->query($sql_update_aluno) === TRUE) {
        // Redireciona de volta para a página de consulta de alunos após a edição
        header("Location: consulta_alunos.php");
        exit();
    } else {
        echo "Erro ao editar aluno: " . $conn->error;
    }
}

}


$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Aluno</title>
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
        input[type="text"], input[type="number"], textarea, select, input[type="submit"], input[type="file"] {
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
        <h2>Editar Aluno</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a>
        <a href='consulta_alunos.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../icons/search_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>Pesquisar</a>
        <br><br>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="aluno_nome">Nome:</label><br>
            <input type="text" id="aluno_nome" name="aluno_nome" value="<?php echo $aluno['aluno_nome']; ?>" required><br><br>

            <label for="aluno_sexo">Sexo:</label><br>
            <select id="aluno_sexo" name="aluno_sexo">
                <option value="Masculino" <?php if($aluno['aluno_sexo'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
                <option value="Feminino" <?php if($aluno['aluno_sexo'] == 'Feminino') echo 'selected'; ?>>Feminino</option>
            </select><br><br>

            <label for="aluno_idade">Idade:</label><br>
            <input type="number" id="aluno_idade" name="aluno_idade" value="<?php echo $aluno['aluno_idade']; ?>" required><br><br>

            <label for="aluno_email">E-mail:</label><br>
            <input type="email" id="aluno_email" name="aluno_email" value="<?php echo $aluno['aluno_email']; ?>" required><br><br>

            <label for="aluno_telefone">Telefone:</label><br>
            <input type="text" id="aluno_telefone" name="aluno_telefone" value="<?php echo $aluno['aluno_telefone']; ?>" required><br><br>

            <label for="aluno_rg">RG:</label><br>
            <input type="text" id="aluno_rg" name="aluno_rg" value="<?php echo $aluno['aluno_rg']; ?>" required><br><br>

            <label for="aluno_cpf">CPF:</label><br>
            <input type="text" id="aluno_cpf" name="aluno_cpf" value="<?php echo $aluno['aluno_cpf']; ?>" required><br><br>
            
            <label for="aluno_dt_nascimento">Data de Nascimento:</label><br>
            <input type="date" id="aluno_dt_nascimento" name="aluno_dt_nascimento" value="<?php echo $aluno['aluno_dt_nascimento']; ?>" required><br><br>

            <label for="aluno_endereco">Endereço:</label><br>
            <input type="text" id="aluno_endereco" name="aluno_endereco" value="<?php echo $aluno['aluno_endereco']; ?>" required><br><br>

            <label for="aluno_foto">Foto:</label><br>
            <input type="file" id="aluno_foto" name="aluno_foto" accept="image/*"><br><br>
        
            <input type="submit" name="editar_aluno" value="Editar Aluno">
        </form>

    </div>
</body>
</html>
