<?php
session_start();

if (!file_exists('config/conexion.php')) {
    die("Fallo 1: El archivo config/conexion.php no existe en esta ruta.");
}

require_once 'config/conexion.php';

if (!isset($pdo)) {
    die("Fallo 2: El archivo cargó, pero la variable \$pdo no se encontró dentro de él.");
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}
$sql = "SELECT p.*, c.nombre AS categoria_nombre
        FROM Producto p
        LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
        WHERE p.estado = 1";
        $stmt = $pdo->query($sql);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoStock - Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="big-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">TecnoStock</a>
            <div class="text-white">
                <span class="navbar-text me-3">
                    Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Inventario de Productos</h3>
            <a href="crear_producto.php" class="btn btn-primary">+ Nuevo Producto</a>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class ="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prod['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($prod['categoria_nombre']); ?></td>
                            <td><?php echo number_format($prod['precio'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($prod['stock_actual'] < $prod['stock_minimo']): ?>
                                   <span class="badge bg-danger"><?php echo $prod['stock_actual']; ?> (Bajo)</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?php echo $prod['stock_actual']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>Activo</td>
                            <td>
                                <a href="editar_producto.php?id=<?php echo $prod['id_producto']; ?>" class="btn btn-sm btn-secondary">Editar</a>
                            </td> 
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>



                    