<?php
header("Content-Type: application/json");

include("conexionBD.php");
$mysqli = abrirConexion();

$data = json_decode(file_get_contents("php://input"), true);

$mascota_id   = intval($data['mascota_id']);
$obs          = $data['obs'] ?? "";
$alergias     = $data['alergias'] ?? "";
$tratamientos = $data['tratamientos'] ?? "";

$sql = "
    INSERT INTO MascotaExpediente (Mascota_Id, Observaciones, Alergias, Tratamientos)
    VALUES (?, ?, ?, ?)
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("isss", $mascota_id, $obs, $alergias, $tratamientos);

if($stmt->execute()){
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
cerrarConexion($mysqli);
?>
