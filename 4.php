<?php include "db.php"; ?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD SIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body class="bg-light">


<div class="container mt-5">
    <h1 class="text-center mb-4">CRUD de Usuarios</h1>


    <?php
    $mensaje = "";


    // ======================
    // CREATE
    // ======================
    if (isset($_POST["crear"])) {
        $nombre = $_POST["nombre"] ?? "";


        if (!empty($nombre)) {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre) VALUES (:nombre)");
            $stmt->execute(["nombre" => $nombre]);
            $mensaje = "Usuario creado ✔";
        }
    }


    // ======================
    // DELETE
    // ======================
    if (isset($_POST["borrar"])) {
        $id = $_POST["id"];
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $mensaje = "Usuario eliminado 🗑️";
    }


    // ======================
    // UPDATE
    // ======================
    if (isset($_POST["editar"])) {
        $id = $_POST["id"];
        $nombre = $_POST["nombre"];


        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre WHERE id = :id");
        $stmt->execute([
            "nombre" => $nombre,
            "id" => $id
        ]);


        $mensaje = "Usuario actualizado ✏️";
    }
    ?>


    <?php if ($mensaje): ?>
        <div class="alert alert-info text-center">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>


    <!-- CREATE -->
    <form method="POST" class="mb-4 text-center">
        <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre">
        <button name="crear" class="btn btn-primary">Crear usuario</button>
    </form>


    <!-- READ -->
    <h3>Usuarios</h3>


    <ul class="list-group">


        <?php
        $stmt = $pdo->query("SELECT * FROM usuarios");


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>


        <li class="list-group-item">
            <form method="POST" class="d-flex gap-2">
                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                <input type="text" name="nombre" value="<?php echo $row["nombre"]; ?>" class="form-control">
                <button name="editar" class="btn btn-warning">Editar</button>
                <button name="borrar" class="btn btn-danger">Borrar</button>
            </form>
        </li>
        <?php } ?>
    </ul>
</div>


</body>
</html>