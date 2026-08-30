<?php
session_start();
require_once 'config/conexion.php';

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_producto = $_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// Consultar datos actuales del producto
$stmt = $pdo->prepare("SELECT id_producto, codigo, nombre, stock_actual FROM Producto WHERE id_producto = :id AND estado = 1");
$stmt->execute(['id' => $id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: dashboard.php");
    exit;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo = $_POST['tipo'];
    $cantidad = (int)$_POST['cantidad'];
    $observacion = $_POST['observacion'];

    if ($cantidad > 0) {
        $nuevo_stock = $producto['stock_actual'];
        $movimiento_valido = true;

        if ($tipo == 'entrada') {
            $nuevo_stock += $cantidad;
        } elseif ($tipo == 'salida') {
            if ($cantidad > $producto['stock_actual']) {
                $error = "Error: La salida no puede ser mayor al stock actual (" . $producto['stock_actual'] . ").";
                $movimiento_valido = false;
            } else {
                $nuevo_stock -= $cantidad;
            }
        }

        if ($movimiento_valido) {
            try {
                // Iniciar transacción para asegurar que ambas tablas se actualicen juntas
                $pdo->beginTransaction();

                // 1. Registrar el movimiento
                $sql_mov = "INSERT INTO Movimiento (tipo, cantidad, observacion, id_producto, id_usuario) 
                            VALUES (:tipo, :cantidad, :observacion, :id_producto, :id_usuario)";
                $stmt_mov = $pdo->prepare($sql_mov);
                $stmt_mov->execute([
                    'tipo' => $tipo, 'cantidad' => $cantidad, 'observacion' => $observacion,
                    'id_producto' => $id_producto, 'id_usuario' => $id_usuario
                ]);

                // 2. Actualizar el stock en el producto
                $sql_prod = "UPDATE Producto SET stock_actual = :nuevo_stock WHERE id_producto = :id_producto";
                $stmt_prod = $pdo->prepare($sql_prod);
                $stmt_prod->execute(['nuevo_stock' => $nuevo_stock, 'id_producto' => $id_producto]);

                // Confirmar cambios
                $pdo->commit();
                
                header("Location: dashboard.php");
                exit;
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Error al procesar el movimiento: " . $e->getMessage();
            }
        }
    } else {
        $error = "La cantidad debe ser mayor a cero.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Movimiento - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Registrar Movimiento</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        
                        <div class="alert alert-info">
                            <strong>Producto:</strong> <?php echo htmlspecialchars($producto['nombre']); ?> (<?php echo htmlspecialchars($producto['codigo']); ?>)<br>
                            <strong>Stock Actual:</strong> <?php echo htmlspecialchars($producto['stock_actual']); ?> unidades
                        </div>

                        <form action="registrar_movimiento.php?id=<?php echo $id_producto; ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Movimiento</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="entrada">Entrada (Sumar stock)</option>
                                    <option value="salida">Salida (Restar stock)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cantidad</label>
                                <input type="number" name="cantidad" class="form-control" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observación (Opcional)</label>
                                <textarea name="observacion" class="form-control" rows="2" placeholder="Ej: Compra a proveedor, Venta, Producto dañado..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-success">Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>