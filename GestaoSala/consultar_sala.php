<?php
session_start();
require_once '../conexao.php'; 

// Consulta as salas no banco de dados
$sql = "SELECT * FROM sala";
$result = $conn->query($sql);

// Função para excluir sala
if(isset($_GET['delete_id'])) {
    $sala_id = $_GET['delete_id'];

    // Excluir associações da tabela de turmas
    $sql_delete_sala_turma = "DELETE FROM sala_turma WHERE fk_sala_sala_codigo = '$sala_id'";
    if($conn->query($sql_delete_sala_turma) === TRUE) {
        // Excluir associações da tabela de agendamento de aulas
        $sql_delete_agendamentos = "DELETE FROM agendamento_aula WHERE fk_sala_sala_codigo = '$sala_id'";
        if($conn->query($sql_delete_agendamentos) === TRUE) {
            // Excluir a sala
            $sql_delete_sala = "DELETE FROM sala WHERE sala_codigo = '$sala_id'";
            if($conn->query($sql_delete_sala) === TRUE) {
                // Exclusão bem-sucedida, redireciona para atualizar a página
                header("Location: consultar_sala.php");
                exit();
            } else {
                echo "Erro ao excluir sala: " . $conn->error;
            }
        } else {
            echo "Erro ao excluir agendamentos associados à sala: " . $conn->error;
        }
    } else {
        echo "Erro ao excluir associações de turmas associadas à sala: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consulta de Salas</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
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
        .expandir {
            cursor: pointer;
        }
        .agendamentos {
            display: none;
        }
        .acoes {
            white-space: nowrap;
        }
        .acoes button {
            margin-right: 5px;
        }
    </style>
    <script>
        function expandirAgendamentos(id) {
            var agendamentos = document.getElementById("agendamentos_" + id);
            if (agendamentos.style.display === "none") {
                agendamentos.style.display = "table-row";
            } else {
                agendamentos.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <h2>Consulta de Salas</h2>
    <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
    <table>
        <tr>            
            <th>Nome da Sala</th>
            <th>Endereço</th>
            <th>Tipo</th>
            <th>Situação</th>
            <th>Capacidade</th>
            <th>Recursos</th>
            <th>Agendamentos</th>
            <th class="acoes">Ações</th>
        </tr>
        <?php
        // Exibe as salas na tabela
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";               
                echo "<td>".$row["sala_nome"]."</td>";
                echo "<td>".$row["sala_endereco"]."</td>";
                echo "<td>".$row["sala_tipo"]."</td>";
                echo "<td>".$row["sala_situacao"]."</td>";
                echo "<td>".$row["sala_capacidade_alunos"]."</td>";
                echo "<td>".$row["sala_descricao"]."</td>";
                // Botão de expandir para os agendamentos
                echo "<td><span class='expandir' onclick='expandirAgendamentos(".$row["sala_codigo"].")'>▶️</span></td>";
                
                echo "<td>
                        <a href='agendamento_aulas_eventos.php?sala_codigo=".$row["sala_codigo"]."' style='text-decoration: none; padding-left: 1%; font-weight: bold '>
                            <img src='../icons/calendar_month_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>
                        </a>
                        <a href='?delete_id=".$row["sala_codigo"]."' onclick='return confirm(\"Tem certeza de que deseja excluir esta sala?\")' class='delete-link'>
                            <img src=\"../icons/delete_FILL0_wght400_GRAD0_opsz24.svg\" alt=\"\" style=\"width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;\">
                        </a>
                        <a href='atualizar_sala.php?id=".$row["sala_codigo"]."' style='text-decoration: none; padding-left: 1%; font-weight: bold '>
                                <img src='../icons/edit_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>
                        </a>
                     </td>";         
                echo "</tr>";
                // Exibir os agendamentos em uma linha oculta
                echo "<tr class='agendamentos' id='agendamentos_".$row["sala_codigo"]."' style='display: none;'>";
                echo "<td colspan='9'>";
                
                // Consulta os agendamentos para esta sala
                $sala_id = $row["sala_codigo"];
                $sql_agendamentos = "SELECT * FROM agendamento_aula WHERE fk_sala_sala_codigo = ?";
                $stmt = $conn->prepare($sql_agendamentos);
                $stmt->bind_param("i", $sala_id); // "i" indica que $sala_id é um inteiro
                $stmt->execute();
                
                $result_agendamentos = $stmt->get_result();

                if ($result_agendamentos->num_rows > 0) {
                    echo "<ul>";
                    while ($agendamento = $result_agendamentos->fetch_assoc()) {
                        echo "<li>".$agendamento['agendamento_aula_data_da_aula']." - ".$agendamento['agendamento_aula_hora_de_inicio']." - ".$agendamento['agendamento_aula_hora_de_termino']."</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "Nenhum agendamento";
                }
                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='9'>Nenhuma sala encontrada</td></tr>";
        }
        ?>
    </table>
</body>
</html>
