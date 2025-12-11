<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../conexionBD.php';
$mysqli = abrirConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    $id_cita = (int) ($_POST['id_cita'] ?? '');

    if (!empty($id_cita)) {
        $stmt = $mysqli->prepare("DELETE FROM citas WHERE id_cita = ?");
        $stmt->bind_param("i", $id_cita);
        if (!($stmt->execute())) {
            $errors[] = "Error al eliminar la cita: {$stmt->error}";
        }
        $stmt->close();
    } else {
        $errors[] = "ID de cita inválido";
    }

    cerrarConexion($mysqli);
    echo json_encode($errors);
    exit();
}
