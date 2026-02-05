<?php
session_start();
require_once '../conexao.php'; 

// Processar consulta do relacionamento entre disciplina e curso
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se o filtro pelo nome da disciplina foi enviado
    if(isset($_POST['disciplina_nome_filter']) && !empty($_POST['disciplina_nome_filter'])) {
        // Obter o valor do filtro
        $disciplina_nome_filter = "%".$_POST['disciplina_nome_filter']."%";
        // Consultar o relacionamento entre disciplina e curso no banco de dados
        $sql_pesquisa = "SELECT cd.curso_disciplina_codigo, curso.curso_nome, curso.curso_codigo, disciplina.disciplina_nome, disciplina.disciplina_codigo 
                         FROM curso_disciplina cd
                         INNER JOIN disciplina ON cd.fk_disciplina_disciplina_codigo = disciplina.disciplina_codigo 
                         INNER JOIN curso ON cd.fk_curso_curso_codigo = curso.curso_codigo 
                         WHERE disciplina.disciplina_nome LIKE ?";
        // Preparar a consulta
        $stmt_pesquisa = $conn->prepare($sql_pesquisa);
        // Associar o parâmetro e executar a consulta
        $stmt_pesquisa->bind_param("s", $disciplina_nome_filter);
        $stmt_pesquisa->execute();
        $result_pesquisa = $stmt_pesquisa->get_result();
    }
}

// Consultar todas as associações entre cursos e disciplinas
$sql_todas_associacoes = "SELECT cd.curso_disciplina_codigo, curso.curso_nome, curso.curso_codigo, cd.curso_disciplina_semestre, disciplina.disciplina_nome, disciplina.disciplina_codigo, disciplina.disciplina_carga_horaria, disciplina.disciplina_transversal 
                          FROM curso_disciplina cd
                          INNER JOIN disciplina ON cd.fk_disciplina_disciplina_codigo = disciplina.disciplina_codigo 
                          INNER JOIN curso ON cd.fk_curso_curso_codigo = curso.curso_codigo";

// Adicionar filtro pelo nome da disciplina, se fornecido
if(isset($_POST['disciplina_nome_filter']) && !empty($_POST['disciplina_nome_filter'])) {
    $disciplina_nome_filter = "%".$_POST['disciplina_nome_filter']."%";
    $sql_todas_associacoes .= " WHERE disciplina.disciplina_nome LIKE ?";
    // Preparar a consulta
    $stmt_todas_associacoes = $conn->prepare($sql_todas_associacoes);
    // Associar o parâmetro e executar a consulta
    $stmt_todas_associacoes->bind_param("s", $disciplina_nome_filter);
    $stmt_todas_associacoes->execute();
    $result_todas_associacoes = $stmt_todas_associacoes->get_result();
} else {
    // Se nenhum filtro foi aplicado, executar a consulta sem parâmetros
    $result_todas_associacoes = $conn->query($sql_todas_associacoes);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Consultar Relacionamento</title>
    <style>
        body {
            background: rgb(238,174,202);
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(113,20,244,1) 0%, rgba(141,126,219,1) 0%, rgba(138,126,219,1) 82%);
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto; /* Centraliza a tabela */
            margin-top: 20px;
            background-color: #3d5275; /* Cor de fundo da tabela */
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            color: #fff;
        }
        th {
            background-color: #247D9E; /* Cor de fundo do cabeçalho */
        }
        a {
         text-decoration: none; /* Remove a sublinha dos links */
        color: red; /* Define a cor do texto dos links */
        }

        a:hover {
         text-decoration: none; /* Mantém a sublinha removida ao passar o mouse */
         color: #ccc; /* Define a cor do texto dos links ao passar o mouse */
        }
         /* Estilo para o botão Consultar */
        input[type="submit"] {
            background-color: #247D9E;
            color: #fff;
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Efeito hover estiloso para o botão Consultar */
        input[type="submit"]:hover {
            background-color: #195D7E; /* Mudança de cor no hover */
            transform: translateY(-2px); /* Efeito de levantar um pouco */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra suave */
        }

          /* Estilo para o botão Consultar */
          #adicionar{
            background-color: #247D9E;
            color: #fff;
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #cabecalho{
            display: flex;
            justify-content: end;
            padding-right: 10%;
        }

    </style>
</head>
<body>
    <h2>Consultar Associação entre Disciplinas e Curso</h2>
    <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
    <form method="post" action="">
        
        
        <label for="disciplina_nome_filter">Filtrar por Nome da Disciplina:</label>
        <input type="text" id="disciplina_nome_filter" name="disciplina_nome_filter" placeholder="Digite o nome da disciplina">  
        
        <input type="submit" value="Consultar">
    </form>

    <h3>Resultado da Consulta Específica:</h3>
<table>
    <tr>
        <th>Código Relacionamento</th>
        <th>Disciplina</th>
        <th>Código da Disciplina</th>
        <th>Curso</th>
        <th>Código do Curso</th>                   
    </tr>
    <?php
    if (isset($result_pesquisa) && $result_pesquisa->num_rows > 0) {
        while($row = $result_pesquisa->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["curso_disciplina_codigo"]."</td>";
            echo "<td>".$row["disciplina_nome"]."</td>";
            echo "<td>".$row["disciplina_codigo"]."</td>";
            echo "<td>".$row["curso_nome"]."</td>";
            echo "<td>".$row["curso_codigo"]."</td>";              
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Nenhum relacionamento encontrado</td></tr>";
    }
    ?>
</table>
</body>
</html>

<?php
$conn->close();
?>

