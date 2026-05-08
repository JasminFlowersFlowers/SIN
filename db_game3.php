<?php
$host = "localhost";
$user = "root";
$pass = ""; // Por defecto en Laragon es vacío
$db   = "game3";


$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


// Si recibimos una puntuación vía POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['score'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $score = (int)$_POST['score'];


    $sql = "INSERT INTO scores (username, score) VALUES ('$username', $score)";
    $conn->query($sql);
    exit; // Termina la ejecución tras guardar
}


// Función para obtener el Ranking
function getHighScores($conn) {
    return $conn->query("SELECT username, score FROM scores ORDER BY score DESC LIMIT 10");
}
?>
