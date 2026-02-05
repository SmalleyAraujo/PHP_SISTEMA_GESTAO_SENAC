<?php
session_start();
require_once '../conexao.php';

if(isset($_GET['id'])) {
    $sala_id = $_GET['id'];

    // Aqui você pode incluir a lógica para excluir a sala do banco de dados usando o $sala_id
    $sql = "DELETE FROM sala WHERE sala_codigo = $sala_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Sala excluída com sucesso";
    } else {
        echo "Erro ao excluir sala: " . $conn->error;
    }
} else {
    echo "ID da sala não especificado.";
}

// Redirecionamento de volta para a página de consulta de salas
header("Location: consultar_sala.php");
exit();
?>
