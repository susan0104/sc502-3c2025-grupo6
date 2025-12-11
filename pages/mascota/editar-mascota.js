document.getElementById("foto").addEventListener("change", function () {
  const file = this.files[0];
  if (!file) {
    document.getElementById("fotoBase64").value = "";
    return;
  }

  const reader = new FileReader();

  reader.onloadend = function () {
    document.getElementById("fotoBase64").value = reader.result;
  };

  reader.readAsDataURL(file);
});

document
  .getElementById("formEditarMascota")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const mascota_id = document.getElementById("mascota_id").value.trim();
    const nombre = document.getElementById("nombre").value.trim();
    const tipo = document.getElementById("tipo").value.trim();
    const raza = document.getElementById("raza").value.trim();
    const edad = document.getElementById("edad").value.trim();
    const observaciones = document.getElementById("observaciones").value.trim();
    const foto = document.getElementById("fotoBase64").value.trim();

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

    if (!mascota_id || !nombre || !tipo || !raza || !edad) {
      Toast.fire({
        icon: "warning",
        title: "Debe completar nombre, especie, raza, edad",
      });
      return;
    }

    if (nombre.length < 3 || nombre.length > 150) {
      Toast.fire({
        icon: "warning",
        title: "El nombre debe tener entre 3 y 150 caracteres",
      });
      return;
    }


    if (raza.length > 100) {
      Toast.fire({
        icon: "warning",
        title: "La raza no puede exceder 100 caracteres",
      });
      return;
    }


    const edadNum = parseInt(edad);

    if (isNaN(edadNum) || edadNum < 0 || edadNum > 30) {
      Toast.fire({
        icon: "warning",
        title: "La edad debe estar entre 0 y 30 años",
      });
      return;
    }


    if (observaciones.length > 200) {
      Toast.fire({
        icon: "warning",
        title: "Las observaciones no pueden exceder 200 caracteres",
      });
      return;
    }

    if (!foto || foto.length < 50) {
      Toast.fire({
        icon: "warning",
        title: "Debe subir una fotografía de la mascota",
      });
      return;
    }


    const datos = new FormData();
    datos.append("mascota_id", mascota_id);
    datos.append("nombre", nombre);
    datos.append("tipo", tipo);
    datos.append("raza", raza);
    datos.append("edad", edad);
    datos.append("observaciones", observaciones);
    datos.append("fotoBase64", foto);

    try {
      const response = await fetch("editarmascota-service.php", {
        method: "POST",
        body: datos,
      });

      const result = await response.text();

      if (result.includes("ok")) {
        Toast.fire({
          icon: "success",
          title: "Mascota actualizada correctamente",
        });

        setTimeout(() => {
          window.location.href = "../cliente/editar-cliente.php?id=" + result.replace("ok:", "");
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
      Toast.fire({
        icon: "error",
        title: "Error de conexión con el servidor",
      });
    }
  });
