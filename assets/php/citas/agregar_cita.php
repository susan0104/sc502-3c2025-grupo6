<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../conexionBD.php';
$mysqli = abrirConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente = trim($_POST['cliente'] ?? '');
    $id_mascota = trim($_POST['mascota'] ?? '');
    $servicio = trim($_POST['servicio'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $precio = trim($_POST['precio'] ?? '');

    $id_mascota = (int) $id_mascota;
    $precio = (float) $precio;
    $errors = [];

    if ($cliente === '')
        $errors[] = "cliente es obligatorio.";
    if ($id_mascota === '')
        $errors[] = "Mascota es obligatoria.";
    if ($servicio === '')
        $errors[] = "Servicio es obligatorio.";
    if ($fecha === '' || $hora === '')
        $errors[] = "Fecha y hora son obligatorias.";

    $dateStr = $fecha . ' ' . $hora;
    $dt = DateTime::createFromFormat('Y-m-d H:i', $dateStr);
    $errorsDt = DateTime::getLastErrors();
    if (!$dt || $errorsDt) {
        $errors[] = "Formato de fecha u hora inválido.";
    } else {
        $now = new DateTime();
        if ($dt <= $now) {
            $errors[] = "La fecha y hora no pueden ser anteriores a la fecha y hora actual.";
        } else {
            $fechaYhora = $dt->format('Y-m-d H:i:00');
        }
    }


    if (empty($errors)) {
        $id_empleado = null;
        $appointmentHour = date('H', strtotime($fechaYhora));
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
                $fechaOnly = date('Y-m-d', strtotime($fechaYhora));
                $stmt->bind_param('ssss', $cargo, $fechaOnly, $hourStart, $hourEnd);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $id_empleado = $row['id_empleado'];
                } else {
                    $errors[] = "No hay empleados disponibles en esta fecha y hora.";
                }
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
                $fechaOnly = date('Y-m-d', strtotime($fechaYhora));
                $stmt->bind_param('ssss', $cargo, $fechaOnly, $hourStart, $hourEnd);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $id_empleado = $row['id_empleado'];
                } else {
                    $errors[] = "No hay empleados disponibles en esta fecha y hora.";
                }
            }
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO citas(id_mascota, id_servicio, id_empleado, fecha, precio, observaciones) VALUES(?,?,?,?,?,?)");
        if (!$stmt) {
            $errors[] = "Error en prepare al insertar cita";
        } else {
            $observaciones = "";
            $stmt_servicio = $mysqli->prepare("SELECT id_servicio FROM servicios WHERE nombre = ? LIMIT 1");
            $stmt_servicio->bind_param('s', $servicio);
            $stmt_servicio->execute();
            $result_servicio = $stmt_servicio->get_result();
            $id_servicio = ($row_servicio = $result_servicio->fetch_assoc()) ? $row_servicio['id_servicio'] : null;
            $stmt_servicio->close();

            if (!$id_mascota || !$id_servicio) {
                $errors[] = "Mascota o servicio no encontrados en la base de datos.";
            }
            $id_servicio = (int) $id_servicio;
            $id_empleado = (int) $id_empleado;

            $stmt->bind_param("iiisds", $id_mascota, $id_servicio, $id_empleado, $fechaYhora, $precio, $observaciones);

            if (!$stmt->execute()) {
                $errors[] = "Error al ejecutar la inserción de la cita.";
            }

            $stmt->close();
        }
    }

    cerrarConexion($mysqli);
    echo json_encode($errors);
    exit();
}
?>