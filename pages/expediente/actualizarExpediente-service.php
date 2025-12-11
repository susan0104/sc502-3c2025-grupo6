<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../assets/php/conexionBD.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mascota_id  = $_POST["mascota_id"] ?? '';
    $obs         = $_POST["obs"] ?? '';
    $alergias    = $_POST["alergias"] ?? '';
    $tratamientos= $_POST["tratamientos"] ?? '';

    if (!$mascota_id) {
        echo "error: Mascota_Id faltante";
        exit();
    }

    $conexion = abrirConexion();

    $sql = "
        UPDATE MascotaExpediente
        SET Observaciones = ?,
            Alergias = ?,
            Tratamientos = ?
        WHERE Mascota_Id = ?
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        echo "error: prepare falló - " . $conexion->error;
        exit();
    }

    $stmt->bind_param("sssi", $obs, $alergias, $tratamientos, $mascota_id);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    cerrarConexion($conexion);
}
?>
