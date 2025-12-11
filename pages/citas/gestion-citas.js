let citaAEditar = null;
let citaPendienteEliminar = null;
const btnAgregar = document.getElementById("btnAgregar");
const modal = new bootstrap.Modal(document.getElementById("modalAdvertencia"));

document.getElementById("btnAgregar").addEventListener("click", agregarCita);
document
  .getElementById("btnLimpiar")
  .addEventListener("click", limpiarFormulario);
document
  .getElementById("btnConfirmarCancelacion")
  .addEventListener("click", confirmarEliminacion);

async function agregarCita() {
  const cliente = document.getElementById("cliente").value.trim();
  const mascota = document.getElementById("mascota").value.trim();
  const servicio = document.getElementById("servicio").value.trim();
  const fecha = document.getElementById("fecha").value;
  const hora = document.getElementById("hora").value;
  const precio = document.getElementById("precio").value;

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

  if (!cliente || !mascota || !servicio || !fecha || !hora || !precio) {
    Toast.fire({ icon: "warning", title: "Debe completar todos los campos" });
    return;
  }

  if (citaAEditar) {
    const formData = new FormData();
    formData.append("id_cita", citaAEditar.id);
    formData.append("cliente", cliente);
    formData.append("mascota", mascota);
    formData.append("servicio", servicio);
    formData.append("fecha", fecha);
    formData.append("hora", hora);
    formData.append("estado", citaAEditar.estado);
    formData.append("precio", precio);
    citaAEditar = null;

    try {
      const response = await fetch("../../assets/php/citas/actualizar_cita.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        Toast.fire({ icon: "error", title: "Error de conexión" });
        return;
      }

      const data = await response.json();

      if (data.length === 0) {
        Toast.fire({
          icon: "success",
          title: "Cita actualizada correctamente",
        });
        limpiarFormulario();
      } else {
        Swal.fire({
          icon: "error",
          title: "Error al actualizar cita",
          text: data.join("\n"),
        });
      }
    } catch (error) {
      Toast.fire({ icon: "error", title: "Error en la solicitud" });
    }

    btnAgregar.innerHTML = '<i class="fa-solid fa-plus me-1"></i>Agregar Cita';
  } else {
    const formData = new FormData();
    formData.append("cliente", cliente);
    formData.append("mascota", mascota);
    formData.append("servicio", servicio);
    formData.append("fecha", fecha);
    formData.append("hora", hora);
    formData.append("precio", precio);

    try {
      const response = await fetch("../../assets/php/citas/agregar_cita.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        Toast.fire({ icon: "error", title: "Error de conexión" });
        return;
      }

      const data = await response.json();

      if (data.length === 0) {
        Toast.fire({ icon: "success", title: "Cita agregada correctamente" });
        limpiarFormulario();
      } else {
        Swal.fire({
          icon: "error",
          title: "Error al agregar cita",
          text: data.join("\n"),
        });
      }
    } catch (error) {
      console.log(error);
      Toast.fire({ icon: "error", title: "Error en la solicitud" });
    }
  }
}

function limpiarFormulario() {
  window.location.href = "gestion-citas.php";
  document.getElementById("cliente").value = "";
  document.getElementById("mascota").value = "";
  document.getElementById("servicio").value = "";
  document.getElementById("fecha").value = "";
  document.getElementById("hora").value = "";
  document.getElementById("precio").value = "";
  citaAEditar = null;
  btnAgregar.innerHTML = '<i class="fa-solid fa-plus me-1"></i>Agregar Cita';
}

async function editarCita(id) {
  btnAgregar.innerHTML = '<i class="fa-solid fa-pencil me-1"></i>Editar Cita';

  try {
    const formData = new FormData();
    formData.append("id_cita", id);

    const response = await fetch("../../assets/php/citas/obtener_cita.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      Swal.fire({ icon: "error", title: "Error de conexión" });
      return;
    }

    const data = await response.json();

    if (data.errors) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: data.errors.join("\n"),
      });
      return;
    }

    const cita = data.cita;

    document.getElementById("cliente").value = cita.cliente;
    document.getElementById("cliente").disabled = true;
    document.getElementById("mascota").value = cita.mascota;
    document.getElementById("servicio").value = cita.servicio;
    document.getElementById("fecha").value = cita.fecha;
    document.getElementById("hora").value = cita.hora;
    document.getElementById("precio").value = cita.precio;

    citaAEditar = { id: id, ...cita };
  } catch (error) {
    console.log(error);
    Swal.fire({ icon: "error", title: "Error en la solicitud" });
  }
}

async function eliminarCita(id, fecha) {
  const hoy = new Date().toISOString().split("T")[0];

  if (fecha === hoy) {
    citaPendienteEliminar = id;
    modal.show();
    return;
  }

  try {
    const formData = new FormData();
    formData.append("id_cita", id);

    const response = await fetch("../../assets/php/citas/eliminar_cita.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      Swal.fire({ icon: "error", title: "Error de conexión" });
      return;
    }

    const data = await response.json();

    if (data.length === 0) {
      Swal.fire({ icon: "success", title: "Cita eliminada correctamente" });
      limpiarFormulario();
    } else {
      Swal.fire({
        icon: "error",
        title: "Error al eliminar cita",
        text: data.join("\n"),
      });
    }
  } catch (error) {
    Swal.fire({ icon: "error", title: "Error en la solicitud" });
  }
}

async function confirmarEliminacion() {
  if (citaPendienteEliminar !== null) {
    const id = citaPendienteEliminar;
    citaPendienteEliminar = null;
    modal.hide();

    try {
      const formData = new FormData();
      formData.append("id_cita", id);

      const response = await fetch("../../assets/php/citas/eliminar_cita.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        Swal.fire({ icon: "error", title: "Error de conexión" });
        return;
      }

      const data = await response.json();

      if (data.length === 0) {
        Swal.fire({ icon: "success", title: "Cita eliminada correctamente" });
        limpiarFormulario();
      } else {
        Swal.fire({
          icon: "error",
          title: "Error al eliminar cita",
          text: data.join("\n"),
        });
      }
    } catch (error) {
      Swal.fire({ icon: "error", title: "Error en la solicitud" });
    }
  }
}

const selectServicio = document.getElementById("servicio");
const inputPrecio = document.getElementById("precio");

selectServicio.addEventListener("change", function () {
  const precio = this.options[this.selectedIndex].getAttribute("data-precio");
  inputPrecio.value = precio;
});
