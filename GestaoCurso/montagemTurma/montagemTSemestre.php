<?php
// Iniciamos uma sessão para gerenciar informações do usuário
session_start(); 
require_once '../../conexao.php'; // Certifique-se de incluir o arquivo de conexão

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $turma_nome = $_POST['turma_nome'];
    $turma_ano = $_POST['turma_ano'];
    $turma_semestre = $_POST['turma_semestre'];
    $turma_estado = $_POST['turma_estado'];
    $turma_turno = $_POST['turma_turno'];
    $fk_curso_curso_codigo = $_POST['fk_curso_curso_codigo'];

    // Insere os dados na tabela 'turma'
    $sql = "INSERT INTO turma (fk_curso_curso_codigo, turma_nome, turma_ano, turma_semestre, turma_estado, turma_turno) VALUES ('$fk_curso_curso_codigo', '$turma_nome', '$turma_ano', '$turma_semestre', '$turma_estado', '$turma_turno')";

    
    if ($conn->query($sql) === TRUE) {
        echo "Turma cadastrada com sucesso!";
    } else {
        echo "Erro ao cadastrar a turma: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Montagem de Turma por Semestre</title>
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
            justify-content: space-around;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Montagem de Turma por Semestre</h2>
        <div id="cabecalho">
             <a href="../../pag-inicial.html" style="text-decoration: none; padding-left: 20%; font-weight: bold "><img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
             <a href='consultaTSemestre.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../../icons/search_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>Pesquisar</a>
        </div>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
                            echo "<option value='" . $row['curso_codigo'] . "'>" . $row['curso_nome'] . "</option>";
                        }
                    }
                    ?>
                </select><br><br>
                
            <label for="turma_nome">Nome da Turma:</label><br>
            <input type="text" id="turma_nome" name="turma_nome" required><br><br>
            
            <label for="turma_ano">Ano:</label><br>
            <input type="number" id="turma_ano" name="turma_ano" required><br><br>
            
            <label for="turma_semestre">Semestre:</label><br>
            <select id="turma_semestre" name="turma_semestre" required>
                <option value="1º Semestre">1º Semestre</option>
                <option value="2º Semestre">2º Semestre</option>
                <option value="3º Semestre">3º Semestre</option>
                <option value="4º Semestre">4º Semestre</option>
            </select><br><br>
            
            <label for="turma_estado">Estado:</label><br>
            <select id="turma_estado" name="turma_estado" required>
                <option value="Ativa">Ativa</option>
                <option value="Inativa">Inativa</option>
            </select><br><br>
            
            <label for="turma_turno">Turno:</label><br>
            <select id="turma_turno" name="turma_turno" required>
                <option value="Manhã">Manhã</option>
                <option value="Tarde">Tarde</option>
                <option value="Noite">Noite</option>
            </select><br><br>
            
            <input type="submit" value="Montar Turma">            
        </form>
        
    </div>
</body>
</html>


<?php
// Fecha a conexão
$conn->close();
?>
