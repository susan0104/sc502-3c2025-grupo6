<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

include("../../assets/php/conexionBD.php");
$mysqli = abrirConexion();

$sql = "
    SELECT 
        m.Mascota_Id,
        m.Nombre AS MascotaNombre,
        m.Raza,
        m.Fecha_registro,
        c.Nombre AS DuenoNombre,
        e.Nombre AS EspecieNombre,
        ex.Observaciones,
        ex.Alergias,
        ex.Vacunas,
        ex.Tratamientos,
        ex.Ultima_actualizacion
    FROM Mascota m
    INNER JOIN Cliente c ON m.Cliente_Id = c.Cliente_Id
    INNER JOIN MascotaEspecie e ON m.Especie_Id = e.Especie_Id
    LEFT JOIN MascotaExpediente ex ON ex.Mascota_Id = m.Mascota_Id
    ORDER BY m.Mascota_Id DESC
";

$result = $mysqli->query($sql);

$mascotas = [];
if ($result) {
    while ($fila = $result->fetch_assoc()) {
        $mascotas[] = $fila;
    }
}

function obtenerUltimaVisita($mysqli, $mascota_id)
{
    $sql = "SELECT Fecha FROM Citas WHERE Mascota_Id = ? ORDER BY Fecha DESC LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $mascota_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return "Sin visitas";
    }

    $fila = $result->fetch_assoc();
    return (new DateTime($fila['Fecha']))->format("d/m/Y");
}
$paginaActiva = 'expedientes';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Golden Paws</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">

    <!-- Google fonts -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

    <link rel="stylesheet" href="../assets/css/style-layout.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <?php include("../../layout/aside.php"); ?>

            <div class="col-12 col-lg-9 col-xl-10 py-4">

                <h1 class="mb-4 border-bottom">Expedientes de Mascotas</h1>

                <!-- Filtros -->
                <div class="row">
                    <div class="col-md-5 col-12 mb-4">
                        <label for="buscarMascota" class="form-label">Nombre Mascota</label>
                        <input type="text" class="form-control" id="buscarMascota" placeholder="Luna, Max...">
                    </div>

                    <div class="col-md-5 col-12 mb-4">
                        <label for="buscarDueno" class="form-label">Propietario</label>
                        <input type="text" class="form-control" id="buscarDueno" placeholder="Sofía, Mario...">
                    </div>

                    <div class="col-md-2 col-12 d-flex align-items-end mb-4">
                        <button type="button" class="btn btn-primary w-100 me-2" onclick="limpiarFiltros()">
                            <i class="fa-solid fa-eraser"></i>
                        </button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-responsive mt-4">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="color-principal">Nombre</th>
                                <th class="color-principal">Especie</th>
                                <th class="color-principal">Raza</th>
                                <th class="color-principal">Propietario</th>
                                <th class="color-principal">Última Visita</th>
                                <th class="text-center color-principal">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaMascotas">

                            <?php foreach ($mascotas as $m):
                                $ultima = obtenerUltimaVisita($mysqli, $m['Mascota_Id']);
                                ?>
                                <tr class="fila-mascota" data-mascota="<?= strtolower($m['MascotaNombre']) ?>"
                                    data-dueno="<?= strtolower($m['DuenoNombre']) ?>">

                                    <td><?= htmlspecialchars($m['MascotaNombre']) ?></td>
                                    <td><?= htmlspecialchars($m['EspecieNombre']) ?></td>
                                    <td><?= htmlspecialchars($m['Raza']) ?></td>
                                    <td><?= htmlspecialchars($m['DuenoNombre']) ?></td>
                                    <td><?= $ultima ?></td>

                                    <td class="text-center">
                                        <button class="btn btn-success me-1"
                                            onclick="window.location.href='./expediente.php?mascota_id=<?= $m['Mascota_Id'] ?>'">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const buscarMascota = document.getElementById("buscarMascota");
        const buscarDueno = document.getElementById("buscarDueno");

        function filtrar() {
            const nombre = buscarMascota.value.toLowerCase();
            const dueno = buscarDueno.value.toLowerCase();

            document.querySelectorAll(".fila-mascota").forEach(fila => {
                const coincideNombre = fila.dataset.mascota.includes(nombre);
                const coincideDueno = fila.dataset.dueno.includes(dueno);

                fila.style.display = (coincideNombre && coincideDueno) ? "" : "none";
            });
        }

        buscarMascota.addEventListener("input", filtrar);
        buscarDueno.addEventListener("input", filtrar);

        function limpiarFiltros() {
            buscarMascota.value = "";
            buscarDueno.value = "";
            filtrar();
        }
    </script>

</body>

</html>