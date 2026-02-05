<?php
session_start();
require_once '../conexao.php'; 

if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    try {
        // Primeiro, exclua todas as associações da disciplina em outras tabelas
        
        // Exclua da tabela curso_disciplina
        $sql_delete_curso_disciplina = "DELETE FROM curso_disciplina WHERE fk_disciplina_disciplina_codigo = '$id'";
        $conn->query($sql_delete_curso_disciplina);

        // Exclua da tabela professor_capacidade
        $sql_delete_professor_capacidade = "DELETE FROM professor_capacidade WHERE fk_disciplina_disciplina_codigo = '$id'";
        if (!$conn->query($sql_delete_professor_capacidade)) {
            throw new Exception("Erro ao excluir associações da disciplina na tabela professor_capacidade: " . $conn->error);
        }
                       
        // Agora, exclua a disciplina
        $sql_delete_disciplina = "DELETE FROM disciplina WHERE disciplina_codigo = '$id'";
        
        if($conn->query($sql_delete_disciplina) === TRUE) {
            // Exclusão bem-sucedida, redireciona para a página de consulta de disciplinas
            header("Location: consultarDisciplina.php");
            exit();
        } else {
            echo "Erro ao excluir disciplina: " . $conn->error;
        }
    } catch (mysqli_sql_exception $e) {
        // Se houver um erro, exiba a mensagem de erro
        echo "Erro ao excluir disciplina: " . $e->getMessage();
    }
}




// Consulta as disciplinas no banco de dados
$sql = "SELECT * FROM disciplina";

// Verifica se há um filtro de nome da disciplina
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';

// Se houver filtro, adiciona a cláusula WHERE à consulta SQL
if (!empty($filtro)) {
    $sql .= " WHERE disciplina_nome LIKE '%$filtro%'";
}

// Adiciona a cláusula ORDER BY para ordenar as disciplinas por nome em ordem alfabética
$sql .= " ORDER BY disciplina_nome";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Consulta de Disciplinas</title>
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
        
        <h2>Consulta de Disciplinas</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <!-- Formulário de filtro por nome da disciplina -->
        <form method="get" action="">
            <label for="filtro">Filtrar por nome da disciplina:</label>
            <input type="text" id="filtro" name="filtro" placeholder="Digite o nome da disciplina">
            <input type="submit" value="Filtrar">
        </form>
        <div id="cabecalho">  
        <button id="adicionar">Adicionar Disciplina<br><a href='cadastrarDisciplina.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../icons/add_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'></a></button>
        </div>
        <table>
            <!-- Cabeçalho da tabela -->
            <tr>
                
                <th>Disciplina</th>
                <th>C.H</th>
                <th>Transversal</th>
                <th>Ementa</th>
                <th>Bibliografia</th>
                <th>Ações</th>                
            </tr>
            <!-- ... -->
            <?php
            // Exibe as disciplinas na tabela
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";                   
                    echo "<td>".$row["disciplina_nome"]."</td>";
                    echo "<td>".$row["disciplina_carga_horaria"]."</td>";
                    echo "<td>".$row["disciplina_transversal"]."</td>";
                    echo "<td>".$row["disciplina_ementa"]."</td>";
                    echo "<td>".$row["disciplina_bibliografia"]."</td>";
                    echo "<td><a href='?delete_id=".$row["disciplina_codigo"]."' onclick='return confirm(\"Tem certeza de que deseja excluir esta disciplina?\")' class='delete-link'><img src=\"../icons/delete_FILL0_wght400_GRAD0_opsz24.svg\" alt=\"\" style=\"width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;\"></a>";
                    echo "<a href='atualizarDisciplina.php?id=".$row["disciplina_codigo"]."&nome=".$row["disciplina_nome"]."&carga_horaria=".$row["disciplina_carga_horaria"]."&transversal=".$row["disciplina_transversal"]."&ementa=".$row["disciplina_ementa"]."&bibliografia=".$row["disciplina_bibliografia"]."' style='text-decoration: none; padding-left: 20%; font-weight: bold '>";
                    echo "<img src='../icons/edit_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>";
                    echo "</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Nenhuma disciplina encontrada</td></tr>";
            }
            ?>
        </table>
        
    </div>
</body>
</html>

<?php
$conn->close();
?>