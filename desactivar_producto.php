<?php
session_start();
require_once 'config/conexion.php';

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_producto = $_GET['id'];

try {
    $sql = "UPDATE Producto SET estado = 0 WHERE id_producto = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_producto]);

} catch (PDOException $e) {
    die("Error al desactivar: " . $e->getMessage());
}

header("Location: dashboard.php");
exit;
?>