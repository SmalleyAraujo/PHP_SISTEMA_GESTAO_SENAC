<?php
session_start();
require_once '../../conexao.php'; 

// Consultar todas as associações entre cursos e disciplinas
$sql_todas_associacoes = "SELECT cd.curso_disciplina_codigo, curso.curso_nome, curso.curso_codigo, cd.curso_disciplina_semestre, disciplina.disciplina_nome, disciplina.disciplina_codigo, disciplina.disciplina_carga_horaria, disciplina.disciplina_transversal 
                          FROM curso_disciplina cd
                          INNER JOIN disciplina ON cd.fk_disciplina_disciplina_codigo = disciplina.disciplina_codigo 
                          INNER JOIN curso ON cd.fk_curso_curso_codigo = curso.curso_codigo";

// Adiciona filtro pelo nome do curso, se fornecido
if(isset($_POST['curso_nome_filter']) && !empty($_POST['curso_nome_filter'])) {
    $curso_nome_filter = $_POST['curso_nome_filter'];
    $sql_todas_associacoes .= " WHERE curso.curso_nome LIKE '%$curso_nome_filter%'";
}

// Ordena os resultados pelo código do curso e pelo código do semestre
$sql_todas_associacoes .= " ORDER BY curso.curso_nome, cd.curso_disciplina_semestre, disciplina.disciplina_nome";

$result_todas_associacoes = $conn->query($sql_todas_associacoes);

// Função para excluir uma alocação do banco de dados
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM curso_disciplina WHERE curso_disciplina_codigo = '$id'";
    if($conn->query($sql_delete) === TRUE) {
        // Exclusão bem-sucedida, redirecionar para a mesma página
        echo '<script>alert("Excluído com sucesso!");</script>';
        echo '<script>window.location.href = "consultarAlocacaoDaC.php";</script>'; // Substitua "nome_da_pagina.php" pelo nome da sua página
        exit(); // Encerra o script PHP para evitar a execução de mais código desnecessário
    } else {
        echo '<script>alert("Erro ao excluir alocação!");</script>';
    }
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
    <h2>Consultar Relacionamento entre Curso e Disciplinas</h2>
    <a href="../../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
    <form method="post" action="">
            
        
        <label for="curso_nome_filter">Filtrar por Nome do Curso:</label>
        <input type="text" id="curso_nome_filter" name="curso_nome_filter" placer-holder="digite o nome do curso">  
        
        <input type="submit" value="Consultar">
    </form>  
      
    <h3>Todas as Alocações de Cursos e Disciplinas:</h3>
    <div id="cabecalho">  
        <button id="adicionar">Criar Aloc.Curso Disciplina<br><a href='alocacaoDaC.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../../icons/add_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'></a></button>
    </div>
    <table>
        <tr>            
            <th>Curso</th>            
            <th>Módulos</th>                       
            <th>Unidade Curricular</th>            
            <th>CH</th>            
            <th>Transversal</th>            
            <th>Ações</th>            
        </tr>
                <?php
        if ($result_todas_associacoes->num_rows > 0) {
            while($row = $result_todas_associacoes->fetch_assoc()) {
                echo "<tr>";               
                echo "<td>".$row["curso_nome"]."</td>";                
                echo "<td>".$row["curso_disciplina_semestre"]."</td>";                
                echo "<td>".$row["disciplina_nome"]."</td>";                
                echo "<td>".$row["disciplina_carga_horaria"]."</td>";                
                echo "<td>".$row["disciplina_transversal"]."</td>";                
                echo "<td><a href='?delete_id=".$row["curso_disciplina_codigo"]."' onclick='return confirm(\"Tem certeza de que deseja excluir este relacionamento?\")' class='delete-link'><img src=\"../../icons/delete_FILL0_wght400_GRAD0_opsz24.svg\" alt=\"\" style=\"width: 35px; right: 35px; padding: 5%; box-shadow: 5px 5px 10px violet; cursor: pointer;\"></a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>Nenhuma associação encontrada</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>