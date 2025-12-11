<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}

include("../../assets/php/conexionBD.php");

if (
    !isset($_GET['id']) || 
    !is_numeric($_GET['id']) ||
    !isset($_GET['cliente_id']) ||
    !is_numeric($_GET['cliente_id'])
) {
    header("Location: ../cliente/clientes.php");
    exit();
}

$mascota_id = intval($_GET['id']);
$cliente_id = intval($_GET['cliente_id']);

$conexion = abrirConexion();

$sql = "DELETE FROM MascotaExpediente WHERE Mascota_Id = ?";
$stmt = $conexion->prepare($sql);

$sql2 = "DELETE FROM Mascota WHERE Mascota_Id = ?";
$stmt2 = $conexion->prepare($sql2);

if (!$stmt && !$stmt2) {
    cerrarConexion($conexion);
    header("Location: ../cliente/editar-cliente.php?id=$cliente_id&error=prepare");
    exit();
}

$stmt->bind_param("i", $mascota_id);
$stmt->execute();

$stmt->close();

$stmt2->bind_param("i", $mascota_id);
$stmt2->execute();

$stmt2->close();
cerrarConexion($conexion);

header("Location: ../cliente/editar-cliente.php?id=$cliente_id&eliminado=1");
exit();
?>
