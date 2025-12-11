<?php
session_start();
if (!isset($_SESSION['id'])) {
  header("Location: ../../index.php");
  exit();
}
$paginaActiva = 'clientes';

include("../../assets/php/conexionBD.php");

$clientes = [];
$conexion = abrirConexion();

$sql = "
  SELECT 
    c.Cliente_Id,
    c.Identificacion,
    c.Nombre,
    p.Nombre Plan
  FROM Cliente c
  INNER JOIN ClientePlan p ON p.Plan_Id = c.Plan_Id
  ORDER BY c.Nombre ASC
";

$resultado = $conexion->query($sql);

if ($resultado) {
  while ($fila = $resultado->fetch_assoc()) {
    $clientes[] = $fila;
  }
}

cerrarConexion($conexion);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Veterinaria Golden Paws</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico" />
  <!-- Google fonts CDN -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" />
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/style-layout.css" />
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <?php include("../../layout/aside.php"); ?>

      <div class="col-12 col-lg-9 col-xl-10 py-4">
        <h1 class="mb-4 border-bottom">Gestión de Clientes</h1>
        <div class="row">
          <div class="col-md-5 col-12 mb-4">
            <label for="nombreCliente" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombreCliente" placeholder="Nombre del Cliente" />
          </div>
          <div class="col-md-5 col-12 mb-4">
            <label for="identificacionCliente" class="form-label">Identificación</label>
            <input type="text" class="form-control" id="identificacionCliente"
              placeholder="Identificación del Cliente" />
          </div>
          <div class="col-md-2 col-12 d-flex align-items-end mb-4">
            <button type="button" class="btn btn-primary w-100 me-2" id="btnLimpiar">
              <i class="fa-solid fa-eraser"></i>
            </button>

            <button type="button" class="btn btn-success w-100" onclick="window.location.href = 'crear-cliente.php'">
              <i class="fa-solid fa-plus"></i>
            </button>
          </div>
        </div>

        <div class="table-responsive mt-4">
          <table class="table table-striped table-hover align-middle">
            <thead class="">
              <tr>
                <th scope="col" class="color-principal">Identificación</th>
                <th scope="col" class="color-principal">Nombre</th>
                <th scope="col" class="color-principal">Plan</th>
                <th scope="col" class="text-center color-principal">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($clientes)): ?>
                <?php foreach ($clientes as $cliente): ?>
                  <tr class="fila-cliente" data-nombre="<?= strtolower($cliente['Nombre']) ?>"
                    data-id="<?= strtolower($cliente['Identificacion']) ?>">
                    <td><?= htmlspecialchars($cliente['Identificacion']) ?></td>
                    <td><?= htmlspecialchars($cliente['Nombre']) ?></td>
                    <td><?= htmlspecialchars($cliente['Plan']) ?></td>
                    <td class="text-center">
                      <button class="btn btn-warning me-1"
                        onclick="window.location.href='./editar-cliente.php?id=<?= $cliente['Cliente_Id'] ?>'">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <button class="btn btn-danger" onclick="eliminar(<?= $cliente['Cliente_Id'] ?>)">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted">
                    No hay clientes registrados
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function eliminar(id) {
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará al cliente permanentemente",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "eliminarcliente-service.php?id=" + id;
        }
      });
    }
    document.addEventListener("DOMContentLoaded", () => {

      const inputNombre = document.getElementById("nombreCliente");
      const inputId = document.getElementById("identificacionCliente");
      const filas = document.querySelectorAll(".fila-cliente");

      function filtrar() {
        const filtroNombre = inputNombre.value.toLowerCase();
        const filtroId = inputId.value.toLowerCase();

        filas.forEach(fila => {
          const nombre = fila.dataset.nombre;
          const identificacion = fila.dataset.id;

          const coincideNombre = nombre.includes(filtroNombre);
          const coincideId = identificacion.includes(filtroId);

          fila.style.display = (coincideNombre && coincideId) ? "" : "none";
        });
      }

      inputNombre.addEventListener("input", filtrar);
      inputId.addEventListener("input", filtrar);

      btnLimpiar.addEventListener("click", () => {
        inputNombre.value = "";
        inputId.value = "";

        filas.forEach(fila => fila.style.display = "");

      });

    });
  </script>
</body>

</html>