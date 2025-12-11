<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../conexionBD.php';
$mysqli = abrirConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cliente_id = (int) ($_POST['cliente'] ?? 0);
    $mascota_id = (int) ($_POST['mascota'] ?? 0);
    $servicio_id = (int) ($_POST['servicio'] ?? 0);
    $fecha = trim($_POST['fecha'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $precio = (float) ($_POST['precio'] ?? 0);

    $errors = [];

    if ($cliente_id === 0)
        $errors[] = "El cliente es obligatorio.";
    if ($mascota_id === 0)
        $errors[] = "La mascota es obligatoria.";
    if ($servicio_id === 0)
        $errors[] = "El servicio es obligatorio.";

    if ($fecha === '' || $hora === '') {
        $errors[] = "Fecha y hora son obligatorias.";
    }

    $dateStr = "$fecha $hora";
    $dt = DateTime::createFromFormat('Y-m-d H:i', $dateStr);
    $errorsDt = DateTime::getLastErrors();

    if (!$dt || $errorsDt) {
        $errors[] = "Formato inválido de fecha u hora.";
    } else {
        $now = new DateTime();
        if ($dt <= $now) {
            $errors[] = "La fecha y hora deben ser posteriores a la actual.";
        }
    }

    if (!empty($errors)) {
        echo json_encode($errors);
        exit();
    }

    $fechaInicio = $dt->format("Y-m-d H:i:00");

    $stmt = $mysqli->prepare("
        SELECT Nombre, Duracion_estimada 
        FROM Servicio 
        WHERE Servicio_Id = ?
    ");
    $stmt->bind_param("i", $servicio_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$row = $result->fetch_assoc()) {
        $errors[] = "El servicio no existe.";
        echo json_encode($errors);
        exit();
    }

    $nombreServicio = $row['Nombre'];
    $duracionMinutos = (int) $row['Duracion_estimada'];
    $stmt->close();

    if ($nombreServicio === "Baño Grooming") {
        $rolRequeridos = [2, 3];
    } else {
        $rolRequeridos = [2];
    }

    $dtFin = clone $dt;
    $dtFin->modify("+$duracionMinutos minutes");
    $fechaFin = $dtFin->format("Y-m-d H:i:00");

    $placeholders = implode(',', array_fill(0, count($rolRequeridos), '?'));
    $types = str_repeat("i", count($rolRequeridos));

    $sql = "
        SELECT u.Usuario_Id
        FROM Usuario u
        WHERE u.Rol_Id IN ($placeholders)
        AND NOT EXISTS (
            SELECT 1
            FROM Citas c
            WHERE c.Usuario_Id = u.Usuario_Id
            AND (
                c.Fecha < ?
                AND DATE_ADD(c.Fecha, INTERVAL 
                    (SELECT Duracion_estimada FROM Servicio WHERE Servicio_Id = c.Servicio_Id) MINUTE
                ) > ?
            )
        )
        LIMIT 1
    ";

    $stmt = $mysqli->prepare($sql);
    $params = array_merge($rolRequeridos, [$fechaFin, $fechaInicio]);
    $stmt->bind_param($types . "ss", ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$empleado = $result->fetch_assoc()) {
        $errors[] = "No hay empleados disponibles para ese horario.";
        echo json_encode($errors);
        exit();
    }

    $empleado_id = $empleado['Usuario_Id'];
    $stmt->close();

    $estado = "Programada";

    $stmt = $mysqli->prepare("
        INSERT INTO Citas (Mascota_Id, Servicio_Id, Usuario_Id, Fecha, Estado, Precio)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iiissd",
        $mascota_id,
        $servicio_id,
        $empleado_id,
        $fechaInicio,
        $estado,
        $precio
    );

    if (!$stmt->execute()) {
        $errors[] = "Error al insertar la cita: " . $stmt->error;
    }

    $stmt->close();
    cerrarConexion($mysqli);

    echo json_encode($errors);
    exit();
}
?>