<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejemplo 3 - Formulario completo</title>


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body class="bg-light">


<div class="container mt-5">


    <h1 class="text-center mb-4">Ejemplo 3: Formulario + PHP</h1>


    <?php
    $mensaje = "";


    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        $accion = $_POST["accion"] ?? "";


        // ======================
        // BORRAR DATOS
        // ======================
        if ($accion == "borrar") {
            if (file_exists("datos.txt")) {
                unlink("datos.txt");
                $mensaje = "Datos borrados correctamente 🗑️";
            } else {
                $mensaje = "No hay datos para borrar";
            }
        }


        // ======================
        // GUARDAR DATOS
        // ======================
        if ($accion == "enviar") {


            $nombre = htmlspecialchars($_POST["nombre"] ?? "");


            if (!empty($nombre)) {
                $mensaje = "Hola, $nombre 👋";
                file_put_contents("datos.txt", $nombre . PHP_EOL, FILE_APPEND);
            } else {
                $mensaje = "Por favor, introduce un nombre";
            }
        }
    }
    ?>


    <!-- MENSAJE -->
    <?php if ($mensaje): ?>
        <div class="alert alert-info text-center">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>


    <!-- FORMULARIO -->
    <form method="POST" class="text-center">


        <div class="mb-3">
            <label class="form-label">Introduce tu nombre</label>
            <input type="text" name="nombre" class="form-control">
        </div>


        <!-- BOTONES JUNTOS -->
        <div class="d-flex justify-content-center gap-3">


            <button type="submit" name="accion" value="enviar" class="btn btn-primary">
                Enviar
            </button>


            <button type="submit" name="accion" value="borrar" class="btn btn-danger">
                Borrar datos
            </button>


        </div>


    </form>


    <!-- DATOS GUARDADOS -->
    <div class="mt-5">
        <h4>Datos almacenados:</h4>


        <ul class="list-group">
            <?php
            if (file_exists("datos.txt")) {
                $lineas = file("datos.txt");


                foreach ($lineas as $linea) {
                    echo "<li class='list-group-item'>" . htmlspecialchars($linea) . "</li>";
                }
            } else {
                echo "<li class='list-group-item'>No hay datos guardados</li>";
            }
            ?>
        </ul>
    </div>


    <!-- VOLVER -->
    <div class="text-center mt-4">
        <a href="index.html" class="btn btn-secondary">Volver</a>
    </div>


</div>


</body>
</html>
