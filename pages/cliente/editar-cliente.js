 document
  .getElementById("formEditarCliente")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const cliente_id = document.getElementById("cliente_id").value.trim();
    const nombre = document.getElementById("nombre").value.trim();
    const identificacion = document.getElementById("identificacion").value.trim();
    const fecha = document.getElementById("fecha").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const plan = document.getElementById("plan").value.trim();

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
      },
    });

    if (!nombre || !identificacion || !fecha || !correo || !plan) {
        Toast.fire({
            icon: "warning",
            title: "Debe completar todos los campos",
        });
        return;
    }


    const datos = new FormData();
    datos.append("cliente_id", cliente_id);
    datos.append("nombre", nombre);
    datos.append("identificacion", identificacion);
    datos.append("fecha", fecha);
    datos.append("correo", correo);
    datos.append("plan", plan);

    try {
      const response = await fetch("editarcliente-service.php", {
        method: "POST",
        body: datos,
      });

      const result = await response.text();

      if (result.includes("ok")) {
        Toast.fire({
          icon: "success",
          title: "Cliente editado correctamente",
        });

        setTimeout(() => {
          window.location.href = "clientes.php";
        }, 2000);
      } else if (result.includes("error:")) {
        Toast.fire({
          icon: "error",
          title: result.replace("error:", "").trim(),
        });
      } else {
        Toast.fire({
          icon: "error",
          title: "Ocurrio un error inesperado al registrar al usuario" + result,
        });
      }
    } catch (error) {
      console.log(error);
      Toast.fire({
        icon: "error",
        title: "Error de conexion con el servidor. " + error,
      });
    }
  });
