<?php include "db_game1.php"; ?>


<?php
// Guardar puntuación
if (isset($_POST["guardar"])) {
    $usuario = $_POST["usuario"] ?? "Anónimo";
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
    <title>Mini Juego SIN</title>


    <style>
        body {
            font-family: Arial;
            text-align: center;
            background: #f2f2f2;
        }


        #box {
            width: 80px;
            height: 80px;
            background: red;
            position: absolute;
            cursor: pointer;
        }
    </style>
</head>


<body>


<h1>🎮 Mini Juego: Clic y gana puntos</h1>


<p>Pulsa el cuadro rojo lo más rápido posible</p>


<h2>Puntos: <span id="score">0</span></h2>


<div id="box"></div>


<form method="POST">
    <input type="text" name="usuario" placeholder="Tu nombre">
    <input type="hidden" name="puntos" id="hiddenScore">
    <button type="submit" name="guardar">Guardar puntuación</button>
</form>


<hr>


<h3>Ranking</h3>


<ul>
<?php
$stmt = $pdo->query("SELECT * FROM puntuaciones ORDER BY puntos DESC LIMIT 10");


while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<li>{$row['usuario']} - {$row['puntos']} puntos</li>";
}
?>
</ul>


<script>
let score = 0;
let timeLeft = 15;
let gameActive = true;


const box = document.getElementById("box");
const scoreText = document.getElementById("score");
const hidden = document.getElementById("hiddenScore");


// Crear contador en pantalla
const timerText = document.createElement("h2");
timerText.innerText = "Tiempo: " + timeLeft;
document.body.insertBefore(timerText, box);


function moveBox() {
    if (!gameActive) return;


    const x = Math.random() * (window.innerWidth - 100);
    const y = Math.random() * (window.innerHeight - 200);


    box.style.left = x + "px";
    box.style.top = y + "px";
}


box.onclick = function () {
    if (!gameActive) return;


    score++;
    scoreText.innerText = score;
    hidden.value = score;
    moveBox();
};


// Temporizador
const timer = setInterval(() => {
    timeLeft--;
    timerText.innerText = "Tiempo: " + timeLeft;


    if (timeLeft <= 0) {
        clearInterval(timer);
        gameActive = false;
        box.style.display = "none";


        alert("⏱️ Fin del juego. Puntuación: " + score);
    }
}, 1000);


moveBox();
</script>


</body>
</html>
