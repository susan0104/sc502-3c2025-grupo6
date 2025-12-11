<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../assets/php/conexionBD.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre         = $_POST["nombre"] ?? '';
    $especie_id     = $_POST["tipo"] ?? '';
    $raza           = $_POST["raza"] ?? '';
    $edad           = $_POST["edad"] ?? '';
    $observaciones  = $_POST["observaciones"] ?? '';
    $cliente_id     = $_POST["cliente_id"] ?? '';

    if (
        !$nombre ||
        !$especie_id ||
        !$raza ||
        $edad === '' ||
        !$observaciones ||
        !$cliente_id
    ) {
        echo "error: datos incompletos";
        exit();
    }

    $conexion = abrirConexion();

    $sql = "
        INSERT INTO Mascota (
            Nombre,
            Especie_Id,
            Raza,
            Edad,
            Observaciones,
            Cliente_Id
        ) VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        echo "error: prepare falló - " . $conexion->error;
        exit();
    }

    $stmt->bind_param(
        "sisiii",
        $nombre,
        $especie_id,
        $raza,
        $edad,
        $observaciones,
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
