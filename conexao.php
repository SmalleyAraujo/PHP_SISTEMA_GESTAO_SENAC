<?php
// Conexão com o banco de dados
$servername = "localhost:3307";
$username = "root";
$password = "senac";
$dbname = "senacbd";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

?>