<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../conexionBD.php';
$mysqli = abrirConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    $id_cita = (int) ($_POST['id_cita'] ?? '');
    $cliente = $_POST['cliente'] ?? '';
    $mascota = $_POST['mascota'] ?? '';
    $servicio = $_POST['servicio'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $precio = (float) ($_POST['precio'] ?? 0);

    $stmt_mascota = $mysqli->prepare("SELECT id_mascota FROM mascotas WHERE nombre = ? LIMIT 1");
    $stmt_mascota->bind_param("s", $mascota);
    $stmt_mascota->execute();
    $result_mascota = $stmt_mascota->get_result();
    $row_mascota = $result_mascota->fetch_assoc();
    $id_mascota = $row_mascota['id_mascota'] ?? 0;
    $stmt_mascota->close();

    $stmt_servicio = $mysqli->prepare("SELECT id_servicio FROM servicios WHERE nombre = ? LIMIT 1");
    $stmt_servicio->bind_param("s", $servicio);
    $stmt_servicio->execute();
    $result_servicio = $stmt_servicio->get_result();
    $row_servicio = $result_servicio->fetch_assoc();
    $id_servicio = $row_servicio['id_servicio'] ?? 0;

    $fechaDateTime = new DateTime($fecha);
    $hoyDateTime = new DateTime();
    $hoyDateTime->setTime(0, 0, 0);

    if ($fechaDateTime->format('Y-m-d') < $hoyDateTime->format('Y-m-d')) {
        $errors[] = "La fecha no puede ser anterior a hoy.";
    }

    $id_empleado = null;
    $appointmentHour = date('H', strtotime("$fecha $hora"));
    $hourStart = str_pad($appointmentHour, 2, '0', STR_PAD_LEFT) . ':00:00';
    $hourEnd = str_pad($appointmentHour + 1, 2, '0', STR_PAD_LEFT) . ':00:00';
    $hourStart = str_pad($appointmentHour, 2, '0', STR_PAD_LEFT) . ':00:00';
    $hourEnd = str_pad($appointmentHour + 1, 2, '0', STR_PAD_LEFT) . ':00:00';

    if ($servicio == 'Limpieza Dental' || $servicio == 'Desparasitación' || $servicio == 'Vacunación') {
        $stmt = $mysqli->prepare("
            SELECT e.id_empleado FROM empleados e
            WHERE e.cargo = ?
            AND NOT EXISTS (
                SELECT 1 FROM citas c
                WHERE c.id_empleado = e.id_empleado
                AND DATE(c.fecha) = ?
                AND TIME(c.fecha) >= ?
                AND TIME(c.fecha) < ?
            )
            LIMIT 1
        ");
        if (!$stmt) {
            $errors[] = "Error en prepare al buscar veterinarios";
        } else {
            $cargo = "Veterinario";
            $fechaOnly = date('Y-m-d', strtotime($fecha));
            $stmt->bind_param('ssss', $cargo, $fechaOnly, $hourStart, $hourEnd);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id_empleado = $row['id_empleado'];
            } else {
                $errors[] = "No hay empleados disponibles en esta fecha y hora.";
            }
            $stmt->close();
        }
    } else {
        $stmt = $mysqli->prepare("
            SELECT e.id_empleado FROM empleados e
            WHERE e.cargo != ?
            AND NOT EXISTS (
                SELECT 1 FROM citas c
                WHERE c.id_empleado = e.id_empleado
                AND DATE(c.fecha) = ?
                AND TIME(c.fecha) >= ?
                AND TIME(c.fecha) < ?
            )
            LIMIT 1
        ");
        if (!$stmt) {
            $errors[] = "Error en prepare al buscar empleados";
        } else {
            $cargo = "Secretario";
            $fechaOnly = date('Y-m-d', strtotime($fecha));
            $stmt->bind_param('ssss', $cargo, $fechaOnly, $hourStart, $hourEnd);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id_empleado = $row['id_empleado'];
            } else {
                $errors[] = "No hay empleados disponibles en esta fecha y hora.";
            }
            $stmt->close();
        }

        $index = $id_cita;
        $observaciones = "";

        if ($index === '') {
            $errors[] = "No se encontró la cita.";
        }

        if (empty($errors)) {
            $observaciones = "";
            $stmt = $mysqli->prepare(
                "UPDATE citas 
             SET id_mascota = ?, id_servicio = ?, id_empleado = ?, 
                 fecha = CONCAT(?, ' ', ?), estado = ?, precio = ?, observaciones = ?
             WHERE id_cita = ?"
            );
            if (!$stmt) {
                $errors[] = "Error en prepare al actualizar la cita";
            } else {
                $stmt->bind_param("iiisssdsi", $id_mascota, $id_servicio, $id_empleado, $fecha, $hora, $estado, $precio, $observaciones, $index);
                if (!$stmt->execute()) {
                    $errors[] = "Error al actualizar la cita: {$stmt->error}";
                }
                $stmt->close();
            }
        }
    }

    cerrarConexion($mysqli);
    echo json_encode($errors);
    exit();
}
