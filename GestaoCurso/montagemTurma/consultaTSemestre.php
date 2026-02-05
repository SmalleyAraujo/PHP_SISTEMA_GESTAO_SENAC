


<?php
session_start(); 
require_once '../../conexao.php'; 

// Consulta SQL para obter os semestres distintos das turmas
$sql_semestres = "SELECT DISTINCT turma_semestre FROM turma";
$result_semestres = $conn->query($sql_semestres);

// Consulta SQL para obter os anos distintos das turmas
$sql_anos = "SELECT DISTINCT turma_ano FROM turma";
$result_anos = $conn->query($sql_anos);

// Consulta SQL para obter os estados distintos das turmas
$sql_estados = "SELECT DISTINCT turma_estado FROM turma";
$result_estados = $conn->query($sql_estados);

// Consulta SQL para obter os turnos distintos das turmas
$sql_turnos = "SELECT DISTINCT turma_turno FROM turma";
$result_turnos = $conn->query($sql_turnos);

// Definir uma consulta SQL padrão para exibir todas as turmas
$sql_default = "SELECT turma.*, curso.curso_nome FROM turma INNER JOIN curso ON turma.fk_curso_curso_codigo = curso.curso_codigo";
$result_default = $conn->query($sql_default);

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário fk_curso_curso_codigo
    $turma_semestre = $_POST['turma_semestre'];
    $turma_ano = $_POST['turma_ano'];
    $turma_estado = $_POST['turma_estado'];
    $turma_turno = $_POST['turma_turno'];
    
    // Monta a consulta SQL dinamicamente de acordo com os filtros selecionados
    $sql = "SELECT turma.*, curso.curso_nome FROM turma INNER JOIN curso ON turma.fk_curso_curso_codigo = curso.curso_codigo WHERE 1=1"; // Começa com 1=1 para adicionar condições de filtro dinamicamente
    
    if (!empty($turma_semestre)) {
        $sql .= " AND turma_semestre = '$turma_semestre'";
    }
    if (!empty($turma_ano)) {
        $sql .= " AND turma_ano = '$turma_ano'";
    }
    if (!empty($turma_estado)) {
        $sql .= " AND turma_estado = '$turma_estado'";
    }
    if (!empty($turma_turno)) {
        $sql .= " AND turma_turno = '$turma_turno'";
    }
    
    // Executa a consulta
    $result = $conn->query($sql);
} else {
    // Se o formulário não foi submetido, use a consulta padrão para exibir todas as turmas
    $result = $result_default;
}

// Função para excluir uma turma
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Verifica se há associações de alunos com a turma
    $sql_check_aluno_turma = "SELECT * FROM aluno_turma WHERE fk_turma_turma_codigo = '$id'";
    $result_check_aluno_turma = $conn->query($sql_check_aluno_turma);

    if($result_check_aluno_turma->num_rows > 0) {
        // Se houver associações de alunos, exclua essas associações primeiro
        $sql_delete_aluno_turma = "DELETE FROM aluno_turma WHERE fk_turma_turma_codigo = '$id'";
        if($conn->query($sql_delete_aluno_turma) === TRUE) {
            // Após excluir as associações de aluno, exclua as associações de sala e turma
            $sql_delete_sala_turma = "DELETE FROM sala_turma WHERE fk_turma_turma_codigo = '$id'";
            if($conn->query($sql_delete_sala_turma) === TRUE) {
                // Após excluir as associações de sala e turma, exclua a turma
                $sql_delete_turma = "DELETE FROM turma WHERE turma_codigo = '$id'";
                if($conn->query($sql_delete_turma) === TRUE) {
                    // Exclusão bem-sucedida, recarrega a página para atualizar a tabela
                    header("Location: consultaTSemestre.php");
                    echo '<script>alert("Turma excluída com sucesso!");</script>';
                    echo '<script>window.location.href = "consultaTSemestre.php";</script>';
                    exit(); // Encerra o script PHP para evitar a execução de mais código desnecessário
                } else {
                    echo "Erro ao excluir turma: " . $conn->error;
                }
            } else {
                echo "Erro ao excluir associações de sala e turma: " . $conn->error;
            }
        } else {
            echo "Erro ao excluir associações de aluno e turma: " . $conn->error;
        }
    } else {
        // Se não houver associações de alunos, exclua diretamente as associações de sala e turma
        $sql_delete_sala_turma = "DELETE FROM sala_turma WHERE fk_turma_turma_codigo = '$id'";
        if($conn->query($sql_delete_sala_turma) === TRUE) {
            // Após excluir as associações de sala e turma, exclua a turma
            $sql_delete_turma = "DELETE FROM turma WHERE turma_codigo = '$id'";
            if($conn->query($sql_delete_turma) === TRUE) {
                // Exclusão bem-sucedida, recarrega a página para atualizar a tabela
                header("Location: consultaTSemestre.php");
                echo '<script>alert("Turma excluída com sucesso!");</script>';
                echo '<script>window.location.href = "consultaTSemestre.php";</script>';
                exit(); // Encerra o script PHP para evitar a execução de mais código desnecessário
            } else {
                echo "Erro ao excluir turma: " . $conn->error;
            }
        } else {
            echo "Erro ao excluir associações de sala e turma: " . $conn->error;
        }
    }
}



