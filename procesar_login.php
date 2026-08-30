<?php
session_start();
require_once 'config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    $sql = "SELECT id_usuario, nombre, clave FROM Usuario WHERE correo = :correo AND estado = 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(['correo' => $correo]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $clave === $usuario['clave']) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];

        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: index.php?error=1');
    }
}
?>