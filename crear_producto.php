<?php
session_start();
require_once 'config/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $id_categoria = $_POST['id_categoria'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock_actual = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];
    
    if($precio >= 0 && $stock_actual >= 0 && $stock_minimo >= 0) {
        try {
            $sql = "INSERT INTO Producto (codigo, nombre, id_categoria, descripcion, precio, stock_actual, stock_minimo, estado) 
                    VALUES (:codigo, :nombre, :id_categoria, :descripcion, :precio, :stock_actual, :stock_minimo, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'id_categoria' => $id_categoria,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'stock_actual' => $stock_actual,
                'stock_minimo' => $stock_minimo
            ]);
        
            header("Location: dashboard.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    } else {
        $error = "Los precios y el stock no pueden ser negativos.";
    }
}

$stmt_cat = $pdo->query("SELECT id_categoria, nombre FROM Categoria WHERE estado = 1");
$categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class= "row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Agregar Nuevo Producto</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        <form action="crear_producto.php" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Código</label>
                                    <input type="text" name="codigo" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Categoría</label>
                                    <select name="id_categoria" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        <?php foreach($categorias as $cat): ?>
                                            <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio</label>
                                    <input type="number" name="precio" class="form-control" min="0" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Stock Actual</label>
                                    <input type="number" name="stock_actual" class="form-control" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stock Mínimo</label>
                                    <input type="number" name="stock_minimo" class="form-control" min="0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

