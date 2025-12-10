<?php
    $paginaActiva = $paginaActiva ?? '';
?>

<link rel="stylesheet" href="../../assets/css/style-layout.css" />
<nav class="navbar bg-white shadow-sm d-lg-none">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <img src="../../assets/img/logo.png" alt="Golden Paws" style="height: 36px" />
        </a>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#MenuMovil">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="MenuMovil">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title d-flex align-items-center gap-2">
            <img src="../../assets/img/logo.png" alt="Golden Paws" />
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0">
        <?php include("aside-links.php"); ?>
    </div>
</div>
<aside class="col-lg-3 col-xl-2 d-none d-lg-flex">
    <div class="sidebar shadow-sm">
        <div class="p-3 d-flex align-items-center gap-2 border-bottom">
            <img src="../../assets/img/logo.png" alt="Golden Paws" style="height: 40px" />
        </div>

        <?php include("aside-links.php"); ?>

    </div>
</aside>