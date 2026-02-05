<?php
session_start();
require_once '../conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os dados do formulário
    $aluno_nome = $_POST['aluno_nome'];
    $aluno_sexo = $_POST['aluno_sexo'];
    $aluno_idade = $_POST['aluno_idade'];
    $aluno_email = $_POST['aluno_email'];
    $aluno_telefone = $_POST['aluno_telefone'];
    $aluno_rg = $_POST['aluno_rg'];
    $aluno_cpf = $_POST['aluno_cpf'];
    $aluno_dt_nascimento = $_POST['aluno_dt_nascimento'];
    $aluno_endereco = $_POST['aluno_endereco'];

    // Verifica se foi enviado um arquivo
    if(isset($_FILES['arquivo'])){
        $arquivo = $_FILES['arquivo'];
    
        if($arquivo['error'])
            die("Falha ao enviar arquivo");
    
        if($arquivo['size'] > 2097152)
            die("Arquivo muito grande!! Max: 2MB");
    
        $pasta = "../arquivos/";
    
        $nomeDoArquivo = $arquivo['name']; // Nome original do arquivo
        $novoNomeDoArquivo = uniqid();
        $extensao = strtolower(pathinfo($nomeDoArquivo, PATHINFO_EXTENSION)); // Extensão do arquivo

        if($extensao != "jpg" && $extensao != 'png')
            die("Tipo de arquivo não aceito");
    
        $path = $pasta . $novoNomeDoArquivo . "." . $extensao; // Caminho onde o arquivo será salvo
        $deu_certo = move_uploaded_file($arquivo["tmp_name"], $path); // Move o arquivo para o diretório de destino
    
        if($deu_certo){
            // Insere os dados do aluno e do arquivo na mesma instrução SQL
            $sql = "INSERT INTO aluno (aluno_nome, aluno_sexo, aluno_idade, aluno_email, aluno_telefone, aluno_rg, aluno_cpf, aluno_dt_nascimento, aluno_endereco, aluno_foto_nome, aluno_foto_path, aluno_foto_data_upload) 
                    VALUES ('$aluno_nome', '$aluno_sexo', '$aluno_idade', '$aluno_email', '$aluno_telefone', '$aluno_rg', '$aluno_cpf', '$aluno_dt_nascimento', '$aluno_endereco', '$nomeDoArquivo', '$path', NOW())";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['success_message'] = "Aluno cadastrado com sucesso!";
                header("refresh:2; url=../pag-inicial.html");
            } else {
                echo "Erro ao cadastrar o aluno: " . $conn->error;
            }
        } else {
            echo "<p>Falha ao enviar arquivo</p>";
        }
    }    
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Alunos</title>
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
        <h2>Cadastro de Alunos</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        
        <form method="post" action="" enctype="multipart/form-data">

            <label for="aluno_nome">Nome:</label><br>
            <input type="text" id="aluno_nome" name="aluno_nome" required><br><br>

            <label for="aluno_sexo">Sexo:</label><br>
            <select id="aluno_sexo" name="aluno_sexo">
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
            </select><br><br>

            <label for="aluno_idade">Idade:</label><br>
            <input type="number" id="aluno_idade" name="aluno_idade" required><br><br>

            <label for="aluno_email">E-mail:</label><br>
            <input type="email" id="aluno_email" name="aluno_email" required><br><br>

            <label for="aluno_telefone">Telefone:</label><br>
            <input type="text" id="aluno_telefone" name="aluno_telefone" required><br><br>

            <label for="aluno_rg">RG:</label><br>
            <input type="text" id="aluno_rg" name="aluno_rg" required><br><br>

            <label for="aluno_cpf">CPF:</label><br>
            <input type="text" id="aluno_cpf" name="aluno_cpf" required><br><br>
            
            <label for="aluno_dt_nascimento">Data de Nascimento:</label><br>
            <input type="date" id="aluno_dt_nascimento" name="aluno_dt_nascimento" required><br><br>

            <label for="aluno_endereco">Endereço:</label><br>
            <input type="text" id="aluno_endereco" name="aluno_endereco" required><br><br>

            <label for="arquivo">Foto:</label><br>
            <input type="file" id="arquivo" name="arquivo"  required><br><br>
            
         
            <input type="submit" value="Cadastrar Aluno">
        </form>

    </div>
</body>
</html>
