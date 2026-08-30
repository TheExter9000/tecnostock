<?php
session_start();
require_once 'config/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

// 1. Obtener el ID del producto desde la URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_producto = $_GET['id'];

// 2. Procesar la actualización si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $id_categoria = $_POST['id_categoria'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock_actual = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];

    // Validación en el servidor: evitar números negativos
    if ($precio >= 0 && $stock_actual >= 0 && $stock_minimo >= 0) {
        try {
            $sql = "UPDATE Producto 
                    SET nombre = :nombre, id_categoria = :id_categoria, descripcion = :descripcion, 
                        precio = :precio, stock_actual = :stock_actual, stock_minimo = :stock_minimo 
                    WHERE id_producto = :id_producto";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'id_categoria' => $id_categoria,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'stock_actual' => $stock_actual,
                'stock_minimo' => $stock_minimo,
                'id_producto' => $id_producto
            ]);

            header("Location: dashboard.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    } else {
        $error = "Los valores numéricos no pueden ser negativos.";
    }
}

// 3. Consultar los datos actuales del producto
$stmt = $pdo->prepare("SELECT * FROM Producto WHERE id_producto = :id AND estado = 1");
$stmt->execute(['id' => $id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: dashboard.php");
    exit;
}

// 4. Traer categorías para el menú desplegable
$stmt_cat = $pdo->query("SELECT id_categoria, nombre FROM Categoria WHERE estado = 1");
$categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Editar Producto (<?php echo htmlspecialchars($producto['codigo']); ?>)</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        
                        <form action="editar_producto.php?id=<?php echo $id_producto; ?>" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Código (No editable)</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($producto['codigo']); ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Categoría</label>
                                    <select name="id_categoria" class="form-select" required>
                                        <?php foreach($categorias as $cat): ?>
                                            <option value="<?php echo $cat['id_categoria']; ?>" <?php if($cat['id_categoria'] == $producto['id_categoria']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($cat['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio</label>
                                    <input type="number" name="precio" class="form-control" min="0" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Stock Actual</label>
                                    <input type="number" name="stock_actual" class="form-control" min="0" value="<?php echo htmlspecialchars($producto['stock_actual']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stock Mínimo</label>
                                    <input type="number" name="stock_minimo" class="form-control" min="0" value="<?php echo htmlspecialchars($producto['stock_minimo']); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-warning">Actualizar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>