<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../conexionBD.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST["nombre"] ?? '';
    $correo = $_POST["correo"] ?? '';
    $contrasena = $_POST["contrasenia"] ?? '';
    $cargo = $_POST["cargo"] ?? '';

    $claveHash = password_hash($contrasena, PASSWORD_DEFAULT);

    $conexion = abrirConexion();

    $sql = "INSERT INTO empleados (nombre, cargo, correo, contrasena) 
    VALUES(?,?,?,?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $cargo, $correo, $claveHash);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error: " . $conexion->error;
    }

    $stmt->close();
    cerrarConexion($conexion);
}
?>