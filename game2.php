<?php include "db_game2.php"; ?>


<?php
if (isset($_POST["guardar"])) {
    $usuario = $_POST["usuario"] ?? "Anonimo";
    $puntos = $_POST["puntos"] ?? 0;


    $stmt = $pdo->prepare("INSERT INTO puntuaciones (usuario, puntos) VALUES (:u, :p)");
    $stmt->execute([
        "u" => $usuario,
        "p" => $puntos
    ]);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Arcade Game v2</title>


<style>
body {
    background: black;
    color: #00ffcc;
    font-family: monospace;
    text-align: center;
    overflow: hidden;
}


h1 {
    text-shadow: 0 0 10px #00ffcc;
}


#gameArea {
    position: relative;
    width: 100%;
    height: 60vh;
    border: 2px solid #00ffcc;
    margin-top: 20px;
}


#target {
    width: 60px;
    height: 60px;
    background: #ff0055;
    position: absolute;
    cursor: pointer;
    border-radius: 10px;
    box-shadow: 0 0 20px #ff0055;
    transition: transform 0.1s;
}


#target:active {
    transform: scale(0.8);
}


#hud {
    margin-top: 10px;
}


#timerBar {
    width: 100%;
    height: 10px;
    background: #333;
}


#timerFill {
    height: 10px;
    background: #00ffcc;
    width: 100%;
}


button {
    margin-top: 10px;
    padding: 10px;
    background: #00ffcc;
    border: none;
    cursor: pointer;
}
</style>
</head>


<body>


<h1>🎮 Arcade Click Game v2</h1>


<div id="hud">
    <span>Puntos: <strong id="score">0</strong></span> |
    <span>Tiempo: <strong id="time">20</strong>s</span>
</div>


<div id="timerBar">
    <div id="timerFill"></div>
</div>


<div id="gameArea">
    <div id="target"></div>
</div>


<form method="POST">
    <input type="text" name="usuario" placeholder="Tu nombre">
    <input type="hidden" name="puntos" id="hiddenScore">
    <button type="submit" name="guardar">Guardar puntuación</button>
</form>


<hr>


<h3>🏆 Ranking</h3>
<ul>
<?php
$stmt = $pdo->query("SELECT * FROM puntuaciones ORDER BY puntos DESC LIMIT 10");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<li>{$row['usuario']} - {$row['puntos']} pts</li>";
}
?>
</ul>


<script>
let score = 0;
let timeLeft = 20;
let gameActive = true;


const target = document.getElementById("target");
const gameArea = document.getElementById("gameArea");
const scoreText = document.getElementById("score");
const timeText = document.getElementById("time");
const hidden = document.getElementById("hiddenScore");
const timerFill = document.getElementById("timerFill");


function moveTarget() {
    if (!gameActive) return;


    const maxX = gameArea.clientWidth - 60;
    const maxY = gameArea.clientHeight - 60;


    const x = Math.random() * maxX;
    const y = Math.random() * maxY;


    target.style.left = x + "px";
    target.style.top = y + "px";
}


// Movimiento automático cada 1000ms
setInterval(moveTarget, 1000);


target.onclick = function () {
    if (!gameActive) return;


    score++;
    scoreText.innerText = score;
    hidden.value = score;
    moveTarget();
};


// Temporizador
const timer = setInterval(() => {
    timeLeft--;
    timeText.innerText = timeLeft;


    // barra
    timerFill.style.width = (timeLeft / 20 * 100) + "%";


    if (timeLeft <= 0) {
        clearInterval(timer);
        gameActive = false;
        target.style.display = "none";


        alert("Fin del juego. Puntuación: " + score);
    }
}, 1000);
</script>


</body>
</html>
