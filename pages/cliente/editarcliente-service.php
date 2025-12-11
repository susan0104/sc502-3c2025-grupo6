<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../assets/php/conexionBD.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cliente_id     = $_POST["cliente_id"] ?? '';
    $nombre         = $_POST["nombre"] ?? '';
    $identificacion = $_POST["identificacion"] ?? '';
    $fecha          = $_POST["fecha"] ?? '';
    $correo         = $_POST["correo"] ?? '';
    $plan           = $_POST["plan"] ?? '';

    if (
        !$cliente_id ||
        !$nombre ||
        !$identificacion ||
        !$fecha ||
        !$correo ||
        !$plan
    ) {
        echo "error: datos incompletos";
        exit();
    }

    $conexion = abrirConexion();


    $sql = "
        UPDATE Cliente SET
            Nombre = ?,
            Identificacion = ?,
            FechaNacimiento = ?,
            Correo = ?,
            Plan_Id = ?
        WHERE Cliente_Id = ?
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        echo "error: prepare falló - " . $conexion->error;
        exit();
    }


    $stmt->bind_param(
        "ssssii",
        $nombre,
        $identificacion,
        $fecha,
        $correo,
        $plan,
        $cliente_id
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
