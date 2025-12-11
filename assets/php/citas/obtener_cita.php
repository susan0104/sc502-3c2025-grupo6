<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../conexionBD.php';
$mysqli = abrirConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $index = trim($_POST['index'] ?? '');
    $index = (int) $index;

    $errors = [];

    if ($index === '') {
        $errors[] = "No se encontró la cita.";
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare(
            "SELECT c.id_cita, c.id_mascota, c.id_servicio, c.id_empleado, 
                    DATE(c.fecha) AS fecha, TIME(c.fecha) AS hora, c.estado, c.precio, c.observaciones,
                    m.nombre AS mascota, s.nombre AS servicio, u.nombre AS cliente
             FROM citas c
             LEFT JOIN mascotas m ON c.id_mascota = m.id_mascota
             LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
             LEFT JOIN mascotas m2 ON c.id_mascota = m2.id_mascota
             LEFT JOIN usuarios u ON m2.id_usuario = u.id_usuario
             WHERE c.id_cita = ? LIMIT 1"
        );
        if (!$stmt) {
            $errors[] = "Error en prepare al seleccionar citas";
        } else {
            $stmt->bind_param("i", $index);
            $stmt->execute();
            $result = $stmt->get_result();
            $cita = $result->fetch_assoc();

            if (!$cita) {
                $errors[] = "Cita no encontrada.";
            }

            $stmt->close();
        }
    }

    cerrarConexion($mysqli);

    if (empty($errors)) {
        echo json_encode(['cita' => $cita]);
    } else {
        echo json_encode(['errors' => $errors]);
    }
    exit();
}
?>