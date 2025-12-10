document
  .getElementById("formRegistro")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const contrasenia = document.getElementById("contrasenia").value.trim();
    const cargo = document.getElementById("cargo").value.trim();

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

    if (!nombre || !correo || !contrasenia || !cargo) {
      Toast.fire({
        icon: "warning",
        title: "Debe completar todos los campos",
      });
    }

    const datos = new FormData();
    datos.append("nombre", nombre);
    datos.append("correo", correo);
    datos.append("contrasenia", contrasenia);
    datos.append("cargo", cargo);

    try {
      const response = await fetch("../assets/php/registro/registro.php", {
        method: "POST",
        body: datos,
      });

      const result = await response.text();

      if (result.includes("ok")) {
        Toast.fire({
          icon: "success",
          title: "Nueva cuenta creada correctamente",
        });

        setTimeout(() => {
          window.location.href = "./inicio.html";
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
