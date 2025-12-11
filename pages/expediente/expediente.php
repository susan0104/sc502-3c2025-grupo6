<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: ../index.php");
  exit();
}

if (!isset($_GET['mascota_id']) || !is_numeric($_GET['mascota_id'])) {
  header("Location: ./listaExpedientes.php");
  exit();
}

$mascota_id = intval($_GET['mascota_id']);

include("../../assets/php/conexionBD.php");
$mysqli = abrirConexion();

$sqlMascota = "
    SELECT 
        m.Mascota_Id,
        m.Nombre  MascotaNombre,
        m.Raza,
        m.Edad,
        m.Observaciones  ObservacionesMascota,
        m.Cliente_Id,
        e.Nombre  EspecieNombre,
        m.Foto
    FROM Mascota m
    INNER JOIN MascotaEspecie e ON m.Especie_Id = e.Especie_Id
    WHERE m.Mascota_Id = ?
";

$stmtMascota = $mysqli->prepare($sqlMascota);
$stmtMascota->bind_param("i", $mascota_id);
$stmtMascota->execute();
$resultMascota = $stmtMascota->get_result();

if ($resultMascota->num_rows === 0) {
  $stmtMascota->close();
  cerrarConexion($mysqli);
  header("Location: ./listaExpedientes.php");
  exit();
}

$mascota = $resultMascota->fetch_assoc();
$stmtMascota->close();

$cliente = [];

$sqlCliente = "SELECT Nombre, Correo FROM Cliente WHERE Cliente_Id = ?";
$stmtCliente = $mysqli->prepare($sqlCliente);
$stmtCliente->bind_param("i", $mascota['Cliente_Id']);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();

if ($resultCliente->num_rows > 0) {
  $cliente = $resultCliente->fetch_assoc();
}
$stmtCliente->close();


$expediente = [];

$sqlExp = "
    SELECT Observaciones, Alergias, Tratamientos
    FROM MascotaExpediente
    WHERE Mascota_Id = ?
    ORDER BY Expediente_Id DESC
    LIMIT 1
";
$stmtExp = $mysqli->prepare($sqlExp);
$stmtExp->bind_param("i", $mascota_id);
$stmtExp->execute();
$resultExp = $stmtExp->get_result();

if ($resultExp->num_rows > 0) {
  $rowExp = $resultExp->fetch_assoc();
  if (!empty($rowExp['Observaciones'])) {
    $expediente['Observaciones'] = $rowExp['Observaciones'];
  }
  $expediente['Alergias'] = $rowExp['Alergias'];
  $expediente['Tratamientos'] = $rowExp['Tratamientos'];
}
$stmtExp->close();

$citas = [];
$ultimaVisita = "Sin visitas registradas";

$sqlCitas = "
    SELECT 
    c.Fecha,
    c.Estado,
    c.Observaciones,
    s.Nombre AS ServicioNombre,
    u.Nombre AS VeterinarioNombre
FROM Citas c
INNER JOIN Servicio s ON c.Servicio_Id = s.Servicio_Id
LEFT JOIN Usuario u ON c.Usuario_Id = u.Usuario_Id
WHERE c.Mascota_Id = ?
ORDER BY c.Fecha DESC

";
$stmtCitas = $mysqli->prepare($sqlCitas);
$stmtCitas->bind_param("i", $mascota_id);
$stmtCitas->execute();
$resCitas = $stmtCitas->get_result();

while ($fila = $resCitas->fetch_assoc()) {
  $citas[] = $fila;
}

$stmtCitas->close();
cerrarConexion($mysqli);

if (!empty($citas)) {
  $fecha = new DateTime($citas[0]['Fecha']);
  $ultimaVisita = $fecha->format('d/m/Y');
}

