document.getElementById("loginForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const usuario = document.getElementById("usuario").value.trim();
  const contrasenia = document.getElementById("contrasenia").value.trim();

  if (!usuario || !contrasenia) {
    Swal.fire({
      icon: "error",
      title: "Datos faltantes",
      text: "Debe ingresar usuario y contraseña",
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 4000,
    });
    return;
  }

  try {
   const respuesta = await fetch("assets/php/login/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ usuario, contrasenia })
    });

    const data = await respuesta.json();

    if (data.status === "ok") {
      Swal.fire({
        icon: "success",
        title: "Bienvenido",
        text: data.mensaje,
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
      });

      setTimeout(() => {
        window.location.href = "./pages/inicio/inicio.php";
      }, 2000);

    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: data.mensaje,
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
      });
    }

  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Servidor caído",
      text: "No se pudo conectar: " + error,
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 4000,
    });
  }
});
