<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejemplo 2 - PHP</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body class="bg-light">


<div class="container text-center mt-5">


    <!-- Logo -->
    <img src="https://portal.edu.gva.es/iesmiralcamp/wp-content/uploads/sites/191/2020/12/logo-IES-Miralcamp-2017-mini-300x240-300x240.png"
         alt="Logo IES Miralcamp"
         class="mb-4">


    <!-- Título -->
    <h1 class="mb-3">Ejemplo 2: PHP</h1>


    <p class="lead">
        Ejemplo de página dinámica ejecutada en el servidor
    </p>


    <!-- PHP: fecha y hora -->
    <div class="alert alert-info">
        <?php
            date_default_timezone_set("Europe/Madrid");
            echo "Fecha y hora del servidor: " . date("d/m/Y H:i:s");
        ?>
    </div>


    <!-- PHP: usuario del sistema -->
    <?php
        $usuario = getenv("USERNAME") ?: "Alumno";
    ?>


    <p>
        Bienvenido, <strong><?php echo $usuario; ?></strong>
    </p>


    <!-- Información del servidor -->
    <div class="alert alert-secondary">
        <?php
            echo "Servidor: " . $_SERVER['SERVER_NAME'];
        ?>
    </div>


    <!-- Botón JavaScript -->
    <button class="btn btn-primary" onclick="mostrarMensaje()">
        Pulsa aquí
    </button>


    <p id="mensaje" class="mt-4 fw-bold"></p>


    <!-- Enlace de vuelta -->
    <div class="mt-4">
        <a href="index.html" class="btn btn-outline-secondary">
            Volver al inicio
        </a>
    </div>


</div>


<!-- JavaScript -->
<script>
function mostrarMensaje() {
    document.getElementById("mensaje").innerText =
        "Este mensaje lo genera JavaScript en el navegador";
}
</script>


</body>
</html>
