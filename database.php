<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = ""; // Senha padrão do MySQL no XAMPP é uma string vazia
$dbname = "carnes_db"; // Nome do banco de dados

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
