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
