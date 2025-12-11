<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../assets/php/conexionBD.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mascota_id = $_POST["mascota_id"] ?? '';
    $nombre = $_POST["nombre"] ?? '';
    $especie_id = $_POST["tipo"] ?? '';
    $raza = $_POST["raza"] ?? '';
    $edad = $_POST["edad"] ?? '';
    $observaciones = $_POST["observaciones"] ?? '';
    $fotoBase64 = $_POST["fotoBase64"] ?? '';

    if (
        !$mascota_id ||
        !$nombre ||
        !$especie_id ||
        !$raza ||
        $edad === ''
    ) {
        echo "error: datos incompletos";
        exit();
    }

    $conexion = abrirConexion();

    if ($fotoBase64 !== '') {

        $sql = "
            UPDATE Mascota SET
                Nombre = ?,
                Especie_Id = ?,
                Raza = ?,
                Edad = ?,
                Observaciones = ?,
                Foto = ?
            WHERE Mascota_Id = ?
        ";

        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            echo "error: prepare falló - " . $conexion->error;
            exit();
        }

        $stmt->bind_param(
            "sisissi",
            $nombre,
            $especie_id,
            $raza,
            $edad,
            $observaciones,
            $fotoBase64,
            $mascota_id
        );

    } else {
        $sql = "
            UPDATE Mascota SET
                Nombre = ?,
                Especie_Id = ?,
                Raza = ?,
                Edad = ?,
                Observaciones = ?
            WHERE Mascota_Id = ?
        ";

        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            echo "error: prepare falló - " . $conexion->error;
            exit();
        }

        $stmt->bind_param(
            "sisisi",
            $nombre,
            $especie_id,
            $raza,
            $edad,
            $observaciones,
            $mascota_id
        );
    }

    if ($stmt->execute()) {
        echo "ok:" . $mascota_id;
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    cerrarConexion($conexion);
}
?>
