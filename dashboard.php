<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TecnoStock - Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">TecnoStock</a>
           <div class= "d-flex text-white align-items-center">
                <span class="me-3">Bienvenido, <?php echo $_SESSION['nombre']; ?></span>
                <a href="logout.php" class="btn btn-outline-light">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Inventario de Productos</h2>
        <!-- Aquí puedes agregar la tabla o el contenido del inventario -->
    </div>
</body>
</html>