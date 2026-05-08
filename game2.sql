CREATE DATABASE sin_game2;


USE sin_game2;


CREATE TABLE puntuaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100),
    puntos INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
