<?php
session_start();
require_once '../conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os dados do formulário
    $professor_nome = $_POST['nome'];
    $professor_email = $_POST['email'];
    $professor_cpf = $_POST['cpf'];
    $professor_endereco = $_POST['endereco'];
    $professor_telefone = $_POST['telefone'];
    $professor_sexo = $_POST['sexo'];
    $professor_data_admissao = $_POST['data_admissao'];
    $professor_data_desligamento = $_POST['data_desligamento'] ? $_POST['data_desligamento'] : null; // Tratamento para data de desligamento
    $professor_titulacao = $_POST['titulacao'];
    $professor_data_nascimento = $_POST['data_nascimento'];
    $professor_url_curriculo_lattes = $_POST['url_curriculo_lattes'];

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
            // Insere os dados do professor e do arquivo na mesma instrução SQL
            $sql = "INSERT INTO professor (professor_nome, professor_email, professor_cpf, professor_endereco, professor_telefone, professor_sexo, professor_data_admissao, professor_data_desligamento, professor_titulacao, professor_data_nascimento, professor_url_curriculo_lattes, professor_foto_nome, professor_foto_path, professor_foto_data_upload) 
                    VALUES ('$professor_nome', '$professor_email', '$professor_cpf', '$professor_endereco', '$professor_telefone', '$professor_sexo', '$professor_data_admissao', '$professor_data_desligamento', '$professor_titulacao', '$professor_data_nascimento', '$professor_url_curriculo_lattes', '$nomeDoArquivo', '$path', NOW())";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['success_message'] = "Professor cadastrado com sucesso!";
                header("refresh:2; url=../pag-inicial.html");
            } else {
                echo "Erro ao cadastrar o professor: " . $conn->error;
            }
        } else {
            echo "<p>Falha ao enviar arquivo</p>";
        }
    }    
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor</title>
    <style>
        body {
            background: rgb(238,174,202);
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(113,20,244,1) 0%, rgba(141,126,219,1) 0%, rgba(138,126,219,1) 82%);
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            margin: 0;
        }

        .container {
            width: 40%;
            margin: 0 auto;
            background-color: #247D9E;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        h2 {
            color: white;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #cccccc;
            text-align: left;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="tel"],
        input[type="url"],
        select,
        input[type="file"],
        input[type="submit"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 4px;
            background-color: #3d5275;
            color: #fff;
            box-sizing: border-box;
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
        <h2>Cadastro de Professor</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="nome">Nome:</label><br>
            <input type="text" id="nome" name="nome" required><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br>

            <label for="cpf">CPF:</label><br>
            <input type="text" id="cpf" name="cpf" required><br>

            <label for="endereco">Endereço:</label><br>
            <input type="text" id="endereco" name="endereco"><br>

            <label for="telefone">Telefone:</label><br>
            <input type="tel" id="telefone" name="telefone"><br>

            <label for="sexo">Sexo:</label><br>
            <select id="sexo" name="sexo" required>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
                <option value="Outro">Outro</option>
            </select><br>

            <label for="data_admissao">Data de Admissão:</label><br>
            <input type="date" id="data_admissao" name="data_admissao" required><br>

            <label for="data_desligamento">Data de Desligamento:</label><br>
            <input type="date" id="data_desligamento" name="data_desligamento"><br>

            <label for="titulacao">Titulação:</label><br>
            <input type="text" id="titulacao" name="titulacao"><br>

            <label for="data_nascimento">Data de Nascimento:</label><br>
            <input type="date" id="data_nascimento" name="data_nascimento" required><br>

            <label for="url_curriculo_lattes">URL do Currículo Lattes:</label><br>
            <input type="url" id="url_curriculo_lattes" name="url_curriculo_lattes"><br>

            <label for="arquivo">Foto:</label><br>
            <input type="file" id="arquivo" name="arquivo" required><br>

            <input type="submit" value="Cadastrar Professor">
        </form>
    </div>
</body>
</html>
