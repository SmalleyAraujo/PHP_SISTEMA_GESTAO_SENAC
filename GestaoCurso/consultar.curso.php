<?php
session_start();
require_once '../conexao.php'; 

// Função para atualizar a quantidade de horas dos cursos
function atualizarQuantidadeHoras($conn) {
    // Consulta SQL para calcular a quantidade de horas de cada curso
    $sql = "SELECT cd.fk_curso_curso_codigo, SUM(d.disciplina_carga_horaria) AS total_horas
            FROM curso_disciplina AS cd
            INNER JOIN disciplina AS d ON cd.fk_disciplina_disciplina_codigo = d.disciplina_codigo
            GROUP BY cd.fk_curso_curso_codigo";

    $result = $conn->query($sql);

    // Verifica se a consulta foi bem-sucedida
    if ($result) {
        // Atualiza a quantidade de horas para cada curso
        while ($row = $result->fetch_assoc()) {
            $curso_codigo = $row['fk_curso_curso_codigo'];
            $total_horas = $row['total_horas'];

            $update_sql = "UPDATE curso SET curso_quantidade_horas = $total_horas WHERE curso_codigo = '$curso_codigo'";
            if(!$conn->query($update_sql)) {
                echo "Erro ao atualizar quantidade de horas: " . $conn->error;
            }
        }
    } else {
        echo "Erro na consulta: " . $conn->error;
    }
}

// Chama a função para atualizar a quantidade de horas
atualizarQuantidadeHoras($conn);

// Consulta os cursos no banco de dados
$sql_cursos = "SELECT * FROM curso";

if(isset($_POST['search'])) {
    $search_term = $_POST['search'];
    $sql_cursos .= " WHERE curso_nome LIKE '%$search_term%'";
}

// Adiciona a ordenação à consulta SQL
$sql_cursos .= " ORDER BY curso_nome";

// Executa a consulta
$result_cursos = $conn->query($sql_cursos);

// Função para excluir um curso
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Excluir entradas na tabela aluno_turma associadas às turmas vinculadas ao curso
    $sql_delete_aluno_turma = "DELETE FROM aluno_turma WHERE fk_turma_turma_codigo IN (SELECT turma_codigo FROM turma WHERE fk_curso_curso_codigo = '$id')";
    if($conn->query($sql_delete_aluno_turma) === TRUE) {
        // Excluir salas de aula associadas às turmas vinculadas ao curso
        $sql_delete_sala_turma = "DELETE FROM sala_turma WHERE fk_turma_turma_codigo IN (SELECT turma_codigo FROM turma WHERE fk_curso_curso_codigo = '$id')";
        if($conn->query($sql_delete_sala_turma) === TRUE) {
            // Excluir turmas vinculadas ao curso
            $sql_delete_turmas = "DELETE FROM turma WHERE fk_curso_curso_codigo = '$id'";
            if($conn->query($sql_delete_turmas) === TRUE) {
                // Excluir associações de disciplinas vinculadas ao curso na tabela curso_disciplina
                $sql_delete_curso_disciplina = "DELETE FROM curso_disciplina WHERE fk_curso_curso_codigo = '$id'";
                if($conn->query($sql_delete_curso_disciplina) === TRUE) {
                    // Excluir registros de matrículas vinculadas ao curso na tabela matricula
                    $sql_delete_matricula = "DELETE FROM matricula WHERE fk_curso_curso_codigo = '$id'";
                    if($conn->query($sql_delete_matricula) === TRUE) {
                        // Excluir o curso
                        $sql_delete_course = "DELETE FROM curso WHERE curso_codigo = '$id'";
                        if($conn->query($sql_delete_course) === TRUE) {
                            // Exclusão bem-sucedida, recarrega a página para atualizar a tabela
                            header("Location: consultar.curso.php");
                            exit();
                        } else {
                            echo "Erro ao excluir curso: " . $conn->error;
                        }
                    } else {
                        echo "Erro ao excluir registros da tabela matricula: " . $conn->error;
                    }
                } else {
                    echo "Erro ao excluir associações de disciplina: " . $conn->error;
                }
            } else {
                echo "Erro ao excluir turmas: " . $conn->error;
            }
        } else {
            echo "Erro ao excluir salas de aula associadas às turmas: " . $conn->error;
        }
    } else {
        echo "Erro ao excluir associações de aluno e turma: " . $conn->error;
    }
}




?>

<!DOCTYPE html>
<html>
<head>
    <title>Consulta de Cursos</title>
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
            width: 80%;
            margin: 0 auto; /* Centraliza horizontalmente */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #3d5275; /* Cor de fundo da tabela */
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: #fff;
        }
        th {
            background-color: #247D9E; /* Cor de fundo do cabeçalho */
        }
        .delete-link {
            color: red;
            text-decoration: none;
        }
        .delete-link:hover {
            text-decoration: underline;
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
    <div class="container">
        <h2>Consulta de Cursos</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a>

        <!-- Formulário de pesquisa -->
        <form method="post" action="">
            <input type="text" name="search" placeholder="Pesquisar por nome do curso">
            <input type="submit" value="Pesquisar">
        </form>
        <div id="cabecalho">  
        <button id="adicionar">Adicionar Curso<br><a href='cadastrar.curso.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../icons/add_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'></a></button>
        </div>
        <table>
            <tr>                
                <th>Curso</th>
                <th>Situação</th>
                <th>Descrição</th>
                <th>Quantidade de Horas</th>
                <th>Ações</th>                
            </tr>
            <?php
            // Exibe os cursos na tabela
            if ($result_cursos->num_rows > 0) {
                while($row = $result_cursos->fetch_assoc()) {
                    echo "<tr>";                    
                    echo "<td>".$row["curso_nome"]."</td>";
                    echo "<td>".$row["curso_situacao"]."</td>";
                    echo "<td>".$row["curso_descricao"]."</td>";
                    echo "<td>".$row["curso_quantidade_horas"]."</td>";
                    echo "<td>
                            <a href='?delete_id=".$row["curso_codigo"]."' onclick='return confirm(\"Tem certeza de que deseja excluir este curso?\")' class='delete-link'>
                                <img src=\"../icons/delete_FILL0_wght400_GRAD0_opsz24.svg\" alt=\"\" style=\"width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;\">
                            </a>
                            <a href='atualizar.curso.php?id=".$row["curso_codigo"]."' style='text-decoration: none; padding-left: 20%; font-weight: bold '>
                                <img src='../icons/edit_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>
                            </a>
                        </td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum curso encontrado</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
