let citas = [];
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
    Toast.fire({
      icon: "warning",
      title: "Debe completar todos los campos",
    });
    return;
  }

  if (citaAEditar) {
    citaAEditar.cliente = cliente;
    citaAEditar.mascota = mascota;
    citaAEditar.servicio = servicio;
    citaAEditar.fecha = fecha;
    citaAEditar.hora = hora;
    citaAEditar.precio = precio;
    citaAEditar = null;
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
      const response = await fetch("../assets/php/citas/agregar_cita.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        Toast.fire({
          icon: "error",
          title: "Error de conexión",
        });
        return;
      }

      const data = await response.json();

      if (data.length === 0) {
        Toast.fire({
          icon: "success",
          title: "Cita agregada correctamente",
        });
        limpiarFormulario();
      } else {
        const mensajeError = data.join("\n");
        Swal.fire({
          icon: "error",
          title: "Error al agregar cita",
          text: mensajeError,
        });
      }
    } catch (error) {
      console.log("Error:", error);
      Toast.fire({
        icon: "error",
        title: "Error en la solicitud",
      });
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
}

function editarCita(index) {
  btnAgregar.innerHTML = "Editar Cita";
  const cita = citas[index];
  //Todo: editar la cita en la base de datos
  document.getElementById("cliente").value = cita.cliente;
  document.getElementById("mascota").value = cita.mascota;
  document.getElementById("servicio").value = cita.servicio;
  document.getElementById("fecha").value = cita.fecha;
  document.getElementById("hora").value = cita.hora;
  document.getElementById("precio").value = cita.precio;
  citaAEditar = cita;
}

function eliminarCita(index) {
  const cita = citas[index];
  const hoy = new Date().toISOString().split("T")[0];

  if (cita.fecha == hoy) {
    citaPendienteEliminar = index;
    modal.show();
  } else {
    //Todo: eliminar la cita de la base de datos
    citas.splice(index, 1);
    renderizarCitas();
  }
}

function confirmarEliminacion() {
  if (citaPendienteEliminar !== null) {
    //Todo: eliminar la cita de la base de datos
    citas.splice(citaPendienteEliminar, 1);
    renderizarCitas();
    citaPendienteEliminar = null;
    modal.hide();
  }
}

const selectServicio = document.getElementById("servicio");
const inputPrecio = document.getElementById("precio");

selectServicio.addEventListener("change", function () {
  const precio = this.options[this.selectedIndex].getAttribute("data-precio");
  inputPrecio.value = precio;
});
