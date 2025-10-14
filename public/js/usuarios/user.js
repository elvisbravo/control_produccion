$(document).ready(function () {
  $("#example").DataTable();
});

const btnAdd = document.getElementById("btnAdd");
const formUser = document.getElementById("formUsuario");
const titleModal = document.getElementById("titleModal");

btnAdd.addEventListener("click", function () {
  const myModal = new bootstrap.Modal(document.getElementById("modalUsuario"));
  myModal.show();

  titleModal.textContent = "Agregar Usuario";
  formUser.reset();
  document.getElementById("idUsuario").value = 0;
});

formUser.addEventListener("submit", function (e) {
  e.preventDefault();

  fetch("/usuarios/guardar", {
    method: "POST",
    body: new FormData(formUser),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        alert("Usuario guardado exitosamente");
        location.reload();
      } else {
        alert("Error al guardar el usuario: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al guardar el usuario: " + error.message);
    });
});

function usersAll() {
  fetch("/usuarios/all")
    .then((response) => response.json())
    .then((data) => {
      viewUsers(data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function viewUsers(data) {
  let html = "";
  data.forEach((user, index) => {
    html += `<tr>
            <td>${index + 1}</td>
            <td>${user.nombres} ${user.apellidos}</td>
            <td>${user.nombre_perfil}</td>
            <td>${user.correo}</td>
            <td>
                <button class="btn btn-warning btnEdit" onclick="editar(${
                  user.id
                })">Editar</button>
                <button class="btn btn-danger btnDelete" onclick="eliminar(${
                  user.id
                })">Eliminar</button>
            </td>
        </tr>`;
  });
  document.querySelector("#tbodyUsuarios").innerHTML = html;
}

usersAll();

function editar(id) {
  const myModal = new bootstrap.Modal(document.getElementById("modalUsuario"));
  myModal.show();

  titleModal.textContent = "Editar Usuario";

  fetch(`/usuarios/${id}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "error") {
        alert("Error al obtener los datos del usuario: " + data.message);
        return;
      }

      const user = data.data;

      document.getElementById("nombre").value = user.nombres;
      document.getElementById("apellidos").value = user.apellidos;
      document.getElementById("cargo").value = user.perfil_id;
      document.getElementById("correo").value = user.correo;
      document.getElementById("password").value = user.password;
      document.getElementById("idUsuario").value = id;
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al obtener los datos del usuario: " + error.message);
    });
}

function eliminar(id) {
  if (confirm("¿Está seguro de que desea eliminar este usuario?")) {
    fetch(`/usuarios/eliminar/${id}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert("Usuario eliminado exitosamente");
          location.reload();
        } else {
          alert("Error al eliminar el usuario: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error al eliminar el usuario: " + error.message);
      });
  }
}
