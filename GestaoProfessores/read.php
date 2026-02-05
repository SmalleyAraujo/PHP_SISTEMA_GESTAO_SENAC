<?php
include '../conexao.php';


//caminho para acessar imagens professor
$pasta_imagens = "../arquivos2/";
// Verifica se o ID de exclusão foi enviado
// Verifica se o ID de exclusão foi enviado
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Inicia uma transação
    $conn->begin_transaction();

    try {
        // Deleta de _professor_capacidade
        $sql_delete_capacidade = "DELETE FROM professor_capacidade WHERE fk_professor_professor_codigo = ?";
        $stmt_capacidade = $conn->prepare($sql_delete_capacidade);
        $stmt_capacidade->bind_param("i", $id);
        if (!$stmt_capacidade->execute()) {
            throw new Exception("Erro ao excluir vínculo em professor_capacidade: " . $stmt_capacidade->error);
        }

        // Deleta de outras tabelas relacionadas aqui, se necessário
        // Exemplo: Deleta de _outra_tabela
        // $sql_delete_outra_tabela = "DELETE FROM _outra_tabela WHERE fk_professor_professor_codigo = ?";
        // $stmt_outra_tabela = $conn->prepare($sql_delete_outra_tabela);
        // $stmt_outra_tabela->bind_param("i", $id);
        // if (!$stmt_outra_tabela->execute()) {
        //     throw new Exception("Erro ao excluir vínculo em _outra_tabela: " . $stmt_outra_tabela->error);
        // }

        // Deleta o professor
        $sql_delete_professor = "DELETE FROM professor WHERE professor_codigo = ?";
        $stmt_professor = $conn->prepare($sql_delete_professor);
        $stmt_professor->bind_param("i", $id);
        if (!$stmt_professor->execute()) {
            throw new Exception("Erro ao excluir professor: " . $stmt_professor->error);
        }

        // Se tudo correr bem, confirma a transação
        $conn->commit();

        // Redireciona de volta para a página de consulta de professores
        header("Location: read.php");
        exit();
    } catch (Exception $e) {
        // Em caso de erro, reverte a transação
        $conn->rollback();
        echo "Erro ao excluir professor e seus vínculos: " . $e->getMessage();
    }
}

