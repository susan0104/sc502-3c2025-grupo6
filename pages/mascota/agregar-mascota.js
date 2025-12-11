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
