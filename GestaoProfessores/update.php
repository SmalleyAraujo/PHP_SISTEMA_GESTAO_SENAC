<?php
// Incluir arquivo de conexão com o banco de dados
include '../conexao.php';

// Função para limpar e validar dados de entrada
function validarDados($conn, $dados) {
    return htmlspecialchars(strip_tags(mysqli_real_escape_string($conn, $dados)));
}

$id = $professor_nome = $professor_email = $professor_cpf = $professor_endereco = $professor_telefone = $professor_sexo = $professor_data_admissao = $professor_data_desligamento = $professor_titulacao = $professor_data_nascimento = $professor_url_curriculo_lattes = $professor_foto_path = $professor_foto_nome = $professor_foto_data_upload = '';
$professor_dias_disponiveis = [];

// Verifica se foi passado um ID de professor válido pela URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM professor WHERE professor_codigo = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $conn->error);
    }
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        die('Erro ao executar a consulta: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $professor_nome = $row["professor_nome"];
        $professor_email = $row["professor_email"];
        $professor_cpf = $row["professor_cpf"];
        $professor_endereco = $row["professor_endereco"];
        $professor_telefone = $row["professor_telefone"];
        $professor_sexo = $row["professor_sexo"];
        $professor_data_admissao = $row["professor_data_admissao"];
        $professor_data_desligamento = $row["professor_data_desligamento"];
        $professor_titulacao = $row["professor_titulacao"];
        $professor_data_nascimento = $row["professor_data_nascimento"];
        $professor_url_curriculo_lattes = $row["professor_url_curriculo_lattes"];
        $professor_foto_path = $row["professor_foto_path"];
        $professor_foto_nome = $row["professor_foto_nome"];
        $professor_foto_data_upload = $row["professor_foto_data_upload"];

        $professor_dias_disponiveis = [
            'segunda_manha' => $row['professor_disponibilidade_segunda_manha'],
            'segunda_tarde' => $row['professor_disponibilidade_segunda_tarde'],
            'segunda_noite' => $row['professor_disponibilidade_segunda_noite'],
            'terca_manha' => $row['professor_disponibilidade_terca_manha'],
            'terca_tarde' => $row['professor_disponibilidade_terca_tarde'],
            'terca_noite' => $row['professor_disponibilidade_terca_noite'],
            'quarta_manha' => $row['professor_disponibilidade_quarta_manha'],
            'quarta_tarde' => $row['professor_disponibilidade_quarta_tarde'],
            'quarta_noite' => $row['professor_disponibilidade_quarta_noite'],
            'quinta_manha' => $row['professor_disponibilidade_quinta_manha'],
            'quinta_tarde' => $row['professor_disponibilidade_quinta_tarde'],
            'quinta_noite' => $row['professor_disponibilidade_quinta_noite'],
            'sexta_manha' => $row['professor_disponibilidade_sexta_manha'],
            'sexta_tarde' => $row['professor_disponibilidade_sexta_tarde'],
            'sexta_noite' => $row['professor_disponibilidade_sexta_noite'],
            'sabado_manha' => $row['professor_disponibilidade_sabado_manha'],
            'sabado_tarde' => $row['professor_disponibilidade_sabado_tarde'],
            'sabado_noite' => $row['professor_disponibilidade_sabado_noite']
        ];

        // Processamento do formulário quando enviado via POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recupera os dados do formulário
            $professor_nome = validarDados($conn, $_POST["nome"]);
            $professor_email = validarDados($conn, $_POST["email"]);
            $professor_cpf = validarDados($conn, $_POST["cpf"]);
            $professor_endereco = validarDados($conn, $_POST["endereco"]);
            $professor_telefone = validarDados($conn, $_POST["telefone"]);
            $professor_sexo = validarDados($conn, $_POST["sexo"]);
            $professor_data_admissao = validarDados($conn, $_POST["data_admissao"]);
            $professor_data_desligamento = validarDados($conn, $_POST["data_desligamento"]);
            $professor_titulacao = validarDados($conn, $_POST["titulacao"]);
            $professor_data_nascimento = validarDados($conn, $_POST["data_nascimento"]);
            $professor_url_curriculo_lattes = validarDados($conn, $_POST["url_curriculo_lattes"]);

            // Verifica se foi fornecida uma data de desligamento
            if (!empty($_POST["data_desligamento"])) {
                $professor_data_desligamento = validarDados($conn, $_POST["data_desligamento"]);
            } else {
                $professor_data_desligamento = NULL; // Define como NULL para ser inserido no banco de dados
            }

            // Processamento da foto
            if ($_FILES['foto']['size'] > 0) {
                $upload_dir = '../arquivos2/';
                $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_file_new = $upload_dir . $new_filename;

                // Move o arquivo para a pasta de destino com o nome único
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_file_new)) {
                    // Atualiza o caminho da foto, nome e data de upload no banco de dados
                    $professor_foto_path = $upload_file_new;
                    $professor_foto_nome = $new_filename;
                    $professor_foto_data_upload = date('Y-m-d H:i:s');
                    
                    $stmt_update_foto = $conn->prepare("UPDATE professor SET professor_foto_path=?, professor_foto_nome=?, professor_foto_data_upload=? WHERE professor_codigo=?");
                    $stmt_update_foto->bind_param("sssi", $professor_foto_path, $professor_foto_nome, $professor_foto_data_upload, $id);
                    
                    if ($stmt_update_foto->execute()) {
                        echo "Foto do professor atualizada com sucesso.";
                    } else {
                        echo "Erro ao atualizar a foto do professor: " . $stmt_update_foto->error;
                    }

                    $stmt_update_foto->close();
                } else {
                    echo "Erro ao fazer upload da foto.";
                }
            }

            // Atualização dos demais dados do professor
            $dias_disponiveis = [
                'segunda_manha' => in_array('segunda_manha', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'segunda_tarde' => in_array('segunda_tarde', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'segunda_noite' => in_array('segunda_noite', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'terca_manha' => in_array('terca_manha', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'terca_tarde' => in_array('terca_tarde', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'terca_noite' => in_array('terca_noite', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'quarta_manha' => in_array('quarta_manha', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'quarta_tarde' => in_array('quarta_tarde', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'quarta_noite' => in_array('quarta_noite', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'quinta_manha' => in_array('quinta_manha', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'quinta_tarde' => in_array('quinta_tarde', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'quinta_noite' => in_array('quinta_noite', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'sexta_manha' => in_array('sexta_manha', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'sexta_tarde' => in_array('sexta_tarde', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'sexta_noite' => in_array('sexta_noite', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'sabado_manha' => in_array('sabado_manha', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'sabado_tarde' => in_array('sabado_tarde', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel',
                'sabado_noite' => in_array('sabado_noite', $_POST['dias_semana']) ? 'disponivel' : 'indisponivel'
            ];

            $stmt_update = $conn->prepare("UPDATE professor SET professor_nome=?, professor_email=?, professor_cpf=?, professor_endereco=?, professor_telefone=?, professor_sexo=?, professor_data_admissao=?, professor_data_desligamento=?, professor_titulacao=?, professor_data_nascimento=?, professor_url_curriculo_lattes=?, professor_disponibilidade_segunda_manha=?, professor_disponibilidade_segunda_tarde=?, professor_disponibilidade_segunda_noite=?, professor_disponibilidade_terca_manha=?, professor_disponibilidade_terca_tarde=?, professor_disponibilidade_terca_noite=?, professor_disponibilidade_quarta_manha=?, professor_disponibilidade_quarta_tarde=?, professor_disponibilidade_quarta_noite=?, professor_disponibilidade_quinta_manha=?, professor_disponibilidade_quinta_tarde=?, professor_disponibilidade_quinta_noite=?, professor_disponibilidade_sexta_manha=?, professor_disponibilidade_sexta_tarde=?, professor_disponibilidade_sexta_noite=?, professor_disponibilidade_sabado_manha=?, professor_disponibilidade_sabado_tarde=?, professor_disponibilidade_sabado_noite=? WHERE professor_codigo=?");
            $stmt_update->bind_param("sssssssssssssssssssssssssssssi", $professor_nome, $professor_email, $professor_cpf, $professor_endereco, $professor_telefone, $professor_sexo, $professor_data_admissao, $professor_data_desligamento, $professor_titulacao, $professor_data_nascimento, $professor_url_curriculo_lattes, $dias_disponiveis['segunda_manha'], $dias_disponiveis['segunda_tarde'], $dias_disponiveis['segunda_noite'], $dias_disponiveis['terca_manha'], $dias_disponiveis['terca_tarde'], $dias_disponiveis['terca_noite'], $dias_disponiveis['quarta_manha'], $dias_disponiveis['quarta_tarde'], $dias_disponiveis['quarta_noite'], $dias_disponiveis['quinta_manha'], $dias_disponiveis['quinta_tarde'], $dias_disponiveis['quinta_noite'], $dias_disponiveis['sexta_manha'], $dias_disponiveis['sexta_tarde'], $dias_disponiveis['sexta_noite'], $dias_disponiveis['sabado_manha'], $dias_disponiveis['sabado_tarde'], $dias_disponiveis['sabado_noite'], $id);

            if ($stmt_update->execute()) {
                echo "Dados do professor atualizados com sucesso.";
                 // Redirecionamento para read.php após 3 segundos
                 echo "<script>setTimeout(function() {
                    window.location.href = 'read.php';
                }, 2000);</script>";
            } else {
                echo "Erro ao atualizar os dados do professor: " . $stmt_update->error;
            }

            $stmt_update->close();
        }
    } else {
        echo "Nenhum professor encontrado com o ID fornecido.";
    }

    $stmt->close();
} else {
    echo "ID de professor não fornecido.";
}

// Fechar a conexão com o banco de dados
$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Professor</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and Container */
        body {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            color: #333;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }

        /* Heading */
        h2 {
            color: #4e54c8;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Form Style */
        form {
            display: flex;
            flex-direction: column;
        }

        /* Labels */
        label {
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
            text-align: left;
        }

        /* Inputs */
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="file"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
            font-size: 16px;
            color: #333;
        }

        input[type="file"] {
            padding: 5px;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #4e54c8;
            background-color: #e6e6ff;
        }

        /* Button */
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #4e54c8;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #3d42a3;
        }

        /* Error Message */
        .error {
            color: #e74c3c;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 15px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            input[type="text"],
            input[type="email"],
            input[type="number"],
            select,
            textarea,
            input[type="submit"] {
                font-size: 14px;
                padding: 8px;
            }
        }

        /* Disponibilidade Section */
        .disponibilidade-section {
            margin-top: 20px;
        }

        .disponibilidade-label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Professor</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <!-- Campos de texto e outros dados do professor -->
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo $professor_nome; ?>">

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo $professor_email; ?>">

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" value="<?php echo $professor_cpf; ?>">

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?php echo $professor_endereco; ?>">

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo $professor_telefone; ?>">

            <label for="sexo">Sexo:</label>
            <input type="text" id="sexo" name="sexo" value="<?php echo $professor_sexo; ?>">

            <label for="data_admissao">Data de Admissão:</label>
            <input type="date" id="data_admissao" name="data_admissao" value="<?php echo $professor_data_admissao; ?>">

            <label for="data_desligamento">Data de Desligamento:</label>
            <input type="date" id="data_desligamento" name="data_desligamento" value="<?php echo $professor_data_desligamento; ?>">

            <label for="titulacao">Titulação:</label>
            <input type="text" id="titulacao" name="titulacao" value="<?php echo $professor_titulacao; ?>">

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo $professor_data_nascimento; ?>">

            <label for="url_curriculo_lattes">URL Currículo Lattes:</label>
            <input type="text" id="url_curriculo_lattes" name="url_curriculo_lattes" value="<?php echo $professor_url_curriculo_lattes; ?>">

            <label for="foto">Foto:</label>
            <input type="file" id="foto" name="foto">

            <!-- Seção de Disponibilidade -->
            <div class="disponibilidade-section">
                <span class="disponibilidade-label">Disponibilidade:</span>
                <?php
                $dias_semana = [
                    'segunda_manha' => 'Segunda Manhã',
                    'segunda_tarde' => 'Segunda Tarde',
                    'segunda_noite' => 'Segunda Noite',
                    'terca_manha' => 'Terça Manhã',
                    'terca_tarde' => 'Terça Tarde',
                    'terca_noite' => 'Terça Noite',
                    'quarta_manha' => 'Quarta Manhã',
                    'quarta_tarde' => 'Quarta Tarde',
                    'quarta_noite' => 'Quarta Noite',
                    'quinta_manha' => 'Quinta Manhã',
                    'quinta_tarde' => 'Quinta Tarde',
                    'quinta_noite' => 'Quinta Noite',
                    'sexta_manha' => 'Sexta Manhã',
                    'sexta_tarde' => 'Sexta Tarde',
                    'sexta_noite' => 'Sexta Noite',
                    'sabado_manha' => 'Sábado Manhã',
                    'sabado_tarde' => 'Sábado Tarde',
                    'sabado_noite' => 'Sábado Noite'
                ];

                foreach ($dias_semana as $dia => $label) {
                    $checked = ($professor_dias_disponiveis[$dia] == 'disponivel') ? 'checked' : '';
                    echo "<input type='checkbox' name='dias_semana[]' value='$dia' $checked> $label<br>";
                }
                ?>
            </div>

            <input type="submit" value="Salvar">
        </form>
    </div>
</body>
</html>


