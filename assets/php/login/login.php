<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$response = [
    'status' => 'error',
    'mensaje' => 'Error inesperado',
    'debug' => 'inicio'
];

try {

    include("../conexionBD.php");

    $raw = file_get_contents("php://input");
    $datos = json_decode($raw, true);

    if (!$datos) {
        $response['mensaje'] = 'JSON inválido';
        $response['debug'] = substr($raw, 0, 200);
        echo json_encode($response);
        exit();
    }

    $usuario     = trim($datos['usuario'] ?? '');
    $Contrasena  = trim($datos['contrasenia'] ?? '');

    if (!$usuario || !$Contrasena) {
        $response['mensaje'] = 'Usuario o contraseña vacíos';
        $response['debug'] = 'Campos vacíos';
        echo json_encode($response);
        exit();
    }

    $mysqli = abrirConexion();

    $sql = "
        SELECT 
            Usuario_Id,
            Nombre,
            Usuario,
            Contrasena,
            Correo,
            Rol_Id
        FROM Usuario
        WHERE Usuario = ?
    ";

    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        $response['mensaje'] = 'Error en prepare';
        $response['debug'] = $mysqli->error;
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {

        $fila = $resultado->fetch_assoc();

        if (password_verify($Contrasena, $fila['Contrasena'])) {

            $Usuario_Id = $fila['Usuario_Id'];
            $Nombre     = $fila['Nombre'];
            $Usuario    = $fila['Usuario'];
            $Rol_Id     = $fila['Rol_Id'];

            $_SESSION['id']      = $Usuario_Id;
            $_SESSION['nombre'] = $Nombre;
            $_SESSION['usuario'] = $Usuario;
            $_SESSION['rol']     = $Rol_Id;

            $response = [
                'status'  => 'ok',
                'mensaje' => $Nombre,
                'rol'     => $Rol_Id,
                'debug'   => 'Login exitoso'
            ];

        } else {
            $response['mensaje'] = 'Contraseña incorrecta';
            $response['debug'] = 'password_verify false';
        }

    } else {
        $response['mensaje'] = 'Usuario no encontrado';
        $response['debug'] = 'No existe';
    }

    cerrarConexion($mysqli);

} catch (Exception $e) {
    $response['mensaje'] = 'Error en login';
    $response['debug'] = $e->getMessage();
}

echo json_encode($response);
exit();
?>
