<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}

$paginaActiva = 'clientes';

include("../../assets/php/conexionBD.php");
$mysqli = abrirConexion();

$planes = [];
$sql = "SELECT Plan_Id, Nombre FROM ClientePlan ORDER BY Plan_Id ASC";
$resultado = $mysqli->query($sql);

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $planes[] = $fila;
    }
}

cerrarConexion($mysqli);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Veterinaria Golden Paws</title>

    <link rel="icon" type="image/x-icon" href="../favicon.ico" />

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

    <link rel="stylesheet" href="../assets/css/style-layout.css" />
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <?php include("../../layout/aside.php"); ?>
            <div class="col-12 col-lg-9 col-xl-10 py-4">

                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom">
                    <h1 class="m-0">Creación de Cliente</h1>

                    <button type="button" class="btn btn-secondary" onclick="window.location.href = 'clientes.php'">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>
                    </button>
                </div>
                <form id="formCrearCliente">
                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Identificación</label>
                            <input type="text" class="form-control" id="identificacion" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date" class="form-control" id="fecha" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Plan de fidelidad</label>
                            <select class="form-select" id="plan" required>
                                <option value="" disabled selected>Seleccione un plan</option>
                                <?php foreach ($planes as $plan): ?>
                                    <option value="<?= $plan['Plan_Id'] ?>">
                                        <?= $plan['Nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 d-flex align-items-end mb-4">
                            <button type="submit" class="btn btn-primary w-100">Guardar</button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="crear-cliente.js"></script>
</body>

</html>