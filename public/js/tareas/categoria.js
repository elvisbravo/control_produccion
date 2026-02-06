$(document).ready(function () {
  $("#example").DataTable();
});

function allCategorias() {
  fetch("/categorias-tareas/all")
    .then((res) => res.json())
    .then((data) => {
      viewCategorias(data);
    });
}

allCategorias();

function viewCategorias(data) {
  let tbody = document.getElementById("tbodyCategorias");

  let html = "";

  data.forEach((cat, index) => {
    html += `<tr>
            <td>${index + 1}</td>
            <td>${cat.nombre_categoria}</td>
            <td>
                <button class="btn btn-sm btn-primary">Editar</button>
                <button class="btn btn-sm btn-danger">Eliminar</button>
            </td>
        </tr>`;
  });

  tbody.innerHTML = html;
}
