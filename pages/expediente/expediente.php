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

include("../assets/php/conexionBD.php");
$mysqli = abrirConexion();

$sqlMascota = "
    SELECT 
        m.Mascota_Id,
        m.Nombre AS MascotaNombre,
        m.Raza,
        m.Edad,
        m.Observaciones AS ObservacionesMascota,
        m.Cliente_Id,
        e.Nombre AS EspecieNombre
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

$cliente = [
    'Nombre' => 'Sin propietario',
    'Correo' => ''
];

$sqlCliente = "SELECT Nombre, Correo FROM Cliente WHERE Cliente_Id = ?";
$stmtCliente = $mysqli->prepare($sqlCliente);
$stmtCliente->bind_param("i", $mascota['Cliente_Id']);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();

if ($resultCliente->num_rows > 0) {
    $cliente = $resultCliente->fetch_assoc();
}
$stmtCliente->close();


$expediente = [
    'Observaciones' => $mascota['ObservacionesMascota'],
    'Alergias'      => '',
    'Tratamientos'  => ''
];

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
    $expediente['Alergias']     = $rowExp['Alergias'];
    $expediente['Tratamientos'] = $rowExp['Tratamientos'];
}
$stmtExp->close();

$citas = [];
$ultimaVisita = "Sin visitas registradas";

$sqlCitas = "
    SELECT 
        c.Fecha,
        c.Observaciones,
        s.Nombre       AS ServicioNombre,
        s.Descripcion  AS ServicioDescripcion,
        u.Nombre       AS VeterinarioNombre
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

