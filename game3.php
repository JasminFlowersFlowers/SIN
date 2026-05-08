<?php include 'db_game3.php'; ?>
<!DOCTYPE html>
<html lang="es">


<head>
    <meta charset="UTF-8">
    <title>Tetris Pro Edition</title>
    <style>
        :root {
            --neon-blue: #00f3ff;
            --neon-pink: #ff00ff;
        }


        body {
            background: #0a0a0a;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }


        .container {
            display: flex;
            gap: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }


        #tetris {
            border: 4px solid var(--neon-blue);
            box-shadow: 0 0 15px var(--neon-blue);
            background: #000;
        }


        .info-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 200px;
        }


        .stats {
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }


        .score-val {
            color: var(--neon-pink);
            font-weight: bold;
        }


        .ranking {
            margin-top: 20px;
            font-size: 14px;
            border-top: 1px solid #444;
            padding-top: 10px;
        }


        #btn-start {
            background: var(--neon-blue);
            border: none;
            padding: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }


        #btn-start:hover {
            transform: scale(1.05);
            filter: brightness(1.2);
        }


        .controls-help {
            margin-top: 20px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            font-size: 0.85em;
            border-left: 3px solid var(--neon-pink);
        }


        .controls-help div {
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }


        .key {
            background: #333;
            padding: 2px 6px;
            border-radius: 4px;
            color: var(--neon-blue);
            font-family: monospace;
        }
    </style>
</head>