function fDMY($fechaStr)
{
  if (!$fechaStr)
    return "";
  $f = new DateTime($fechaStr);
  return $f->format('d/m/Y');
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

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

  <link rel="stylesheet" href="../../assets/css/style-layout.css">
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <?php include("../../layout/aside.php"); ?>
      <div class="col-12 col-lg-9 col-xl-10 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom">
          <h1 class="m-0">Revisión de Expediente</h1>

          <button type="button" class="btn btn-secondary" onclick="window.location.href='listaExpedientes.php'">
            <i class="fa-solid fa-right-from-bracket me-1"></i>
          </button>
        </div>

        <div class="row">
          <div class="col-6">
            <div class="text-center mb-4">
              <img src="<?= $mascota['Foto'] ?>" class="rounded shadow-sm"
                style="width: 180px; height:180px; object-fit:cover;">
              <h4 class="mt-3 mb-0"><?= htmlspecialchars($mascota['MascotaNombre']) ?></h4>
              <p class="text-muted">
                <?= htmlspecialchars($mascota['EspecieNombre']) ?> · <?= htmlspecialchars($mascota['Raza']) ?>
              </p>
            </div>
          </div>
          <div class="col-6">


            <div class="row">

              <div class="col-md-6 mb-4">
                <label class="form-label">Mascota</label>
                <input type="text" class="form-control" disabled
                  value="<?= htmlspecialchars($mascota['MascotaNombre']) ?>">
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label">Propietario</label>
                <input type="text" class="form-control" disabled
                  value="<?= htmlspecialchars($cliente['Nombre'] ?? '') ?>">
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label">Especie</label>
                <input type="text" class="form-control" disabled
                  value="<?= htmlspecialchars($mascota['EspecieNombre']) ?>">
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label">Raza</label>
                <input type="text" class="form-control" disabled value="<?= htmlspecialchars($mascota['Raza']) ?>">
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label">Edad</label>
                <input type="text" class="form-control" disabled value="<?= intval($mascota['Edad']) ?> años">
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label">Última visita</label>
                <input type="text" class="form-control" disabled value="<?= $ultimaVisita ?>">
              </div>

            </div>
          </div>
          <div class="col-12">
            <form id="formEditarExpediente">
              <input type="hidden" id="mascota_id" value="<?= $mascota['Mascota_Id'] ?>">

              <div class="row">
                <div class="col-md-12 mb-4">
                  <label class="form-label">Observaciones</label>
                  <textarea class="form-control" rows="3"
                    id="obs"><?= htmlspecialchars($expediente['Observaciones'] ?? '') ?></textarea>
                </div>

                <div class="col-md-12 mb-4">
                  <label class="form-label">Alergias</label>
                  <textarea class="form-control" rows="3"
                    id="alergias"><?= htmlspecialchars($expediente['Alergias'] ?? '') ?></textarea>
                </div>

                <div class="col-md-12 mb-4">
                  <label class="form-label">Tratamientos</label>
                  <textarea class="form-control" rows="3"
                    id="tratamientos"><?= htmlspecialchars($expediente['Tratamientos'] ?? '') ?></textarea>
                </div>

                <div class="col-md-12 d-flex align-items-end mb-4">
                  <button type="submit" class="btn btn-primary w-100">
                    Guardar Cambios
                  </button>
                </div>

              </div>
            </form>
          </div>
        </div>





        <h3 class="m-0 mt-4">Historial de Citas</h3>
        <hr>

        <div class="table-responsive mt-3">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th class="color-principal">Fecha</th>
                <th class="color-principal">Estado</th>
                <th class="color-principal">Servicio</th>
                <th class="color-principal">Veterinario</th>
              </tr>
            </thead>
            <tbody>

              <?php if (empty($citas)): ?>
                <tr>
                  <td colspan="4" class="text-center text-muted">
                    No hay citas registradas.
                  </td>
                </tr>

              <?php else: ?>

                <?php foreach ($citas as $c): ?>
                  <tr>
                    <td><?= fDMY($c['Fecha']) ?></td>

                    <td><?= htmlspecialchars($c['Estado'] ?? 'Desconocido') ?></td>

                    <td><?= htmlspecialchars($c['ServicioNombre']) ?></td>

                    <td><?= htmlspecialchars($c['VeterinarioNombre'] ?? '-') ?></td>
                  </tr>
                <?php endforeach; ?>

              <?php endif; ?>

            </tbody>
          </table>
        </div>


      </div>


    </div>

  </div>
  </div>

  <script>
    document.getElementById("formEditarExpediente").addEventListener("submit", function (e) {
      e.preventDefault();

      const datos = {
        mascota_id: document.getElementById("mascota_id").value,
        obs: document.getElementById("obs").value,
        alergias: document.getElementById("alergias").value,
        tratamientos: document.getElementById("tratamientos").value
      };

      fetch("../assets/php/expediente/actualizarExpediente.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
      })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            alert("Expediente actualizado correctamente");
            location.reload();
          } else {
            alert("Error: " + data.message);
          }
        });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="actualizar-expediente.js"></script>
1
</body>

</html>