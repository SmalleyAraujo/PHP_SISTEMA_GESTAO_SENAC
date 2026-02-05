<?php
session_start();
require_once '../conexao.php'; 

// Inicializa a string da consulta SQL
$sql_alunos = "SELECT * FROM aluno";

// Verifica se houve uma busca por nome de aluno
if(isset($_POST['search'])) {
    $search_term = $_POST['search'];
    // Utiliza prepared statement para evitar SQL injection
    $sql_alunos .= " WHERE aluno_nome LIKE ?";
    // Prepara a consulta
    $stmt = $conn->prepare($sql_alunos);
    // Adiciona o caractere curinga '%' para buscar por qualquer ocorrência do termo de busca
    $search_term = "%$search_term%";
    // Executa a consulta com o termo de busca seguro
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result_alunos = $stmt->get_result();
} else {
    // Executa a consulta normal se não houver busca
    $result_alunos = $conn->query($sql_alunos);
}

// Função para excluir um aluno
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Excluir registros de matrículas vinculadas ao aluno na tabela matricula
    $sql_delete_matricula = "DELETE FROM matricula WHERE fk_aluno_aluno_codigo = ?";
    $stmt = $conn->prepare($sql_delete_matricula);
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        // Excluir registros de associação de aluno à turma na tabela aluno_turma
        $sql_delete_aluno_turma = "DELETE FROM aluno_turma WHERE fk_aluno_aluno_codigo = ?";
        $stmt = $conn->prepare($sql_delete_aluno_turma);
        $stmt->bind_param("i", $id);
        if($stmt->execute()) {
            // Excluir o aluno
            $sql_delete_aluno = "DELETE FROM aluno WHERE aluno_codigo = ?";
            $stmt = $conn->prepare($sql_delete_aluno);
            $stmt->bind_param("i", $id);
            if($stmt->execute()) {
                // Exclusão bem-sucedida, redireciona de volta para a página de consulta de alunos
                header("Location: consulta_alunos.php");
                exit();
            } else {
                echo "Erro ao excluir aluno: " . $conn->error;
            }
        } else {
            echo "Erro ao excluir registros da tabela aluno_turma: " . $conn->error;
        }
    } else {
        echo "Erro ao excluir registros da tabela matricula: " . $conn->error;
    }
    // Saia da execução do script após a exclusão para evitar redirecionamento desnecessário
    exit();
}


$pasta_imagens = "../arquivos/";
?>





<!DOCTYPE html>
<html>
<head>
    <title>Consulta de Alunos</title>
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
        <h2>Consulta de Alunos</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <!-- Formulário de pesquisa -->
        <form method="post" action="">
            <input type="text" name="search" placeholder="Pesquisar por nome do aluno">
            <input type="submit" value="Pesquisar">
        </form>
       
        <div id="cabecalho">  
        <button id="adicionar">Adicionar Aluno<br><a href='cadastro_alunos.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../icons/add_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'></a></button>
        </div>
       

        <table>
            <tr>
                <th>Foto</th>
                <th>Nome</th>
                <th>Sexo</th>
                <th>Idade</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>RG</th>
                <th>CPF</th>
                <th>Data de Nascimento</th>
                <th>Endereço</th>                
                <th>Ações</th>                
            </tr>
            <?php
            // Exibe os alunos na tabela
            if ($result_alunos->num_rows > 0) {
                while($row = $result_alunos->fetch_assoc()) {
                echo "<tr>";
                echo "<td><img src='". $pasta_imagens .$row["aluno_foto_path"]."' alt='Foto do Aluno' style='width: 100px; height: 100px;'></td>"; // Exibe a foto do aluno
                echo "<td>".$row["aluno_nome"]."</td>";
                echo "<td>".$row["aluno_sexo"]."</td>";
                echo "<td>".$row["aluno_idade"]."</td>";
                echo "<td>".$row["aluno_email"]."</td>";
                echo "<td>".$row["aluno_telefone"]."</td>";
                echo "<td>".$row["aluno_rg"]."</td>";
                echo "<td>".$row["aluno_cpf"]."</td>";
                echo "<td>".date('d/m/Y', strtotime($row["aluno_dt_nascimento"]))."</td>";
                echo "<td>".$row["aluno_endereco"]."</td>";
               
                // Adicione links para editar ou excluir um aluno, se necessário
                echo "<td>                        
                    <a href='?delete_id=".$row["aluno_codigo"]."' onclick='return confirm(\"Tem certeza de que deseja excluir este aluno?\")' class='delete-link'>
                    <img src=\"../icons/delete_FILL0_wght400_GRAD0_opsz24.svg\" alt=\"\" style=\"width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;\"></a>
                    <a href='atualizar_alunos.php?id=".$row["aluno_codigo"]."' style='text-decoration: none; padding-left: 20%; font-weight: bold '>
                    <img src='../icons/edit_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'></a>
                    </td>";
                echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12'>Nenhum aluno encontrado</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
