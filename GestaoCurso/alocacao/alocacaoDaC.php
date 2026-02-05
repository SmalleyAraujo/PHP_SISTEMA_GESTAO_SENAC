<?php
// Iniciamos uma sessão para gerenciar informações do usuário
session_start(); 
require_once '../../conexao.php'; // Certifique-se de incluir o arquivo de conexão



?>
<?php
// Consulta SQL para obter as disciplinas
$sql_disciplina = "SELECT disciplina_codigo, disciplina_nome FROM disciplina";

// Adiciona a ordenação à consulta SQL
$sql_disciplina .= " ORDER BY disciplina_nome";

$result_disciplina = $conn->query($sql_disciplina);

// Consulta SQL para obter os cursos
$sql_curso = "SELECT curso_codigo, curso_nome FROM curso";

// Adiciona a ordenação à consulta SQL
$sql_curso .= " ORDER BY curso_nome";

$result_curso = $conn->query($sql_curso);

?>


<!DOCTYPE html>
<html>
<head>
    <title>Selecionar Disciplina e Curso</title>
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
            margin: 0 auto;
            background-color: #247D9E;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: white;
        }
        select, input[type="submit"] {
            width:100%;
            background-color: #3d5275;
            color: #fff;
            border: 1px solid #666666;
            border-radius: 4px;
            padding: 8px;
            font-size: 16px;
            margin-bottom: 10px;
            display: inline-block;
            transition: all 0.3s;
        }
        select:focus, input[type="submit"]:focus {
            outline: none;
        }
        input[type="submit"]:hover {
            background-color: #666666;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            cursor: pointer;
        }
        input[type="submit"]:active {
            background-color: #4a4a4a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Alocação de Disciplinas aos Cursos</h1>
        <a href="../../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <h2>Selecione uma Disciplina:</h2>
        <form action="" method="POST">
            <select name="disciplina">
                <?php
                if ($result_disciplina->num_rows > 0) {
                    while($row = $result_disciplina->fetch_assoc()) {
                        echo "<option value='" . $row['disciplina_codigo'] . "'>" . $row['disciplina_nome'] . "</option>";
                    }
                } else {
                    echo "0 resultados";
                }
                ?>
            </select>
            <br> <br> <br>
            <h2>Selecione um Curso:</h2>
            <select name="curso">
                <?php
                if ($result_curso->num_rows > 0) {
                    while($row = $result_curso->fetch_assoc()) {
                        echo "<option value='" . $row['curso_codigo'] . "'>" . $row['curso_nome'] . "</option>";
                    }
                } else {
                    echo "0 resultados";
                }
                ?>
            </select>
             <br> <br> 
            <h2>Selecione um Semestre:</h2>            
            <select id="semestre" name="semestre" required>
                <option value="1º Semestre">1º Semestre</option>
                <option value="2º Semestre">2º Semestre</option>
                <option value="3º Semestre">3º Semestre</option>
                <option value="4º Semestre">4º Semestre</option>
            </select><br><br>  
            <input type="submit" value="INCLUIR">
        </form>
        <a href='consultarAlocacaoDaC.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../../icons/search_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'></a>
        
    </div>
</body>
</html>




<?php
// Iniciamos uma sessão para gerenciar informações do usuário

// require_once '../../conexao.php'; // Certifique-se de incluir o arquivo de conexão / Já existe uma comunicação com a conexão virgente no Start

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $disciplina_codigo = $_POST['disciplina'];
    $curso_codigo = $_POST['curso'];
    $curso_disciplina_semestre = $_POST['semestre'];
    
    // Insere os dados na tabela 'curso_disciplina'
    $sql = "INSERT INTO curso_disciplina (curso_disciplina_semestre, fk_disciplina_disciplina_codigo, fk_curso_curso_codigo) VALUES ('$curso_disciplina_semestre','$disciplina_codigo', '$curso_codigo')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Relacionamento entre disciplina e curso cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar o relacionamento entre disciplina e curso: " . $conn->error;
    }
}

?>






<?php
// Fecha a conexão
$conn->close();
?>
