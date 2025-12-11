<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}

include("../../assets/php/conexionBD.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$cliente_id = intval($_GET['id']);

$conexion = abrirConexion();

$sqlExpediente = "DELETE e FROM MascotaExpediente e
INNER JOIN Mascota m ON e.Mascota_Id = m.Mascota_Id
WHERE m.Cliente_Id = ?;
";
$stmtExpediente = $conexion->prepare($sqlExpediente);
$stmtExpediente->bind_param("i", $cliente_id);
$stmtExpediente->execute();

$sqlMascota = "DELETE FROM Mascota WHERE Cliente_Id = ?";
$stmtMascota = $conexion->prepare($sqlMascota);
$stmtMascota->bind_param("i", $cliente_id);
$stmtMascota->execute();

$sqlCliente = "DELETE FROM Cliente WHERE Cliente_Id = ?";
$stmtCliente = $conexion->prepare($sqlCliente);
$stmtCliente->bind_param("i", $cliente_id);
$stmtCliente->execute();

$stmtMascota->close();
$stmtCliente->close();
$stmtExpediente->close();
cerrarConexion($conexion);

header("Location: clientes.php?eliminado=1");
exit();
?>