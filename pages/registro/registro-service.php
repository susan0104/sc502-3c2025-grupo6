<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../assets/php/conexionBD.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre     = $_POST["nombre"] ?? '';
    $usuario    = $_POST["usuario"] ?? '';
    $correo     = $_POST["correo"] ?? '';
    $rol        = $_POST["cargo"] ?? '';
    $contrasena = $_POST["contrasenia"] ?? '';

    if (!$nombre || !$usuario || !$correo || !$rol || !$contrasena) {
        echo "error: datos incompletos";
        exit();
    }
    $claveHash = password_hash($contrasena, PASSWORD_DEFAULT);

    $conexion = abrirConexion();
    $sql = "
        INSERT INTO Usuario (
            Nombre,
            Usuario,
            Correo,
            Contrasena,
            Rol_Id
        ) VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        echo "error: prepare falló - " . $conexion->error;
        exit();
    }

    $stmt->bind_param(
        "ssssi",
        $nombre,
        $usuario,
        $correo,
        $claveHash,
        $rol
    );

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    cerrarConexion($conexion);
}
?>
