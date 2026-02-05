<?php
session_start(); 
require_once '../../conexao.php'; 

if(isset($_GET['codigo'])) {
    $codigo_turma = $_GET['codigo'];
    
    // Consulta SQL para obter os detalhes da turma com base no código fornecido
    $sql_detalhes_turma = "SELECT * FROM turma WHERE turma_codigo = '$codigo_turma'";
    $result_detalhes_turma = $conn->query($sql_detalhes_turma);
    
    if($result_detalhes_turma->num_rows > 0) {
        // Turma encontrada, preencha o formulário com os detalhes
        $row = $result_detalhes_turma->fetch_assoc();
        $turma_nome = $row['turma_nome'];
        $turma_ano = $row['turma_ano'];
        $turma_semestre = $row['turma_semestre'];
        $turma_estado = $row['turma_estado'];
        $turma_turno = $row['turma_turno'];
        $fk_curso_curso_codigo = $row['fk_curso_curso_codigo']; // Adiciona esta linha para recuperar o código do curso associado à turma
    } else {
        // Se a turma não for encontrada, redirecione de volta para a página de consulta ou exiba uma mensagem de erro
        header("Location: consultaTSemestre.php");
        exit();
    }
} else {
    // Se não foi fornecido nenhum código de turma, redirecione de volta para a página de consulta
    header("Location: consultaTSemestre.php");
    exit();
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $turma_codigo = $_POST['turma_codigo'];
    $turma_nome = $_POST['turma_nome'];
    $turma_ano = $_POST['turma_ano'];
    $turma_semestre = $_POST['turma_semestre'];
    $turma_estado = $_POST['turma_estado'];
    $turma_turno = $_POST['turma_turno'];
    $fk_curso_curso_codigo = $_POST['fk_curso_curso_codigo']; // Adiciona esta linha para obter o código do curso

    // Atualiza os dados da turma no banco de dados
    $sql_update = "UPDATE turma SET turma_nome='$turma_nome', turma_ano='$turma_ano', turma_semestre='$turma_semestre', turma_estado='$turma_estado', turma_turno='$turma_turno', fk_curso_curso_codigo='$fk_curso_curso_codigo' WHERE turma_codigo='$turma_codigo'";
    
    if ($conn->query($sql_update) === TRUE) {
        echo "Turma atualizada com sucesso!";
    } else {
        echo "Erro ao atualizar a turma: " . $conn->error;
    }
}

// Consulta SQL para obter todas as turmas
$sql_turmas = "SELECT * FROM turma";
$result_turmas = $conn->query($sql_turmas);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alteração de Turma</title>
    <style>
        body {
            background: rgb(238,174,202);
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(113,20,244,1) 0%, rgba(141,126,219,1) 0%, rgba(138,126,219,1) 82%);
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
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
        input[type="text"], input[type="number"], select, input[type="submit"] {
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
        #cabecalho{
            display: flex;
            justify-content: center;
            margin-bottom:1%;
        }
    </style>
</head>
<body>
    <h2>Alteração de Turma</h2>

    <div id="cabecalho">
             <a href="../../pag-inicial.html" style="text-decoration: none;  font-weight: bold "><img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
             <a href='consultaTSemestre.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../../icons/search_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>Pesquisar</a>
        </div>

    <div class="container">
        <form method="post">
            <input type="hidden" id="turma_codigo" name="turma_codigo" value="<?php echo $codigo_turma; ?>"><br><br>

            <label for="fk_curso_curso_codigo">Curso:</label><br>
            <select id="fk_curso_curso_codigo" name="fk_curso_curso_codigo" required>
                <option value="">Selecione o Curso</option>
                <?php
                // Consulta SQL para obter os cursos
                $sql_cursos = "SELECT curso_codigo, curso_nome FROM curso";
                $result_cursos = $conn->query($sql_cursos);
                // Verifica se há cursos disponíveis
                if ($result_cursos->num_rows > 0) {
                    // Loop através dos resultados da consulta para exibir as opções do select
                    while ($row = $result_cursos->fetch_assoc()) {
                        $selected = ($row['curso_codigo'] == $fk_curso_curso_codigo) ? 'selected' : ''; // Define a opção selecionada com base no curso associado à turma
                        echo "<option value='" . $row['curso_codigo'] . "' $selected>" . $row['curso_nome'] . "</option>";
                    }
                }
                ?>
            </select><br><br>

            <label for="turma_nome">Nome da Turma:</label><br>
            <input type="text" id="turma_nome" name="turma_nome" value="<?php echo $turma_nome; ?>" required><br><br>
            
            <label for="turma_ano">Ano:</label><br>
            <input type="number" id="turma_ano" name="turma_ano" value="<?php echo $turma_ano; ?>" required><br><br>

            <label for="turma_semestre">Semestre:</label><br>
            <select id="turma_semestre" name="turma_semestre" required>
            <option value="1º Semestre" <?php if($turma_semestre == "1º Semestre") echo "selected"; ?>>1º Semestre</option>
                <option value="2º Semestre" <?php if($turma_semestre == "2º Semestre") echo "selected"; ?>>2º Semestre</option>
                <option value="3º Semestre" <?php if($turma_semestre == "3º Semestre") echo "selected"; ?>>3º Semestre</option>
                <option value="4º Semestre" <?php if($turma_semestre == "4º Semestre") echo "selected"; ?>>4º Semestre</option>
            </select><br><br>
        
            <label for="turma_estado">Estado:</label><br>
            <select id="turma_estado" name="turma_estado" required>
                <option value="Ativa" <?php if($turma_estado == "Ativa") echo "selected"; ?>>Ativa</option>
                <option value="Inativa" <?php if($turma_estado == "Inativa") echo "selected"; ?>>Inativa</option>
            </select><br><br>
        
            <label for="turma_turno">Turno:</label><br>
            <select id="turma_turno" name="turma_turno" required>
                <option value="Manhã" <?php if($turma_turno == "Manhã") echo "selected"; ?>>Manhã</option>
                <option value="Tarde" <?php if($turma_turno == "Tarde") echo "selected"; ?>>Tarde</option>
                <option value="Noite" <?php if($turma_turno == "Noite") echo "selected"; ?>>Noite</option>
            </select><br><br>
        
            <input type="submit" value="Atualizar Turma">
        </form>
        
    </div>
</body>
</html>

<?php
// Fecha a conexão
$conn->close();
?>
