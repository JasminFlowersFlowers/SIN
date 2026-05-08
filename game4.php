<?php 
include "db_game4.php"; 

// Procesar el guardado de puntuación
if (isset($_POST["guardar"])) {
    $usuario = !empty($_POST["usuario"]) ? $_POST["usuario"] : "Anónimo";
    $movimientos = $_POST["movimientos"] ?? 0;
    $tiempo = $_POST["tiempo"] ?? 0;

    $stmt = $pdo->prepare("INSERT INTO puntuaciones (usuario, movimientos, tiempo) VALUES (:u, :m, :t)");
    $stmt->execute(["u" => $usuario, "m" => $movimientos, "t" => $tiempo]);
    
    // Redirigir para evitar reenvío de formulario
    header("Location: game4.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Match Deluxe</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#111827; color:white; text-align:center; margin:0; padding:20px; }
        h1 { margin-top:10px; color: #00ffcc; text-shadow: 0 0 10px rgba(0,255,204,0.3); }
        #stats { margin: 20px 0; font-size: 20px; background: #1f2937; display: inline-block; padding: 10px 20px; border-radius: 50px; }
        #gameBoard { width: fit-content; margin: 20px auto; display: grid; grid-template-columns: repeat(4, 120px); gap: 15px; }
        
        /* Estilos de las Cartas */
        .card { width: 120px; height: 120px; position: relative; transform-style: preserve-3d; transition: transform 0.5s; cursor: pointer; }
        .card.flip { transform: rotateY(180deg); }
        .front, .back { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border-radius: 12px; display: flex; justify-content: center; align-items: center; }
        .front { background: #374151; font-size: 40px; border: 2px solid #4b5563; }
        .back { transform: rotateY(180deg); background: white; overflow: hidden; }
        .back img { width: 100%; height: 100%; object-fit: cover; }
        
        .matched { pointer-events: none; animation: glow 1s infinite alternate; }
        @keyframes glow { from { box-shadow: 0 0 5px #00ffcc; } to { box-shadow: 0 0 20px #00ffcc; } }

        /* UI y Formulario */
        button { padding: 12px 25px; border: none; background: #00b894; color: white; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        button:hover { background: #00d1a0; transform: scale(1.05); }
        input { padding: 12px; border-radius: 8px; border: 1px solid #374151; background: #1f2937; color: white; margin-bottom: 10px; }
        
        table { margin: 40px auto; border-collapse: collapse; width: 90%; max-width: 600px; background: #1f2937; border-radius: 10px; overflow: hidden; }
        th, td { padding: 15px; text-align: center; border-bottom: 1px solid #374151; }
        th { background: #111827; color: #00ffcc; }
    </style>
</head>
<body>

    <h1>🎮 Memory Match Deluxe</h1>
    <p>Desafía tu mente: encuentra todas las parejas</p>

    <div id="stats">
        🧠 Movimientos: <span id="moves">0</span> | ⏱️ Tiempo: <span id="time">0</span>s
    </div>

    <br>
    <button onclick="location.reload()">🔄 Reiniciar Partida</button>

    <div id="gameBoard"></div>

    <div id="winMessage" style="display:none; margin-top: 20px;">
        <form method="POST">
            <h3>¡Felicidades! Guarda tu récord:</h3>
            <input type="text" name="usuario" placeholder="Tu nombre" required>
            <input type="hidden" name="movimientos" id="hiddenMoves">
            <input type="hidden" name="tiempo" id="hiddenTime">
            <button type="submit" name="guardar">💾 Guardar en Ranking</button>
        </form>
    </div>

    <h2>🏆 Top 10 Ranking</h2>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Movimientos</th>
            <th>Tiempo</th>
        </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM puntuaciones ORDER BY movimientos ASC, tiempo ASC LIMIT 10");
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo "<tr>
                    <td>" . htmlspecialchars($row['usuario']) . "</td>
                    <td>{$row['movimientos']}</td>
                    <td>{$row['tiempo']}s</td>
                  </tr>";
        }
        ?>
    </table>

    <script>
        const images = [
            "https://picsum.photos/id/1025/200", "https://picsum.photos/id/1012/200",
            "https://picsum.photos/id/1062/200", "https://picsum.photos/id/1074/200",
            "https://picsum.photos/id/219/200", "https://picsum.photos/id/169/200"
        ];

        let cardsData = [...images, ...images];
        cardsData.sort(() => Math.random() - 0.5);

        const board = document.getElementById("gameBoard");
        let firstCard = null, secondCard = null;
        let lockBoard = false, moves = 0, matches = 0, time = 0;

        let timer = setInterval(() => {
            time++;
            document.getElementById("time").innerText = time;
            document.getElementById("hiddenTime").value = time;
        }, 1000);

        function createBoard() {
            cardsData.forEach(imgUrl => {
                const card = document.createElement("div");
                card.classList.add("card");
                card.innerHTML = `
                    <div class="front">?</div>
                    <div class="back"><img src="${imgUrl}"></div>
                `;
                card.dataset.image = imgUrl;
                card.addEventListener("click", flipCard);
                board.appendChild(card);
            });
        }

        function flipCard() {
            if (lockBoard || this === firstCard) return;
            this.classList.add("flip");

            if (!firstCard) {
                firstCard = this;
                return;
            }

            secondCard = this;
            moves++;
            document.getElementById("moves").innerText = moves;
            document.getElementById("hiddenMoves").value = moves;
            checkMatch();
        }

        function checkMatch() {
            let isMatch = firstCard.dataset.image === secondCard.dataset.image;
            if (isMatch) {
                firstCard.classList.add("matched");
                secondCard.classList.add("matched");
                matches++;
                resetTurn();
                if (matches === images.length) {
                    clearInterval(timer);
                    document.getElementById("winMessage").style.display = "block";
                }
            } else {
                lockBoard = true;
                setTimeout(() => {
                    firstCard.classList.remove("flip");
                    secondCard.classList.remove("flip");
                    resetTurn();
                }, 1000);
            }
        }

        function resetTurn() {
            [firstCard, secondCard, lockBoard] = [null, null, false];
        }

        createBoard();
    </script>
</body>
</html>