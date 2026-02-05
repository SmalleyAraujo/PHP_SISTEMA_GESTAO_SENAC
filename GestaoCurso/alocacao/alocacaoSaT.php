<?php
session_start(); 
require_once '../../conexao.php';

$sql_sala = "SELECT sala_codigo, sala_nome FROM sala";
$result_sala = $conn->query($sql_sala);

$sql_turma = "SELECT turma_codigo, turma_nome FROM turma";
$result_turma = $conn->query($sql_turma);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Selecionar Sala e Turma</title>
    <style>
        body {
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(113,20,244,1) 0%, rgba(141,126,219,1) 0%, rgba(138,126,219,1) 82%);
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .container {
            width: 40%;
            margin: 0 auto;
            background-color: #247D9E;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: white;
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
            background-color: #4a4a4a;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            cursor: pointer;
        }
        input[type="submit"]:active {
            background-color: #666666;
        }
        #cabecalho{
            display: flex;
            justify-content: space-around;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Alocação de Salas a Turmas:</h1>
        <div id="cabecalho">        
            <a href="../../pag-inicial.html" style="text-decoration: none; padding-left: 20%; font-weight: bold "><img src='../../icons/home_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'> Menu principal</a><br><br>
            <a href='consultarAlocacaoSaT.php' style='text-decoration: none; padding-left: 5%; font-weight: bold '><img src='../../icons/search_FILL0_wght400_GRAD0_opsz24.svg' alt='' style='width: 35px; right: 35px; box-shadow: 5px 5px 10px violet; cursor: pointer;'>Pesquisar</a>
        </div>
        <h2>Selecione uma Sala:</h2>
        <form action="" method="POST">
            <select name="sala">
                <?php
                if ($result_sala->num_rows > 0) {
                    while($row = $result_sala->fetch_assoc()) {
                        echo "<option value='" . $row['sala_codigo'] . "'>" . $row['sala_nome'] . "</option>";
                    }
                } else {
                    echo "<option disabled selected>Nenhuma sala disponível</option>";
                }
                ?>
            </select>
            <br><br>
            <h2>Selecione uma Turma:</h2>
            <select name="turma">
                <?php
                if ($result_turma->num_rows > 0) {
                    while($row = $result_turma->fetch_assoc()) {
                        echo "<option value='" . $row['turma_codigo'] . "'>" . $row['turma_nome'] . "</option>";
                    }
                } else {
                    echo "<option disabled selected>Nenhuma turma disponível</option>";
                }
                ?>
            </select>
            <br><br>
            <input type="submit" value="INCLUIR">
        </form>
        
    </div>
</body>
</html>

<?php

require_once '../../conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $turma_codigo = $_POST['turma'];
    $sala_codigo = $_POST['sala'];

    // Insere uma nova associação entre turma e sala
    $sql = "INSERT INTO sala_turma (fk_turma_turma_codigo, fk_sala_sala_codigo) VALUES ('$turma_codigo', '$sala_codigo')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Turma alocada na sala com sucesso!";
    } else {
        echo "Erro ao alocar a turma na sala: " . $conn->error;
    }
}

?>

<?php
$conn->close();
?>


