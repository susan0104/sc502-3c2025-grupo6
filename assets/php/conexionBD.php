<?php
function abrirConexion()
{

    $host = "127.0.0.1";
    $user = "root";
    $password = "ambienteWeb1234";
    $db = "golden_paws";

    $mysqli = new mysqli($host, $user, $password, $db);

    if ($mysqli->connect_errno) {
        throw new Exception("Error de conexion: " . $mysqli->connect_errno);
    }

    $mysqli->set_charset("utf8mb4");
    return $mysqli;
}

function cerrarConexion($mysqli)
{
    $mysqli->close();
}
?>