?>

<!DOCTYPE html>
<html>
<head>
    <title>Consulta de Turmas</title>
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
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            color: #fff;
        }
        th {
            background-color: #247D9E; /* Cor de fundo do cabeçalho */
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
    <div class="container">
        <h2>Consulta de Turmas por Semestre</h2>
        <a href="../../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="turma_semestre">Semestre:</label>
            <select id="turma_semestre" name="turma_semestre">
                <option value="">Selecione o Semestre</option>
                <?php while ($row = $result_semestres->fetch_assoc()) : ?>
                    <option value="<?php echo $row['turma_semestre']; ?>"><?php echo $row['turma_semestre']; ?></option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <label for="turma_ano">Ano:</label>
            <select id="turma_ano" name="turma_ano">
                <option value="">Selecione o Ano</option>
                <?php while ($row = $result_anos->fetch_assoc()) : ?>
                    <option value="<?php echo $row['turma_ano']; ?>"><?php echo $row['turma_ano']; ?></option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <label for="turma_estado">Estado:</label>
            <select id="turma_estado" name="turma_estado">
                <option value="">Selecione o Estado</option>
                <?php while ($row = $result_estados->fetch_assoc()) : ?>
                    <option value="<?php echo $row['turma_estado']; ?>"><?php echo $row['turma_estado']; ?></option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <label for="turma_turno">Turno:</label>
            <select id="turma_turno" name="turma_turno">
                <option value="">Selecione o Turno</option>
                <?php while ($row = $result_turnos->fetch_assoc()) : ?>
                    <option value="<?php echo $row['turma_turno']; ?>"><?php echo $row['turma_turno']; ?></option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <input type="submit" value="Consultar">
           
        </form>
        <div id="cabecalho">  
        <button id="adicionar">Criar Turma<br><a href='montagemTSemestre.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../../icons/add_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'></a></button>
        </div>           
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>Curso</th>
                    <th>Nome da Turma</th>
                    <th>Ano</th>
                    <th>Semestre</th>
                    <th>Estado</th>
                    <th>Turno</th>
                    <th>Total Alunos</th>
                    <th>Ações</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row["curso_nome"]; ?></td>
                        <td><?php echo $row["turma_nome"]; ?></td>
                        <td><?php echo $row["turma_ano"]; ?></td>
                        <td><?php echo $row["turma_semestre"]; ?></td>
                        <td><?php echo $row["turma_estado"]; ?></td>
                        <td><?php echo $row["turma_turno"]; ?></td>
                        <td><?php 
                            // Consulta SQL para obter o total de alunos alocados nesta turma
                            $turma_codigo = $row["turma_codigo"];
                            $sql_total_alunos = "SELECT COUNT(*) AS total_alunos FROM aluno_turma WHERE fk_turma_turma_codigo = '$turma_codigo'";
                            $result_total_alunos = $conn->query($sql_total_alunos);
                            $total_alunos = $result_total_alunos->fetch_assoc()['total_alunos'];
                            echo $total_alunos;
                        ?>
                          
                          <a href='consultarTAlunos.php?codigo=<?php echo $row["turma_codigo"]; ?>' style='text-decoration: none; padding-left: 20%; font-weight: bold '>
                                <img src='../../icons/search_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>
                            </a>
                        
                        </td>


                        <td>                            
                            <a href='associacaoAlunoTurma.php?codigo=<?php echo $row["turma_codigo"]; ?>' style='text-decoration: none; padding-left: 5%; font-weight: bold '>
                                <img src='../../icons/person_add_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>
                            </a>
                            <a href='?delete_id=<?php echo $row["turma_codigo"]; ?>' onclick='return confirm("Tem certeza de que deseja excluir esta turma?")'>
                                <img src="../../icons/delete_FILL0_wght400_GRAD0_opsz24.svg" alt="" style="width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;">
                            </a>
                            <a href='alterarTSemestre.php?codigo=<?php echo $row["turma_codigo"]; ?>' style='text-decoration: none; padding-left: 5%; font-weight: bold '>
                                <img src='../../icons/edit_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>Nenhuma turma encontrada.</p>
        <?php endif; ?>
    </div>
    
</body>
</html>

<?php
$conn->close();
?>

