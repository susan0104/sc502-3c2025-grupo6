<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

include '../conexionBD.php';
$mysqli = abrirConexion();

header('Content-Type: application/json');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_cita = intval($_POST['id_cita'] ?? 0);

    if ($id_cita > 0) {

        $stmt = $mysqli->prepare("DELETE FROM Citas WHERE Cita_Id = ?");

        if (!$stmt) {
            echo json_encode(["Error al preparar la consulta: " . $mysqli->error]);
            exit();
        }

        $stmt->bind_param("i", $id_cita);

        if (!$stmt->execute()) {
            $errors[] = "Error al eliminar la cita: {$stmt->error}";
        }

        $stmt->close();
        
    } else {
        $errors[] = "ID de cita inválido";
    }
}

cerrarConexion($mysqli);

echo json_encode($errors);
exit();
