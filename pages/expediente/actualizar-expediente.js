document.getElementById("formEditarExpediente")
.addEventListener("submit", async function (e) {
    e.preventDefault();

    const mascota_id  = document.getElementById("mascota_id").value.trim();
    const obs         = document.getElementById("obs").value.trim();
    const alergias    = document.getElementById("alergias").value.trim();
    const tratamientos= document.getElementById("tratamientos").value.trim();

    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: "#fff",
        color: "#000",
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        }
    });

    if (!mascota_id) {
        Toast.fire({
            icon: "warning",
            title: "Error interno: mascota no identificada"
        });
        return;
    }

    const datos = new FormData();
    datos.append("mascota_id", mascota_id);
    datos.append("obs", obs);
    datos.append("alergias", alergias);
    datos.append("tratamientos", tratamientos);

    try {
        const response = await fetch("./actualizarExpediente-service.php", {
            method: "POST",
            body: datos
        });

        const result = await response.text();

        if (result.includes("ok")) {
            Toast.fire({
                icon: "success",
                title: "Expediente actualizado correctamente"
            });

            setTimeout(() => {
                window.location.href = "listaexpedientes.php";
            }, 1500);

        } else {
            Toast.fire({
                icon: "error",
                title: "Error al guardar: " + result
            });
        }

    } catch (err) {
        console.error(err);
        Toast.fire({
            icon: "error",
            title: "Error de conexión con el servidor"
        });
    }
});