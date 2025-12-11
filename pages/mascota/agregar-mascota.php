<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}
if (!isset($_GET['cliente_id']) || !is_numeric($_GET['cliente_id'])) {
    header("Location: clientes.php");
    exit();
}

$cliente_id = intval($_GET['cliente_id']);

$paginaActiva = 'clientes';

include("../../assets/php/conexionBD.php");
$mysqli = abrirConexion();

$especies = [];
$sql = "SELECT Especie_Id, Nombre FROM MascotaEspecie ORDER BY Especie_Id ASC";
$resultado = $mysqli->query($sql);

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $especies[] = $fila;
    }
}

cerrarConexion($mysqli);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Veterinaria Golden Paws</title>

    <link rel="icon" type="image/x-icon" href="../../favicon.ico" />

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
                    <h1 class="m-0">Creación de Mascota</h1>

                    <button type="button" class="btn btn-secondary"
                        onclick="window.location.href = '../cliente/clientes.php'">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>
                    </button>
                </div>
                <form id="formMascota">
                    <input type="hidden" id="cliente_id" name="cliente_id" value="<?= $cliente_id ?>">

                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Nombre de la mascota</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Especie</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="" disabled selected>Seleccione una especie</option>
                                <?php foreach ($especies as $especie): ?>
                                    <option value="<?= $especie['Especie_Id'] ?>"><?= $especie['Nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Raza</label>
                            <input type="text" class="form-control" id="raza" name="raza">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Edad</label>
                            <input type="number" class="form-control" id="edad" name="edad" min="0">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Foto de la mascota</label>
                            <input type="file" class="form-control" id="foto" accept="image/*">
                        </div>

                        <input type="hidden" id="fotoBase64" name="fotoBase64">

                        <div class="col-12 mb-4">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                        </div>

                        <div class="col-md-6 d-flex align-items-end mb-4">
                            <button type="submit" class="btn btn-primary w-100">
                                Guardar Mascota
                            </button>
                        </div>

                        <div class="col-md-6 d-flex align-items-end mb-4">
                            <button type="button" class="btn btn-secondary w-100"
                                onclick="window.location.href='../cliente/editar-cliente.php?id=<?= $cliente_id ?>'">
                                Volver
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="agregar-mascota.js"></script>
</body>

</html>