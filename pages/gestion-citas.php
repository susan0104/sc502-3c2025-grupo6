<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../assets/php/conexionBD.php';

$mysqli = abrirConexion();
$servicios = $mysqli->query("SELECT id_servicio, nombre, precio FROM servicios");
$citas = $mysqli->query(
  "SELECT c.id_cita, c.id_mascota, c.id_servicio, DATE(c.fecha) AS fecha, TIME(c.fecha) AS hora, "
  . "s.nombre AS servicio, s.precio AS precio, m.nombre AS mascota, u.nombre AS cliente "
  . "FROM citas c "
  . "LEFT JOIN mascotas m ON c.id_mascota = m.id_mascota "
  . "LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario "
  . "LEFT JOIN servicios s ON c.id_servicio = s.id_servicio"
);
$clientes = $mysqli->query("SELECT id_usuario, nombre FROM usuarios");
$usuario = isset($_GET['cliente']) ? htmlspecialchars($_GET['cliente']) : '';

function obtenerMascotasUsuario($nombreUsuario, $mysqli)
{
  if (empty($nombreUsuario)) {
    return null;
  }
  $query = "SELECT m.id_mascota, m.nombre FROM mascotas m 
            INNER JOIN usuarios u ON m.id_usuario = u.id_usuario 
            WHERE u.nombre = ?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param('s', $nombreUsuario);
  $stmt->execute();
  return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestión de Citas | Veterinaria Golden Paws</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico" />
  <!-- Google Fonts -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" />
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/style-layout.css" />
  <link rel="stylesheet" href="../assets/css/style-gestion-citas.css" />
</head>

<body>
  <nav class="navbar bg-white shadow-sm d-lg-none">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <img src="../assets/img/logo.png" alt="Golden Paws" style="height: 36px" />
      </a>
      <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#MenuMovil"
        aria-controls="MenuMovil" aria-label="Abrir menú">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </nav>

  <div class="offcanvas offcanvas-start" tabindex="-1" id="MenuMovil" aria-labelledby="MenuMovilLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title d-flex align-items-center gap-2" id="MenuMovilLabel">
        <img src="../assets/img/logo.png" alt="Golden Paws" />
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body p-0">
      <nav class="sidebar-nav p-3 d-flex flex-column justify-content-between" style="height: 100%">
        <ul class="nav flex-column gap-1">
          <li class="nav-item">
            <a class="nav-link" href="../pages/inicio.html"><i class="fa-solid fa-house me-2"></i>Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../pages/clientes.html"><i class="fa-solid fa-user-group me-2"></i>Clientes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../pages/listaExpedientes.html"><i
                class="fa-solid fa-folder-open me-2"></i>Expedientes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="../pages/gestion-citas.html"><i
                class="fa-solid fa-calendar-check me-2"></i>Citas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa-solid fa-chart-line me-2"></i>Reportes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa-regular fa-life-ring me-2"></i>Soporte</a>
          </li>
        </ul>
        <div class="mt-auto pt-3 border-top">
          <a class="nav-link text-danger" href="../index.html"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar
            sesión</a>
        </div>
      </nav>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <aside class="col-lg-3 col-xl-2 d-none d-lg-flex">
        <div class="sidebar shadow-sm">
          <div class="p-3 d-flex align-items-center gap-2 border-bottom">
            <img src="../assets/img/logo.png" alt="Golden Paws" style="height: 40px" />
          </div>
          <nav class="sidebar-nav p-3 d-flex flex-column justify-content-between" style="height: 100%">
            <ul class="nav flex-column gap-1">
              <li class="nav-item">
                <a class="nav-link" href="../pages/inicio.html"><i class="fa-solid fa-house me-2"></i>Inicio</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../pages/clientes.html"><i
                    class="fa-solid fa-user-group me-2"></i>Clientes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../pages/listaExpedientes.html"><i
                    class="fa-solid fa-folder-open me-2"></i>Expedientes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="../pages/gestion-citas.html"><i
                    class="fa-solid fa-calendar-check me-2"></i>Citas</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa-solid fa-chart-line me-2"></i>Reportes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa-regular fa-life-ring me-2"></i>Soporte</a>
              </li>
            </ul>
            <div class="mt-auto pt-3 border-top">
              <a class="nav-link text-danger" href="../index.html"><i
                  class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
            </div>
          </nav>
        </div>
      </aside>

      <div class="col-12 col-lg-9 col-xl-10 py-4">
        <h1 class="mb-4 border-bottom">Gestión de Citas</h1>

        <div class="row">
          <div class="col-md-2 col-12 mb-3">
            <label class="form-label">Cliente</label>
            <select id="cliente" class="form-control"
              onchange="window.location.href='gestion-citas.php?cliente=' + encodeURIComponent(this.value);">
              <option value="" disabled selected>
                Selecciona un cliente
              </option>
              <?php
              $clientes->data_seek(0);
              while ($fila = $clientes->fetch_assoc()):
                ?>
                <option value="<?php echo htmlspecialchars($fila['nombre']); ?>" <?= ($usuario === $fila['nombre']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($fila['nombre']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2 col-12 mb-3">
            <label class="form-label">Mascota</label>
            <?php if ($usuario == ''): ?>
              <input type="text" id="mascota" class="form-control" value="Selecciona un cliente" disabled />
            <?php else: ?>
              <select id="mascota" class="form-control">
                <option value="" disabled selected>
                  Selecciona una mascota
                </option>
                <?php
                $mascotas = obtenerMascotasUsuario($usuario, $mysqli);
                if ($mascotas && $mascotas->num_rows > 0):
                  while ($mascota = $mascotas->fetch_assoc()):
                    ?>
                    <option value="<?= htmlspecialchars($mascota['id_mascota']) ?>">
                      <?= htmlspecialchars($mascota['nombre']) ?>
                    </option>
                    <?php
                  endwhile;
                else:
                  ?>
                  <option disabled>No hay mascotas para este usuario</option>
                <?php endif; ?>
              </select>
            <?php endif; ?>
          </div>
          <div class="col-md-2 col-12 mb-3">
            <label class="form-label">Servicio</label>
            <select id="servicio" class="form-control">
              <option value="" disabled selected>
                Selecciona un servicio
              </option>
              <?php while ($fila = $servicios->fetch_assoc()): ?>
                <option value="<?php echo $fila['nombre']; ?>" data-precio="<?= htmlspecialchars($fila['precio']) ?>">
                  <?= htmlspecialchars($fila['nombre']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2 col-12 mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" id="fecha" class="form-control" />
          </div>
          <div class="col-md-2 col-12 mb-3">
            <label class="form-label">Hora</label>
            <input type="time" id="hora" class="form-control" />
          </div>
          <div class="col-md-2 col-12 mb-3">
            <label class="form-label">Precio</label>
            <input type="number" id="precio" class="form-control" placeholder="₡" value="<?php ?>" readonly />
          </div>
        </div>

        <div class="mb-4">
          <button class="btn btn-success" id="btnAgregar">
            <i class="fa-solid fa-plus me-1"></i>Agregar Cita
          </button>
          <button class="btn btn-secondary" id="btnLimpiar">
            <i class="fa-solid fa-eraser me-1"></i>Limpiar
          </button>
        </div>

        <div class="table-responsive mt-4">
          <table class="table table-striped table-hover align-middle" id="tablaCitas">
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Mascota</th>
                <th>Servicio</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Precio</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($fila = $citas->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($fila["cliente"]) ?></td>
                  <td><?php echo htmlspecialchars($fila["mascota"]) ?></td>
                  <td><?php echo htmlspecialchars($fila["servicio"]) ?></td>
                  <td><?php echo htmlspecialchars($fila["fecha"]) ?></td>
                  <td><?php echo htmlspecialchars($fila["hora"]) ?></td>
                  <td>₡<?php echo htmlspecialchars($fila["precio"]) ?></td>
                  <td class="text-center">
                    <button class="btn btn-warning btn-sm me-1"
                      onclick="editarCita(<?= htmlspecialchars($fila['id_cita']) ?>)">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm"
                      onclick="eliminarCita(<?= htmlspecialchars($fila['id_cita']) ?>)">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
              <?php cerrarConexion($mysqli); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalAdvertencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-warning">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>Advertencia
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p>
            ⚠️ Al cancelar la cita el mismo día, recuerde al usuario que se le
            cobrará el
            <strong>50% del valor</strong> de la cita.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cerrar
          </button>
          <button type="button" class="btn btn-danger" id="btnConfirmarCancelacion">
            Cancelar igualmente
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/gestion-citas.js"></script>
</body>

</html>