<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../assets/php/conexionBD.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = $_POST["nombre"] ?? '';
    $especie_id = $_POST["tipo"] ?? '';
    $raza = $_POST["raza"] ?? '';
    $edad = $_POST["edad"] ?? '';
    $observaciones = $_POST["observaciones"] ?? '';
    $cliente_id = $_POST["cliente_id"] ?? '';
    $fotoBase64 = $_POST["fotoBase64"] ?? '';

    if (!$nombre || !$especie_id || !$raza || $edad === '' || !$cliente_id) {
        echo "error: datos incompletos";
        exit();
    }

    $conexion = abrirConexion();

    $sqlMascota = "
        INSERT INTO Mascota (
            Nombre,
            Especie_Id,
            Raza,
            Edad,
            Observaciones,
            Cliente_Id,
            Foto
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

    $stmtMascota = $conexion->prepare($sqlMascota);

    if (!$stmtMascota) {
        echo "error: prepare mascota - " . $conexion->error;
        exit();
    }
    $stmtMascota->bind_param(
        "sisisis",
        $nombre,
        $especie_id,
        $raza,
        $edad,
        $observaciones,
        $cliente_id,
        $fotoBase64
    );

    $stmtMascota->execute();
    $nuevaMascotaId = $conexion->insert_id;

    $sqlExp = "INSERT INTO MascotaExpediente (Mascota_Id) VALUES (?)";
    $stmtExp = $conexion->prepare($sqlExp);
    $stmtExp->bind_param("i", $nuevaMascotaId);
    $stmtExp->execute();

    echo "ok";

    $stmtMascota->close();
    $stmtExp->close();
    cerrarConexion($conexion);
}
?>