<body>


    <div class="container">
        <div>
            <canvas id="tetris" width="240" height="400"></canvas>
        </div>


        <div class="info-panel">
            <div class="stats">
                <div>Puntos:</div>
                <div id="score" class="score-val">0</div>
            </div>


            <div class="ranking">
                <h3>TOP 10 RANKING</h3>
                <ul id="highscores">
                    <?php
                    $res = getHighScores($conn);
                    while ($row = $res->fetch_assoc()) {
                        echo "<li>{$row['username']}: {$row['score']}</li>";
                    }
                    ?>
                </ul>
            </div>


            <button id="btn-start">EMPEZAR JUEGO</button>
            <div class="controls-help">
                <h4>CONTROLES</h4>
                <div><span>Mover:</span> <span class="key">← →</span></div>
                <div><span>Girar:</span> <span class="key">Q / W</span></div>
                <div><span>Bajar:</span> <span class="key">↓</span></div>
                <div><span>Caída Rápida:</span> <span class="key">Espacio</span></div>
            </div>
        </div>


    </div>


    <script>
        const canvas = document.getElementById('tetris');
        const context = canvas.getContext('2d');
        const scoreElement = document.getElementById('score');


        context.scale(20, 20);


        // Lógica de piezas (Mecánica Core)
        function createPiece(type) {
            if (type === 'T') return [
                [0, 1, 0],
                [1, 1, 1],
                [0, 0, 0]
            ];
            if (type === 'O') return [
                [2, 2],
                [2, 2]
            ];
            if (type === 'L') return [
                [0, 0, 3],
                [3, 3, 3],
                [0, 0, 0]
            ];
            if (type === 'J') return [
                [4, 0, 0],
                [4, 4, 4],
                [0, 0, 0]
            ];
            if (type === 'I') return [
                [0, 5, 0, 0],
                [0, 5, 0, 0],
                [0, 5, 0, 0],
                [0, 5, 0, 0]
            ];
            if (type === 'S') return [
                [0, 6, 6],
                [6, 6, 0],
                [0, 0, 0]
            ];
            if (type === 'Z') return [
                [7, 7, 0],
                [0, 7, 7],
                [0, 0, 0]
            ];
        }


        const colors = [null, '#00f3ff', '#ff00ff', '#f39c12', '#2ecc71', '#e74c3c', '#9b59b6', '#f1c40f'];


        function draw() {
            context.fillStyle = '#000';
            context.fillRect(0, 0, canvas.width, canvas.height);
            drawMatrix(arena, {
                x: 0,
                y: 0
            });
            drawMatrix(player.matrix, player.pos);
        }


        function drawMatrix(matrix, offset) {
            matrix.forEach((row, y) => {
                row.forEach((value, x) => {
                    if (value !== 0) {
                        context.fillStyle = colors[value];
                        context.fillRect(x + offset.x, y + offset.y, 1, 1);
                        context.strokeStyle = '#000';
                        context.lineWidth = 0.05;
                        context.strokeRect(x + offset.x, y + offset.y, 1, 1);
                    }
                });
            });
        }


        // Colisiones y Limpieza de líneas
        function arenaSweep() {
            let rowCount = 1;
            outer: for (let y = arena.length - 1; y > 0; --y) {
                for (let x = 0; x < arena[y].length; ++x) {
                    if (arena[y][x] === 0) continue outer;
                }
                const row = arena.splice(y, 1)[0].fill(0);
                arena.unshift(row);
                ++y;
                player.score += rowCount * 10;
                rowCount *= 2;
            }
            updateScore();
        }


        function collide(arena, player) {
            const [m, o] = [player.matrix, player.pos];
            for (let y = 0; y < m.length; ++y) {
                for (let x = 0; x < m[y].length; ++x) {
                    if (m[y][x] !== 0 && (arena[y + o.y] && arena[y + o.y][x + o.x]) !== 0) return true;
                }
            }
            return false;
        }


        function merge(arena, player) {
            player.matrix.forEach((row, y) => {
                row.forEach((value, x) => {
                    if (value !== 0) arena[y + player.pos.y][x + player.pos.x] = value;
                });
            });
        }


        function playerDrop() {
            player.pos.y++;
            if (collide(arena, player)) {
                player.pos.y--;
                merge(arena, player);
                playerReset();
                arenaSweep();
                updateScore();
            }
            dropCounter = 0;
        }


        function playerMove(dir) {
            player.pos.x += dir;
            if (collide(arena, player)) player.pos.x -= dir;
        }


        function playerReset() {
            const pieces = 'ILJOTSZ';
            player.matrix = createPiece(pieces[pieces.length * Math.random() | 0]);
            player.pos.y = 0;
            player.pos.x = (arena[0].length / 2 | 0) - (player.matrix[0].length / 2 | 0);
            if (collide(arena, player)) {
                // GAME OVER
                const name = prompt("¡Juego Terminado! Tu puntuación: " + player.score + "\nIntroduce tu nombre:");
                if (name) saveScore(name, player.score);
                arena.forEach(row => row.fill(0));
                player.score = 0;
                updateScore();
            }
        }


        function saveScore(username, score) {
            const formData = new FormData();
            formData.append('username', username);
            formData.append('score', score);
            fetch('db_game3.php', {
                    method: 'POST',
                    body: formData
                })
                .then(() => location.reload());
        }


        function playerRotate(dir) {
            const pos = player.pos.x;
            let offset = 1;
            rotate(player.matrix, dir);
            while (collide(arena, player)) {
                player.pos.x += offset;
                offset = -(offset + (offset > 0 ? 1 : -1));
                if (offset > player.matrix[0].length) {
                    rotate(player.matrix, -dir);
                    player.pos.x = pos;
                    return;
                }
            }
        }


        function rotate(matrix, dir) {
            for (let y = 0; y < matrix.length; ++y) {
                for (let x = 0; x < y; ++x) {
                    [matrix[x][y], matrix[y][x]] = [matrix[y][x], matrix[x][y]];
                }
            }
            if (dir > 0) matrix.forEach(row => row.reverse());
            else matrix.reverse();
        }


        function playerHardDrop() {
            while (!collide(arena, player)) {
                player.pos.y++;
            }
            player.pos.y--; // Volver un paso atrás tras la colisión
            merge(arena, player);
            playerReset();
            arenaSweep();
            updateScore();
            dropCounter = 0;
        }


        let dropCounter = 0;
        let dropInterval = 1000;
        let lastTime = 0;


        function update(time = 0) {
            const deltaTime = time - lastTime;
            lastTime = time;
            dropCounter += deltaTime;
            if (dropCounter > dropInterval) playerDrop();
            draw();
            requestAnimationFrame(update);
        }


        function updateScore() {
            scoreElement.innerText = player.score;
        }


        const arena = Array.from({
            length: 20
        }, () => Array(12).fill(0));


        const player = {
            pos: {
                x: 0,
                y: 0
            },
            matrix: null,
            score: 0,
        };


        document.addEventListener('keydown', event => {
            // Evitar que el espacio haga scroll en la página
            if (event.keyCode === 32) {
                event.preventDefault();
                playerHardDrop();
            }


            if (event.keyCode === 37) {
                playerMove(-1);
            } else if (event.keyCode === 39) {
                playerMove(1);
            } else if (event.keyCode === 40) {
                playerDrop();
            } else if (event.keyCode === 81) {
                playerRotate(-1);
            } else if (event.keyCode === 87) {
                playerRotate(1);
            }
        });


        document.getElementById('btn-start').addEventListener('click', () => {
            playerReset();
            updateScore();
            update();
            document.getElementById('btn-start').style.display = 'none';
        });
    </script>
</body>


</html>
