<?php
session_start();
require_once '../../conexao.php';

// Função para excluir uma alocação do banco de dados
if (isset($_GET['delete_id'])) {
    $delete_id = filter_var($_GET['delete_id'], FILTER_SANITIZE_NUMBER_INT);
    $sql_delete = "DELETE FROM sala_turma WHERE sala_turma_codigo = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    
    if ($stmt_delete->execute()) {
        echo '<script>alert("Alocação excluída com sucesso!");</script>';
    } else {
        echo '<script>alert("Erro ao excluir alocação!");</script>';
    }
}

// Consultar todas as alocações de turmas em salas
$sql_todas_alocacoes = "SELECT st.sala_turma_codigo, t.turma_nome, t.turma_codigo, t.turma_turno, s.sala_nome, s.sala_codigo, COUNT(*) AS total_alunos
                        FROM sala_turma st
                        INNER JOIN turma t ON st.fk_turma_turma_codigo = t.turma_codigo
                        INNER JOIN sala s ON st.fk_sala_sala_codigo = s.sala_codigo
                        GROUP BY st.sala_turma_codigo, t.turma_nome, t.turma_codigo, t.turma_turno, s.sala_nome, s.sala_codigo";

$result_todas_alocacoes = $conn->query($sql_todas_alocacoes);

// Função para obter o total de alunos em uma turma
function obterTotalAlunos($turmaCodigo, $conn) {
    $sql_total_alunos = "SELECT COUNT(*) AS total_alunos FROM aluno_turma WHERE fk_turma_turma_codigo = ?";
    $stmt_total_alunos = $conn->prepare($sql_total_alunos);
    $stmt_total_alunos->bind_param("i", $turmaCodigo);
    $stmt_total_alunos->execute();
    $result_total_alunos = $stmt_total_alunos->get_result();
    $total_alunos = $result_total_alunos->fetch_assoc()['total_alunos'];
    return $total_alunos;
}

// Função para obter a capacidade máxima de uma sala
function obterCapacidadeSala($salaCodigo, $conn) {
    $sql_capacidade_sala = "SELECT sala_capacidade_alunos FROM sala WHERE sala_codigo = ?";
    $stmt_capacidade_sala = $conn->prepare($sql_capacidade_sala);
    $stmt_capacidade_sala->bind_param("i", $salaCodigo);
    $stmt_capacidade_sala->execute();
    $result_capacidade_sala = $stmt_capacidade_sala->get_result();
    $capacidade_sala = $result_capacidade_sala->fetch_assoc()['sala_capacidade_alunos'];
    return $capacidade_sala;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Consultar Alocação de Turma em Sala</title>
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
    <h2>Consultar Alocação de Turma em Sala</h2>

    <h3>Todas as Alocações de Turmas em Salas:</h3>
    <a href="../../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
    <div id="cabecalho">  
        <button id="adicionar">Criar Aloc.Turmas a Salas<br><a href='alocacaoSaT.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../../icons/add_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'></a></button>
    </div>
    
    <table>
        <tr>
            <th>Turma</th>
            <th>Turno</th>
            <th>Quant. Alunos</th>
            <th>Sala</th>
            <th>Capacidade</th>
            <th>Excluir</th>
        </tr>

        <?php
        if ($result_todas_alocacoes) {
            while ($row = $result_todas_alocacoes->fetch_assoc()) {
                $turma_codigo = $row["turma_codigo"];
                $sala_codigo = $row["sala_codigo"];

                $total_alunos = obterTotalAlunos($turma_codigo, $conn);
                $capacidade_sala = obterCapacidadeSala($sala_codigo, $conn);
                echo "<tr>";
                echo "<td>" . $row["turma_nome"] . "</td>";
                echo "<td>" . $row["turma_turno"] . "</td>";
                echo "<td>" . $total_alunos . "</td>";
                echo "<td>" . $row["sala_nome"] . "</td>";
                echo "<td>" . $capacidade_sala . "</td>";
                echo "<td><a href='?delete_id=" . $row["sala_turma_codigo"] . "' onclick='return confirm(\"Tem certeza de que deseja excluir esta alocação?\")'><img src=\"../../icons/delete_FILL0_wght400_GRAD0_opsz24.svg\" alt=\"\" style=\"width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;\"></a></td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
