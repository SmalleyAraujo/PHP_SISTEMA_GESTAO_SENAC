<?php
session_start();
require_once '../../conexao.php';

// Verifica se o código da turma está presente na URL
if(isset($_GET['codigo'])) {
    // Obtém o código da turma da URL
    $turma_codigo = $_GET['codigo'];
    
    // Consulta SQL para obter o nome da turma
    $sql_turma_nome = "SELECT turma_nome FROM turma WHERE turma_codigo = $turma_codigo";
    $result_turma_nome = $conn->query($sql_turma_nome);
    
    // Verifica se a consulta retornou resultados
    if ($result_turma_nome) {
        if ($result_turma_nome->num_rows > 0) {
            // Obtém o nome da turma
            $row = $result_turma_nome->fetch_assoc();
            $turma_nome = $row['turma_nome'];
        } else {
            echo "Turma não encontrada.";
            exit(); // Encerra o script se a turma não for encontrada
        }
    } else {
        echo "Erro ao consultar o nome da turma: " . $conn->error;
        exit();
    }
    
    
    // Consulta SQL para obter a alocação de sala para a turma
    $sql_alocacao_sala = "SELECT s.sala_nome, s.sala_capacidade_alunos 
                          FROM sala_turma st
                          INNER JOIN sala s ON st.fk_sala_sala_codigo = s.sala_codigo
                          WHERE st.fk_turma_turma_codigo = $turma_codigo";

    // Executar a consulta
    $result_alocacao_sala = $conn->query($sql_alocacao_sala);

    // Verificar se há resultados
    if ($result_alocacao_sala) {
        if ($result_alocacao_sala->num_rows > 0) {
            // Obter os dados da sala
            $row_sala = $result_alocacao_sala->fetch_assoc();
            $sala_nome = $row_sala["sala_nome"];
            $capacidade_alunos = $row_sala["sala_capacidade_alunos"];
        } else {
            echo "Nenhuma alocação de sala encontrada para esta turma.";
            echo "<script>setTimeout(function(){ window.location.href = 'consultaTSemestre.php'; }, 2000);</script>";
            exit();
        }
    } else {
        echo "Erro ao consultar a alocação de sala: " . $conn->error;
        exit();
    }


    // Consulta SQL para obter os alunos
    $sql_alunos = "SELECT aluno_codigo, aluno_nome FROM aluno";
    $result_alunos = $conn->query($sql_alunos);

    // Verifica se a consulta foi executada com sucesso
    if (!$result_alunos) {
        echo "Erro ao consultar os alunos: " . $conn->error;
        exit();
    }

  
    
    // Consulta SQL para obter a alocação de sala para a turma
    $sql_alocacao_sala = "SELECT s.sala_nome, s.sala_capacidade_alunos 
                          FROM sala_turma st
                          INNER JOIN sala s ON st.fk_sala_sala_codigo = s.sala_codigo
                          WHERE st.fk_turma_turma_codigo = $turma_codigo";

    // Executar a consulta
    $result_alocacao_sala = $conn->query($sql_alocacao_sala);

    // Verificar se há resultados
    if ($result_alocacao_sala) {
        if ($result_alocacao_sala->num_rows > 0) {
            // Obter os dados da sala
            $row_sala = $result_alocacao_sala->fetch_assoc();
            $sala_nome = $row_sala["sala_nome"];
            $capacidade_alunos = $row_sala["sala_capacidade_alunos"];
        } else {
            echo "Nenhuma alocação de sala encontrada para esta turma.";                                       
            exit();
        }
    } else {
        echo "Erro ao consultar a alocação de sala: " . $conn->error;
        exit();
    }


    // Consulta SQL para obter os alunos já associados à turma
    $sql_alunos_turma = "SELECT COUNT(*) AS total_alunos FROM aluno_turma WHERE fk_turma_turma_codigo = $turma_codigo";
    $result_alunos_turma = $conn->query($sql_alunos_turma);

    // Verifica se a consulta foi executada com sucesso
    if ($result_alunos_turma) {
        $row_alunos_turma = $result_alunos_turma->fetch_assoc();
        $total_alunos_turma = $row_alunos_turma['total_alunos'];
    } else {
        echo "Erro ao consultar o total de alunos na turma: " . $conn->error;
        exit();
    }


    // Consulta SQL para obter os alunos
    $sql_alunos = "SELECT aluno_codigo, aluno_nome FROM aluno";
    $result_alunos = $conn->query($sql_alunos);

    // Verifica se a consulta foi executada com sucesso
    if (!$result_alunos) {
        echo "Erro ao consultar os alunos: " . $conn->error;
        exit();
    }
}

 // Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os alunos foram selecionados
    if(isset($_POST['alunos'])) {
        // Obtém os dados do formulário
        $alunos_selecionados = $_POST['alunos'];
        $turma_codigo = $_POST['turma'];
        
        // Calcula o total de alunos selecionados
        $total_alunos_selecionados = count($alunos_selecionados);
        
        // Verifica se a quantidade total de alunos na turma mais os alunos selecionados excede a capacidade da sala
        if (($total_alunos_turma + $total_alunos_selecionados) > $capacidade_alunos) {
            echo "A quantidade total de alunos na turma excede a capacidade da sala.";
        } else {
            // Loop para inserir cada aluno na turma
            foreach ($alunos_selecionados as $aluno_codigo) {
                // Insere os dados na tabela de associação aluno_turma
                $sql = "INSERT INTO aluno_turma (fk_aluno_aluno_codigo, fk_turma_turma_codigo) VALUES ('$aluno_codigo', '$turma_codigo')";
                
                if ($conn->query($sql) !== TRUE) {
                    echo "Erro ao alocar aluno à turma: " . $conn->error;
                }
            }
            echo "Aluno(s) alocado(s) à turma com sucesso!";
        }
    } else {
        echo "Nenhum aluno selecionado.";
    }
    
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Alocação de Alunos a Turmas</title>
    <style>
        body {
            background: rgb(238, 174, 202);
            background: radial-gradient(circle, rgba(238, 174, 202, 1) 0%, rgba(113, 20, 244, 1) 0%, rgba(141, 126, 219, 1) 0%, rgba(138, 126, 219, 1) 82%);
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            padding-top: 50px; /* Adiciona espaço acima do formulário */
            margin: 0;
        }

        .container {
            width: 30%;
            margin: 0 auto;
            background-color: #247D9E;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: white;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;   
            padding: 5px;                   
        }
        
        
        select, input[type="submit"] {
            width: 100%;
            background-color: #3d5275;
            color: #fff;
            border: 1px solid #666666;
            border-radius: 4px;
            padding: 8px;
            font-size: 16px;
            margin-bottom: 10px;
            display: inline-block;
            transition: all 0.3s;
        }

        select:focus, input[type="submit"]:focus {
            outline: none;
        }

        input[type="submit"]:hover {
            background-color: #666666;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            cursor: pointer;
        }

        input[type="submit"]:active {
            background-color: #4a4a4a;
        }

    </style>