function fDMY($fechaStr) {
    if (!$fechaStr) return "";
    $f = new DateTime($fechaStr);
    return $f->format('d/m/Y');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Golden Paws</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

    <link rel="stylesheet" href="../assets/css/style-layout.css">
    <link rel="stylesheet" href="../assets/css/style-expediente.css">
</head>

<body>

  <?php ?>

  <nav class="navbar bg-white shadow-sm d-lg-none">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <img src="../assets/img/logo.png" alt="Golden Paws" style="height:36px">
      </a>
      <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#MenuMovil">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </nav>

  <div class="offcanvas offcanvas-start" id="MenuMovil">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">
        <img src="../assets/img/logo.png">
      </h5>
      <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
      <nav class="sidebar-nav p-3 d-flex flex-column justify-content-between">
        <ul class="nav flex-column gap-1">
          <li class="nav-item"><a class="nav-link" href="../pages/inicio.html"><i class="fa-solid fa-house me-2"></i>Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="../pages/clientes.html"><i class="fa-solid fa-user-group me-2"></i>Clientes</a></li>
          <li class="nav-item"><a class="nav-link active" href="../pages/listaExpedientes.php"><i class="fa-solid fa-folder-open me-2"></i>Expedientes</a></li>
          <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-calendar-check me-2"></i>Citas</a></li>
        </ul>
        <div class="border-top pt-3">
          <a class="nav-link text-danger" href="../index.html"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
        </div>
      </nav>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">

      <!-- SIDEBAR DESKTOP -->
      <aside class="col-lg-3 col-xl-2 d-none d-lg-flex">
        <div class="sidebar shadow-sm">
          <div class="p-3 border-bottom">
            <img src="../assets/img/logo.png" style="height:40px">
          </div>
          <nav class="sidebar-nav p-3 d-flex flex-column justify-content-between">
            <ul class="nav flex-column gap-1">
              <li><a class="nav-link" href="../pages/inicio.html">Inicio</a></li>
              <li><a class="nav-link" href="../pages/clientes.html">Clientes</a></li>
              <li><a class="nav-link active" href="../pages/listaExpedientes.php">Expedientes</a></li>
            </ul>
            <div class="border-top pt-3">
              <a class="nav-link text-danger" href="../index.html">Cerrar sesión</a>
            </div>
          </nav>
        </div>
      </aside>

      <!-- CONTENIDO PRINCIPAL -->
      <main class="col-lg-9 col-xl-10 px-4 py-4">

        <a href="../pages/listaExpedientes.php" class="btn btn-volver mb-3">
            <i class="fa-solid fa-arrow-left me-2"></i>Volver
        </a>

        <h2 class="text-center titulo-seccion mb-4">
            Expediente de <?= htmlspecialchars($mascota['MascotaNombre']) ?>
        </h2>

        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalEditar">
            <i class="fa-solid fa-pen-to-square me-2"></i>Editar expediente
        </a>

        <div class="card-expediente">
          <div class="row align-items-center">

            <div class="col-md-4 text-center mb-4">
              <img src="../assets/img/perroEjemplo.jpg" class="foto-mascota mb-3">
              <h5><?= htmlspecialchars($mascota['MascotaNombre']) ?></h5>
              <p class="text-muted">
                <?= htmlspecialchars($mascota['EspecieNombre']) ?> - <?= htmlspecialchars($mascota['Raza']) ?>
              </p>
            </div>

            <div class="col-md-8">
              <h5 class="titulo-seccion mb-3">Datos Generales</h5>

              <p><strong>Propietario:</strong> <?= htmlspecialchars($cliente['Nombre']) ?></p>
              <p><strong>Teléfono:</strong> -</p>
              <p><strong>Edad:</strong> <?= intval($mascota['Edad']) ?> años</p>
              <p><strong>Última visita:</strong> <?= $ultimaVisita ?></p>

              <p><strong>Observaciones:</strong><br><?= nl2br(htmlspecialchars($expediente['Observaciones'])) ?></p>

              <?php if (!empty($expediente['Alergias'])): ?>
                <p><strong>Alergias:</strong><br><?= nl2br(htmlspecialchars($expediente['Alergias'])) ?></p>
              <?php endif; ?>

              <?php if (!empty($expediente['Tratamientos'])): ?>
                <p><strong>Tratamientos:</strong><br><?= nl2br(htmlspecialchars($expediente['Tratamientos'])) ?></p>
              <?php endif; ?>

            </div>
          </div>

          <div class="mt-4">
            <h5 class="titulo-seccion mb-3">Historial de Citas</h5>

            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Motivo</th>
                  <th>Tratamiento</th>
                  <th>Veterinario</th>
                  <th>Observaciones</th>
                </tr>
              </thead>
              <tbody>
              <?php if (empty($citas)): ?>
                <tr><td colspan="5" class="text-center text-muted">No hay citas registradas.</td></tr>
              <?php else: foreach ($citas as $c): ?>
                <tr>
                  <td><?= fDMY($c['Fecha']) ?></td>
                  <td><?= htmlspecialchars($c['ServicioNombre']) ?></td>
                  <td><?= htmlspecialchars($c['ServicioDescripcion'] ?: '-') ?></td>
                  <td><?= htmlspecialchars($c['VeterinarioNombre'] ?: '-') ?></td>
                  <td><?= htmlspecialchars($c['Observaciones'] ?: '-') ?></td>
                </tr>
              <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </main>
    </div>
  </div>

  <div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Editar expediente de <?= htmlspecialchars($mascota['MascotaNombre']) ?></h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form id="formEditarExpediente">
          <div class="modal-body">

            <input type="hidden" id="mascota_id" value="<?= $mascota_id ?>">

            <div class="mb-3">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea id="obs" rows="3" class="form-control"><?= htmlspecialchars($expediente['Observaciones']) ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Alergias</label>
              <textarea id="alergias" rows="3" class="form-control"><?= htmlspecialchars($expediente['Alergias']) ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Tratamientos</label>
              <textarea id="tratamientos" rows="3" class="form-control"><?= htmlspecialchars($expediente['Tratamientos']) ?></textarea>
            </div>

          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary" type="submit">Guardar cambios</button>
          </div>

        </form>

      </div>
    </div>
  </div>

  <script>
document.getElementById("formEditarExpediente").addEventListener("submit", function(e){
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
        if(data.success){
            alert("Expediente actualizado correctamente");
            location.reload();
        } else {
            alert("Error: " + data.message);
        }
    });
});
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