// Query para selecionar todos os professores
$sql = "SELECT * FROM professor";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consulta de Professores</title>
    <style>
        body {
            background: rgb(238,174,202);
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(113,20,244,1) 0%, rgba(141,126,219,1) 0%, rgba(138,126,219,1) 82%);
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
        }
        /* Estilo para o cabeçalho */
        header {
            background-color: #247D9E;
            color: #fff;
            padding: 10px 0;
            position: sticky;
            top: 0;
            z-index: 1000; /* Garante que o cabeçalho fique acima de outros elementos */
        }
        .container {
            width: 90%;
            margin: 0 auto; /* Centraliza horizontalmente */                    
        }
        table {
            width: auto;
            border-collapse: collapse;
            margin: 20px auto;
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
        tr:nth-child(even) {
            background-color: #4d648d; /* Cor de fundo das linhas pares */
        }
        tr:nth-child(odd) {
            background-color: #3d5275; /* Cor de fundo das linhas ímpares */
        }
        .delete-link {
            color: red;
            text-decoration: none;
        }
        .delete-link:hover {
            text-decoration: underline;
        }
        /* Estilo para o botão Consultar */
        #adicionar {
            background-color: #247D9E;
            color: #fff;
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #cabecalho {
            display: flex;
            justify-content: end;
            padding-right: 10%;
        }
        #acoes {
            width: 7%;
        }
        .foto {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Consulta de Professores</h2>
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold;"><img src='../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a>

        <table>
            <tr>
                <th>Foto</th> <!-- Cabeçalho para a coluna da foto -->
                <th>Nome</th>
                <th>Email</th>
                <th>CPF</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Sexo</th>
                <th>Data de Admissão</th>
                <th>Data de Desligamento</th>
                <th>Titulação</th>
                <th>Data de Nascimento</th>
                <th>URL do Currículo Lattes</th>
                <th>Disponibilidade Segunda Manhã</th>
                <th>Disponibilidade Segunda Tarde</th>
                <th>Disponibilidade Segunda Noite</th>
                <th>Disponibilidade Terça Manhã</th>
                <th>Disponibilidade Terça Tarde</th>
                <th>Disponibilidade Terça Noite</th>
                <th>Disponibilidade Quarta Manhã</th>
                <th>Disponibilidade Quarta Tarde</th>
                <th>Disponibilidade Quarta Noite</th>
                <th>Disponibilidade Quinta Manhã</th>
                <th>Disponibilidade Quinta Tarde</th>
                <th>Disponibilidade Quinta Noite</th>
                <th>Disponibilidade Sexta Manhã</th>
                <th>Disponibilidade Sexta Tarde</th>
                <th>Disponibilidade Sexta Noite</th>
                <th>Disponibilidade Sábado Manhã</th>
                <th>Disponibilidade Sábado Tarde</th>
                <th>Disponibilidade Sábado Noite</th>
                <th id="acoes">Ações</th>
                <th id="acoes">Capacidade</th>
            </tr>
            <?php
        // Exibe os professores na tabela
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='". $pasta_imagens .$row["professor_foto_path"]."' target='_blank'><img src='". $pasta_imagens .$row["professor_foto_path"]."' alt='Foto do Professor' class='foto'></a></td>"; // Adiciona o link em torno da imagem
                echo "<td>".$row["professor_nome"]."</td>";
                echo "<td>".$row["professor_email"]."</td>";
                echo "<td>".$row["professor_cpf"]."</td>";
                echo "<td>".$row["professor_endereco"]."</td>";
                echo "<td>".$row["professor_telefone"]."</td>";
                echo "<td>".$row["professor_sexo"]."</td>";
                echo "<td>".$row["professor_data_admissao"]."</td>";
                echo "<td>".$row["professor_data_desligamento"]."</td>";
                echo "<td>".$row["professor_titulacao"]."</td>";
                echo "<td>".$row["professor_data_nascimento"]."</td>";
                echo "<td>".$row["professor_url_curriculo_lattes"]."</td>";
                echo "<td>".$row["professor_disponibilidade_segunda_manha"]."</td>";
                echo "<td>".$row["professor_disponibilidade_segunda_tarde"]."</td>";
                echo "<td>".$row["professor_disponibilidade_segunda_noite"]."</td>";
                echo "<td>".$row["professor_disponibilidade_terca_manha"]."</td>";
                echo "<td>".$row["professor_disponibilidade_terca_tarde"]."</td>";
                echo "<td>".$row["professor_disponibilidade_terca_noite"]."</td>";
                echo "<td>".$row["professor_disponibilidade_quarta_manha"]."</td>";
                echo "<td>".$row["professor_disponibilidade_quarta_tarde"]."</td>";
                echo "<td>".$row["professor_disponibilidade_quarta_noite"]."</td>";
                echo "<td>".$row["professor_disponibilidade_quinta_manha"]."</td>";
                echo "<td>".$row["professor_disponibilidade_quinta_tarde"]."</td>";
                echo "<td>".$row["professor_disponibilidade_quinta_noite"]."</td>";
                echo "<td>".$row["professor_disponibilidade_sexta_manha"]."</td>";
                echo "<td>".$row["professor_disponibilidade_sexta_tarde"]."</td>";
                echo "<td>".$row["professor_disponibilidade_sexta_noite"]."</td>";
                echo "<td>".$row["professor_disponibilidade_sabado_manha"]."</td>";
                echo "<td>".$row["professor_disponibilidade_sabado_tarde"]."</td>";
                echo "<td>".$row["professor_disponibilidade_sabado_noite"]."</td>";
                echo "<td>
                    <a href='?delete_id=".$row["professor_codigo"]."' onclick='return confirm(\"Tem certeza de que deseja excluir este professor?\")' class='delete-link'>
                        <img src=\"../icons/delete_FILL0_wght400_GRAD0_opsz24.svg\" alt=\"Excluir\" style=\"width: 35px; cursor: pointer;\">
                    </a>
                    <a href='update.php?id=".$row["professor_codigo"]."' style='text-decoration: none; font-weight: bold '>
                        <img src='../icons/edit_FILL0_wght400_GRAD0_opsz24.svg' alt='Editar' style='width: 35px; cursor: pointer;'>
                    </a>
                    
                    </td>";

                

                echo "<td>
                <a href='capacidade.php?id=".$row["professor_codigo"]."' style='text-decoration: none; font-weight: bold '>
                    <img src='../icons/assignment_add_24dp_FILL0_wght400_GRAD0_opsz24.svg' alt='Editar' style='width: 35px; cursor: pointer;'>
                </a>
                <a href='consultaVinculos.php?id=".$row["professor_codigo"]."' style='text-decoration: none; font-weight: bold '>
                    <img src='../icons/search_FILL0_wght400_GRAD0_opsz24.svg' alt='Editar' style='width: 35px; cursor: pointer;'>
                </a>

                </td>";

             echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='31'>Nenhum professor encontrado</td></tr>";
        }
        ?>

        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
