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

function agregarCita() {
  const cliente = document.getElementById("cliente").value.trim();
  const mascota = document.getElementById("mascota").value.trim();
  const servicio = document.getElementById("servicio").value.trim();
  const fecha = document.getElementById("fecha").value;
  const hora = document.getElementById("hora").value;
  const precio = document.getElementById("precio").value;

  if (!cliente || !mascota || !servicio || !fecha || !hora || !precio) {
    alert("Por favor complete todos los campos.");
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
    citas.push({ cliente, mascota, servicio, fecha, hora, precio });
  }

  limpiarFormulario();
  renderizarCitas();
}

function limpiarFormulario() {
  document.getElementById("cliente").value = "";
  document.getElementById("mascota").value = "";
  document.getElementById("servicio").value = "";
  document.getElementById("fecha").value = "";
  document.getElementById("hora").value = "";
  document.getElementById("precio").value = "";
  citaAEditar = null;
}

function renderizarCitas() {
  const tbody = document.querySelector("#tablaCitas tbody");
  tbody.innerHTML = "";

  citas.forEach((cita, index) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${cita.cliente}</td>
      <td>${cita.mascota}</td>
      <td>${cita.servicio}</td>
      <td>${cita.fecha}</td>
      <td>${cita.hora}</td>
      <td>₡${cita.precio}</td>
      <td class="text-center">
        <button class="btn btn-warning btn-sm me-1" onclick="editarCita(${index})">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button class="btn btn-danger btn-sm" onclick="eliminarCita(${index})">
          <i class="fa-solid fa-trash"></i>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function editarCita(index) {
  btnAgregar.innerHTML = 'Editar Cita';
  const cita = citas[index];
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
    citas.splice(index, 1);
    renderizarCitas();
  }
}

function confirmarEliminacion() {
  if (citaPendienteEliminar !== null) {
    citas.splice(citaPendienteEliminar, 1);
    renderizarCitas();
    citaPendienteEliminar = null;
    modal.hide();
  }
}

  const preciosServicios = {
    "Corte de pelo": 10000,
    "Baño completo": 8000,
    "Corte y baño": 16000,
    "Limpieza dental": 12000,
    "Desparasitación": 6000
  };

  const servicioSelect = document.getElementById("servicio");
  const precioInput = document.getElementById("precio");

  servicioSelect.addEventListener("change", function () {
    const servicioSeleccionado = this.value;
    const precio = preciosServicios[servicioSeleccionado] || "";
    precioInput.value = precio;
  });
