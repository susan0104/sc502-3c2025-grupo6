<nav class="sidebar-nav p-3 d-flex flex-column justify-content-between" style="height: 100%">
    <ul class="nav flex-column gap-1">

        <li class="nav-item">
            <a class="nav-link <?= $paginaActiva === 'inicio' ? 'active' : '' ?>" href="../inicio/inicio.php">
                <i class="fa-solid fa-house me-2"></i>Inicio
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $paginaActiva === 'clientes' ? 'active' : '' ?>" href="../cliente/clientes.php">
                <i class="fa-solid fa-user-group me-2"></i>Clientes
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $paginaActiva === 'expedientes' ? 'active' : '' ?>" href="../expediente/listaExpedientes.php">
                <i class="fa-solid fa-folder-open me-2"></i>Expedientes
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $paginaActiva === 'citas' ? 'active' : '' ?>" href="../gestion-citas.php">
                <i class="fa-solid fa-calendar-check me-2"></i>Citas
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $paginaActiva === 'reportes' ? 'active' : '' ?>" href="#">
                <i class="fa-solid fa-chart-line me-2"></i>Reportes
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $paginaActiva === 'soporte' ? 'active' : '' ?>" href="#">
                <i class="fa-regular fa-life-ring me-2"></i>Soporte
            </a>
        </li>

    </ul>

    <div class="mt-auto pt-3 border-top">
        <a class="nav-link text-danger" href="../../assets/php/login/logout.php">
            <i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión
        </a>
    </div>
</nav>