</head>
<body>
    <div class="container">
        
        <a href="../pag-inicial.html" style="text-decoration: none; font-weight: bold "><img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
        <a href="consultaTSemestre.php" style='text-decoration: none; font-weight: bold;  padding-right: 60%;'>
            <img src='../../icons/arrow_back_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; border-radius:100%; box-shadow: 5px 5px 10px white; cursor: pointer;'> Voltar</a><br><br>

        <h2>Nome da Turma: <?php echo isset($turma_nome) ? $turma_nome : ''; ?></h2>
        <h2>Sala: <?php echo isset($sala_nome) ? $sala_nome : ''; ?></h2>
        <h2>Capacidade da Sala: <?php echo isset($capacidade_alunos) ? $capacidade_alunos : ''; ?> alunos</h2>
        <h2>Total de Alunos na Turma: <?php echo isset($total_alunos_turma) ? $total_alunos_turma : ''; ?></h2>

        <form action="" method="POST">
            <h2>Selecione os Alunos:</h2>
            <?php
            if ($result_alunos->num_rows > 0) {
                while($row = $result_alunos->fetch_assoc()) {
                    echo "<div class='checkbox-container'>";
                    echo "<input type='checkbox' id='checkbox' name='alunos[]' value='" . $row['aluno_codigo'] . "'>";
                    echo "<label for='checkbox'>" . $row['aluno_nome'] . "</label>";
                    echo "</div>";
                }
                
            } else {
                echo "0 resultados";
            }
            ?>
            <br>

            <!-- Use um input hidden para enviar o código da turma -->
            <input type="hidden" name="turma" value="<?php echo $turma_codigo; ?>">

            <input type="submit" value="Alocar Aluno(s)">
        </form>

        <?php
        // Fecha a conexão
        $conn->close();
        ?>
    </div>
   
</body>
</html>
