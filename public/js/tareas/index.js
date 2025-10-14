const btnAdd = document.querySelector("#btnAdd");
const formTarea = document.querySelector("#formTarea");

btnAdd.addEventListener("click", function () {
  const myModal = new bootstrap.Modal(document.getElementById("modalTarea"));
  myModal.show();
});

formTarea.addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(formTarea);

  fetch("/tareas/guardar", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        alert("Tarea guardada exitosamente");
        location.reload();
      } else {
        alert("Error al guardar la tarea");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al guardar la tarea");
    });
});

function loadTareas() {
  fetch("/tareas-all", {
    method: "POST",
  })
    .then((response) => response.json())
    .then((data) => {
      viewTareas(data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

loadTareas();

function viewTareas(data) {
  const tareas = document.querySelector("#tareas");
  tareas.innerHTML = "";

  let html = "";

  data.forEach((tarea) => {
    const tareasAll = tarea.tareas;

    let htmlTareas = "";

    tareasAll.forEach((task) => {
      htmlTareas += `
        <tr>
            <td>${task.nombre_tarea}</td>
            <td>${task.horas_estimadas}</td>
            <td>
                <button class="btn btn-sm btn-primary">Editar</button>
                <button class="btn btn-sm btn-danger">Eliminar</button>
            </td>
        </tr>
        `;
    });

    html += `
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center px-3 py-2">
                    <div>
                        <h6 class="mb-0">${tarea.nombre_categoria} - ${tarea.total_horas}</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Tarea</th>
                                <th>Horas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${htmlTareas}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    `;
  });

  tareas.innerHTML = html;
}
