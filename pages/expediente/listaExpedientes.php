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
        e.Nombre AS EspecieNombre
    FROM Mascota m
    INNER JOIN Cliente c ON m.Cliente_Id = c.Cliente_Id
    INNER JOIN MascotaEspecie e ON m.Especie_Id = e.Especie_Id
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
    $fecha = new DateTime($fila['Fecha']);
    return $fecha->format("d/m/Y");
}

cerrarConexion($mysqli);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Golden Paws</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    
    <!-- Google fonts CDN -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

    <link rel="stylesheet" href="../assets/css/style-layout.css">
    <link rel="stylesheet" href="../assets/css/style-expediente.css">
</head>

<body>

<nav class="navbar bg-white shadow-sm d-lg-none">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <img src="../assets/img/logo.png" alt="Golden Paws" style="height:36px">
        </a>
        <button class="btn btn-outline-secondary" type="button" 
            data-bs-toggle="offcanvas" data-bs-target="#MenuMovil">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="MenuMovil">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">
            <img src="../assets/img/logo.png" alt="Golden Paws">
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <nav class="sidebar-nav p-3 d-flex flex-column justify-content-between" style="height: 100%;">
            <ul class="nav flex-column gap-1">
                <li class="nav-item"><a class="nav-link" href="../pages/inicio.html"><i
                        class="fa-solid fa-house me-2"></i>Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="../pages/clientes.html"><i
                        class="fa-solid fa-user-group me-2"></i>Clientes</a></li>
                <li class="nav-item"><a class="nav-link active" href="../pages/listaExpedientes.php"><i
                        class="fa-solid fa-folder-open me-2"></i>Expedientes</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i
                        class="fa-solid fa-calendar-check me-2"></i>Citas</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i
                        class="fa-solid fa-chart-line me-2"></i>Reportes</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i
                        class="fa-regular fa-life-ring me-2"></i>Soporte</a></li>
            </ul>
            <div class="mt-auto pt-3 border-top">
                <a class="nav-link text-danger" href="../index.html"><i
                    class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
            </div>
        </nav>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

        <aside class="col-lg-3 col-xl-2 d-none d-lg-flex">
            <div class="sidebar shadow-sm">
                <div class="p-3 d-flex align-items-center gap-2 border-bottom">
                    <img src="../assets/img/logo.png" alt="Golden Paws" style="height:40px">
                </div>

                <nav class="sidebar-nav p-3 d-flex flex-column justify-content-between" style="height: 100%;">
                    <ul class="nav flex-column gap-1">
                        <li class="nav-item"><a class="nav-link" href="../pages/inicio.html"><i
                                class="fa-solid fa-house me-2"></i>Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="../pages/clientes.html"><i
                                class="fa-solid fa-user-group me-2"></i>Clientes</a></li>
                        <li class="nav-item"><a class="nav-link active" href="../pages/listaExpedientes.php"><i
                                class="fa-solid fa-folder-open me-2"></i>Expedientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><i
                                class="fa-solid fa-calendar-check me-2"></i>Citas</a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><i
                                class="fa-solid fa-chart-line me-2"></i>Reportes</a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><i
                                class="fa-regular fa-life-ring me-2"></i>Soporte</a></li>
                    </ul>

                    <div class="mt-auto pt-3 border-top">
                        <a class="nav-link text-danger" href="../index.html"><i
                            class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
                    </div>
                </nav>
            </div>
        </aside>

        <main class="col-lg-9 col-xl-10 px-4 py-4">

            <h2 class="text-center titulo-seccion mb-4">Expedientes de Mascotas</h2>

            <div class="search-box">
                <label for="buscar" class="form-label fw-semibold">Buscar mascota:</label>
                <input type="text" id="buscar" class="form-control" placeholder="Ejemplo: Luna, Max, Toby...">
            </div>

            <?php foreach ($mascotas as $m): 
                $ultima = obtenerUltimaVisita($mysqli, $m['Mascota_Id']);
            ?>
            <div class="card-expediente expediente" data-nombre="<?= strtolower($m['MascotaNombre']) ?>">
              <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                  <img src="../assets/img/perroEjemplo.jpg" class="foto-mascota mb-2">
                </div>

                <div class="col-md-7">
                  <h5 class="fw-semibold mb-1"><?= htmlspecialchars($m['MascotaNombre']) ?></h5>
                  <p class="text-muted mb-1"><?= htmlspecialchars($m['EspecieNombre']) ?> - <?= htmlspecialchars($m['Raza']) ?></p>
                  <p class="mb-0"><strong>Propietaria:</strong> <?= htmlspecialchars($m['DuenoNombre']) ?></p>
                  <p class="mb-0"><strong>Última visita:</strong> <?= $ultima ?></p>
                </div>

                <div class="col-md-2 text-center mt-3 mt-md-0">
                  <a href="expediente.php?mascota_id=<?= $m['Mascota_Id'] ?>" class="btn btn-expediente">
                    Ver expediente
                  </a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>

        </main>

    </div>
</div>

<script>
const buscar = document.getElementById("buscar");
buscar.addEventListener("input", () => {
    const texto = buscar.value.toLowerCase();
    document.querySelectorAll(".expediente").forEach(card => {
        card.style.display = card.dataset.nombre.includes(texto) ? "block" : "none";
    });
});
</script>

</body>
</html>
