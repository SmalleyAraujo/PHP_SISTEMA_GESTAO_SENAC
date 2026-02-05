<?php
session_start(); 
require_once '../../conexao.php'; 

// Verifica se o parâmetro de turma foi passado via URL
if(isset($_GET['codigo'])) {
    // Obtém o código da turma da URL
    $turma_codigo = $_GET['codigo'];

    // Consulta SQL para obter os detalhes da turma específica
    $sql_turma = "SELECT * FROM turma WHERE turma_codigo = '$turma_codigo'";
    $result_turma = $conn->query($sql_turma);
    
    // Consulta SQL para obter os alunos alocados nesta turma específica, incluindo o código do relacionamento
    //Consulta SQL para obter os alunos alocados nesta turma específica, incluindo o código do relacionamento e ordenados por nome
    $sql_alunos = "SELECT aluno.*, aluno_turma.aluno_turma_codigo 
    FROM aluno_turma 
    INNER JOIN aluno ON aluno_turma.fk_aluno_aluno_codigo = aluno.aluno_codigo 
    WHERE aluno_turma.fk_turma_turma_codigo = '$turma_codigo'
    ORDER BY aluno.aluno_nome";


    $result_alunos = $conn->query($sql_alunos);

    // Verifica se a turma foi encontrada
    if ($result_turma->num_rows > 0) {
        // Obtém os detalhes da turma
        $turma = $result_turma->fetch_assoc();
    } else {
        // Se a turma não for encontrada, redireciona para uma página de erro ou exibe uma mensagem
        echo "Turma não encontrada";
        exit();
    }
} else {
    // Se o parâmetro de turma não foi passado, redireciona para uma página de erro ou exibe uma mensagem
    echo "Turma não especificada";
    exit();
}


// Função para excluir a alocação de um aluno à turma
if(isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    // Armazena o código do relacionamento em uma variável
    $aluno_turma_codigo = $id;
    $sql_delete = "DELETE FROM aluno_turma WHERE aluno_turma_codigo = '$aluno_turma_codigo'";

    if($conn->query($sql_delete) === TRUE) {
        // Exclusão bem-sucedida, redirecionar para a mesma página
        header("Location: consultaTSemestre.php");
        exit(); // Encerra o script PHP para evitar a execução de mais código desnecessário
    } else {
        echo '<script>alert("Erro ao excluir alocação!");</script>';
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Alunos Alocados na Turma <?php echo $turma['turma_nome']; ?></title>
    <style>
       /* Estilos Gerais */
body {
    background: rgb(238,174,202);
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(113,20,244,1) 0%, rgba(141,126,219,1) 0%, rgba(138,126,219,1) 82%);
            
}

.container {
    width: 80%;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
}

h2 {
    color: #333;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    color: #0056b3;
}

/* Tabela de Alunos */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #f2f2f2;
}

th, td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

th {
    background-color: #007bff;
    color: #fff;
}

tr:nth-child(even) {
    background-color: #dddddd;
}

tr:hover {
    background-color: #f2f2f2;
}

/* Botões */
input[type="submit"], #adicionar {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover, #adicionar:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Cabeçalho do Botão */
#cabecalho {
    display: flex;
    justify-content: flex-end;
    padding-right: 10%;
}

/* Foto do Aluno */
.aluno-foto {
    width: 100px;
    height: 100px;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Alunos Alocados na Turma <?php echo $turma['turma_nome']; ?></h2>
        <a href="../../pag-inicial.html" style="text-decoration: none; font-weight: bold ">
            <img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal
        </a><br><br>
        <a href="consultaTSemestre.php" style='text-decoration: none; font-weight: bold;  padding-right: 60%;'>
            <img src='../../icons/arrow_back_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; border-radius:100%; box-shadow: 5px 5px 10px white; cursor: pointer;'> Voltar</a><br><br>

        <!-- Exibir os detalhes da turma -->
        <h3>Detalhes da Turma</h3>
        <p>Nome da Turma: <?php echo $turma['turma_nome']; ?></p>
        <p>Ano: <?php echo $turma['turma_ano']; ?></p>
        <p>Semestre: <?php echo $turma['turma_semestre']; ?></p>
        <p>Estado: <?php echo $turma['turma_estado']; ?></p>
        <p>Turno: <?php echo $turma['turma_turno']; ?></p>
        
        <!-- Exibir a lista de alunos alocados na turma -->
        <h3>Alunos Alocados</h3>
        <?php if ($result_alunos->num_rows > 0) : ?>
            <table>
                <tr>
                    <!-- <th>Codigo do relacionamento</th> -->
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
                <?php while ($row = $result_alunos->fetch_assoc()) : ?>
                    <tr>
                        <!-- Exibir os detalhes dos alunos -->
                        <!-- <td><php echo $row["aluno_turma_codigo"]; ></td> -->
                        <td><img src='../../arquivos/<?php echo $row["aluno_foto_path"]; ?>' alt='Foto do Aluno' class='aluno-foto'></td>
                        <td><?php echo $row["aluno_nome"]; ?></td>
                        <td><?php echo $row["aluno_sexo"]; ?></td>
                        <td><?php echo $row["aluno_idade"]; ?></td>
                        <td><?php echo $row["aluno_email"]; ?></td>
                        <td><?php echo $row["aluno_telefone"]; ?></td>
                        <td><?php echo $row["aluno_rg"]; ?></td>
                        <td><?php echo $row["aluno_cpf"]; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row["aluno_dt_nascimento"])); ?></td>
                        <td><?php echo $row["aluno_endereco"]; ?></td>
                        <td>
                            <!-- Adiciona um formulário para enviar o código do relacionamento para o PHP -->
                            <form method="post">
                                <input type="hidden" name="delete_id" value="<?php echo $row["aluno_turma_codigo"]; ?>">
                                <button type="submit" onclick="return confirm('Tem certeza de que deseja excluir esta associação?')">
                                    <img src="../../icons/delete_FILL0_wght400_GRAD0_opsz24.svg" alt="" style="width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;">
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>Nenhum aluno alocado nesta turma.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
