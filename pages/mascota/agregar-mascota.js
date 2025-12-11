document
  .getElementById("formMascota")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const cliente_id   = document.getElementById("cliente_id").value.trim();
    const nombre       = document.getElementById("nombre").value.trim();
    const tipo         = document.getElementById("tipo").value.trim();
    const raza         = document.getElementById("raza").value.trim();
    const edad         = document.getElementById("edad").value.trim();
    const observaciones= document.getElementById("observaciones").value.trim();

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

    if (!cliente_id || !nombre || !tipo || !raza || !edad) {
      Toast.fire({
        icon: "warning",
        title: "Debe completar el nombre, el tipo, la raza y la edad",
      });
      return;
    }

    const datos = new FormData();
    datos.append("cliente_id", cliente_id);
    datos.append("nombre", nombre);
    datos.append("tipo", tipo);
    datos.append("raza", raza);
    datos.append("edad", edad);
    datos.append("observaciones", observaciones);

    try {
      const response = await fetch("agregarmascota-service.php", {
        method: "POST",
        body: datos,
      });

      const result = await response.text();

      if (result.includes("ok")) {
        Toast.fire({
          icon: "success",
          title: "Mascota registrada correctamente",
        });

        setTimeout(() => {
          window.location.href = "../cliente/editar-cliente.php?id=" + cliente_id;
        }, 1500);

      } else if (result.includes("error:")) {
        Toast.fire({
          icon: "error",
          title: result.replace("error:", "").trim(),
        });
      } else {
        Toast.fire({
          icon: "error",
          title: "Error inesperado: " + result,
        });
      }

    } catch (error) {
      console.log(error);
      Toast.fire({
        icon: "error",
        title: "Error de conexión con el servidor",
      });
    }
  });
