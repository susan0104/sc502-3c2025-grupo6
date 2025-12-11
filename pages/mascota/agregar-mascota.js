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

document.getElementById("formMascota").addEventListener("submit", async function (e) {
  e.preventDefault();

  const cliente_id = document.getElementById("cliente_id").value.trim();
  const nombre = document.getElementById("nombre").value.trim();
  const tipo = document.getElementById("tipo").value.trim();
  const raza = document.getElementById("raza").value.trim();
  const edad = document.getElementById("edad").value.trim();
  const observaciones = document.getElementById("observaciones").value.trim();
  const foto = document.getElementById("fotoBase64").value.trim();



  if (!nombre || !tipo || !raza || edad === "" || !foto) {
    Toast.fire({
      icon: "warning",
      title: "Debe completar todos los campos y subir una foto",
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
  datos.append("cliente_id", cliente_id);
  datos.append("nombre", nombre);
  datos.append("tipo", tipo);
  datos.append("raza", raza);
  datos.append("edad", edad);
  datos.append("observaciones", observaciones);
  datos.append("fotoBase64", foto);

  const response = await fetch("agregarmascota-service.php", {
    method: "POST",
    body: datos,
  });

  const result = await response.text();
  console.log("Respuesta del servidor:", result);

  if (result.includes("ok")) {
    Swal.fire("Éxito", "Mascota registrada correctamente", "success");
    setTimeout(() => {
      window.location.href = "../cliente/editar-cliente.php?id=" + cliente_id;
    }, 1200);

  } else {
    Swal.fire("Error", result, "error");
  }
});
