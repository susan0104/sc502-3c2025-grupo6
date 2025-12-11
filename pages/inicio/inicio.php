<?php
session_start();
$paginaActiva = 'inicio';
if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}
$paginaActiva = 'inicio';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Veterinaria Golden Paws</title>

  <link rel="icon" type="image/x-icon" href="../favicon.ico" />

  <!-- Google fonts -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" />

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

  <!-- Estilos -->
  <link rel="stylesheet" href="../../assets/css/style-inicio.css" />
</head>

<body>

  <div class="container-fluid">
    <div class="row">
      <?php include("../../layout/aside.php"); ?>
      <div class="col-12 col-lg-9 col-xl-10 py-4">

        <h1>Bienvenido(a)</h1>
        <h3><?= $_SESSION['nombre'] ?? 'Usuario' ?></h3>

        <div id="carouselPrincipal" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">

            <div class="carousel-item active">
              <img src="../../assets/img/carrusel-1.jpg" class="d-block w-100" alt="..." />
            </div>

            <div class="carousel-item">
              <img src="../../assets/img/carrusel-2.jpg" class="d-block w-100" alt="..." />
            </div>

            <div class="carousel-item">
              <img src="../../assets/img/carrusel-3.jpg" class="d-block w-100" alt="..." />
            </div>

          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#carouselPrincipal" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>

          <button class="carousel-control-next" type="button" data-bs-target="#carouselPrincipal" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>

        </div>
      </div>

    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>