<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}

$paginaActiva = 'clientes';

include("../../assets/php/conexionBD.php");
$mysqli = abrirConexion();


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$clienteId = intval($_GET['id']);

$sqlCliente = "
    SELECT 
        Cliente_Id,
        Nombre,
        Identificacion,
        FechaNacimiento,
        Correo,
        Plan_Id
    FROM Cliente
    WHERE Cliente_Id = ?
";

$stmtCliente = $mysqli->prepare($sqlCliente);
$stmtCliente->bind_param("i", $clienteId);
$stmtCliente->execute();
$resultadoCliente = $stmtCliente->get_result();

if ($resultadoCliente->num_rows === 0) {
    header("Location: clientes.php");
    exit();
}

$cliente = $resultadoCliente->fetch_assoc();
$stmtCliente->close();

$planes = [];
$sqlPlanes = "SELECT Plan_Id, Nombre FROM ClientePlan ORDER BY Plan_Id ASC";
$resultadoPlanes = $mysqli->query($sqlPlanes);

if ($resultadoPlanes) {
    while ($fila = $resultadoPlanes->fetch_assoc()) {
        $planes[] = $fila;
    }
}

$mascotas = [];

$sqlMascotas = "
    SELECT 
        Mascota_Id,
        Nombre,
        Edad
    FROM Mascota
    WHERE Cliente_Id = ?
    ORDER BY Nombre ASC
";

$stmtMascotas = $mysqli->prepare($sqlMascotas);
$stmtMascotas->bind_param("i", $clienteId);
$stmtMascotas->execute();
$resultadoMascotas = $stmtMascotas->get_result();

if ($resultadoMascotas) {
    while ($fila = $resultadoMascotas->fetch_assoc()) {
        $mascotas[] = $fila;
    }
}

$stmtMascotas->close();


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
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <?php include("../../layout/aside.php"); ?>

            <div class="col-12 col-lg-9 col-xl-10 py-4">

                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom">
                    <h1 class="m-0">Edición de Cliente</h1>

                    <button type="button" class="btn btn-secondary" onclick="window.location.href='clientes.php'">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>
                    </button>
                </div>

                <form id="formEditarCliente">

                    <div class="row">
                        <input type="hidden" id="cliente_id" value="<?= $cliente['Cliente_Id'] ?>">

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre"
                                value="<?= htmlspecialchars($cliente['Nombre']) ?>" id="nombre" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Identificación</label>
                            <input type="text" class="form-control" name="identificacion" id="identificacion"
                                value="<?= htmlspecialchars($cliente['Identificacion']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date" class="form-control" name="fecha" id="fecha"
                                value="<?= $cliente['FechaNacimiento'] ?>" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="correo" id="correo"
                                value="<?= htmlspecialchars($cliente['Correo']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Plan de fidelidad</label>
                            <select class="form-select" name="plan" id="plan" required>
                                <option value="" disabled>Seleccione un plan</option>

                                <?php foreach ($planes as $plan): ?>
                                    <option value="<?= $plan['Plan_Id'] ?>" <?= ($plan['Plan_Id'] == $cliente['Plan_Id']) ? 'selected' : '' ?>>
                                        <?= $plan['Nombre'] ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="col-md-6 d-flex align-items-end mb-4 gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                Guardar Cambios
                            </button>
                            <button type="button" class="btn btn-outline-secondary w-100"
                                onclick="window.location.href='../mascota/agregar-mascota.php?cliente_id=<?= $cliente['Cliente_Id'] ?>'">
                                Agregar Mascota
                            </button>
                        </div>

                    </div>
                </form>
                <h3 class="m-0">Lista de Mascotas</h3>
                <hr>
                <div class="table-responsive mt-4">
                    <table class="table table-striped table-hover align-middle">

                        <thead>
                            <tr>
                                <th class="color-principal">Mascota</th>
                                <th class="color-principal">Edad</th>
                                <th class="text-center color-principal">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($mascotas)): ?>
                                <?php foreach ($mascotas as $mascota): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($mascota['Nombre']) ?></td>
                                        <td><?= htmlspecialchars($mascota['Edad']) ?> años</td>
                                        <td class="text-center">
                                            <button class="btn btn-warning me-1"
                                                onclick="window.location.href='../mascota/editar-mascota.php?mascota_id=<?= $mascota['Mascota_Id'] ?>'">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <button class="btn btn-danger me-1"
                                                onclick="eliminarMascota(<?= $mascota['Mascota_Id'] ?>, <?= $cliente['Cliente_Id'] ?>)">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                            <button class="btn btn-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Este cliente aún no tiene mascotas registradas
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>



                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="editar-cliente.js"></script>
    <script>
        function eliminarMascota(mascotaId, clienteId) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción eliminará la mascota permanentemente",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../mascota/eliminarmascota-service.php?id=" + mascotaId + "&cliente_id=" + clienteId;
                }
            });
        }
    </script>

</body>

</html>