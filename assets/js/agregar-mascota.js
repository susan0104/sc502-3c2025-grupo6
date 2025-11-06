document.getElementById("formMascota").addEventListener("submit", function (e) {
    e.preventDefault();

    const mascota = {
        nombre: document.getElementById("nombre").value,
        tipo: document.getElementById("tipo").value,
        raza: document.getElementById("raza").value,
        edad: document.getElementById("edad").value,
        observaciones: document.getElementById("observaciones").value
    };

    console.log("Mascota guardada:", mascota);
    alert("Mascota registrada correctamente ");

    // Aquí podrías guardar los datos en localStorage o en tu backend
    // localStorage.setItem("mascota", JSON.stringify(mascota));

    // Regresar a la creación de cliente
    window.location.href = "crear-cliente.html";
});
