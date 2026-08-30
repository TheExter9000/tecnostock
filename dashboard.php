<?php
session_start();
require_once 'config/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}


$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

$sql = "SELECT p.*, c.nombre AS categoria_nombre
        FROM Producto p
        LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
        WHERE p.estado = 1";
$params = [];

if ($buscar != '') {
    $sql .= " AND (p.nombre LIKE :buscar OR p.codigo LIKE :buscar OR c.nombre LIKE :buscar)";
    $params['buscar'] = "%$buscar%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TecnoStock - Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TecnoStock</a>
            <div class="d-flex align-items-center">
                <span class="me-3">
                    Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Administrador'); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Inventario de Productos</h3>
            <a href="crear_producto.php" class="btn btn-primary">+ Nuevo Producto</a>
        </div>
        
        <form action="dashboard.php" method="GET" class="d-flex mb-4">
            <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por código, nombre o categoría..." value="<?php echo htmlspecialchars($buscar); ?>">
            <button type="submit" class="btn btn-primary px-4">Buscar</button>
            <?php if($buscar != ''): ?>
                <a href="dashboard.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
            <?php endif; ?>
        </form>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
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
                                <td>$<?php echo number_format($prod['precio'], 0, ',', '.'); ?></td>
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
                                    <a href="desactivar_producto.php?id=<?php echo $prod['id_producto']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de desactivar este producto?');">Desactivar</a>
                                </td> 
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($productos) == 0): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No se encontraron productos.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>