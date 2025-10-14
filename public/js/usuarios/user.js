$(document).ready(function () {
    $('#example').DataTable();
});

const btnAdd = document.getElementById('btnAdd');
const formUser = document.getElementById('formUsuario');

btnAdd.addEventListener('click', function () {
    const myModal = new bootstrap.Modal(document.getElementById('modalUsuario'));
    myModal.show();
});

formUser.addEventListener('submit', function (e) {
    e.preventDefault();

    fetch('/usuarios/guardar', {
        method: 'POST',
        body: new FormData(formUser)
    }).then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Usuario guardado exitosamente');
                location.reload();
            } else {
                alert('Error al guardar el usuario: ' + data.message);
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Error al guardar el usuario: ' + error.message);
        });
});

function usersAll() {
    fetch('/usuarios/all')
        .then(response => response.json())
        .then(data => {
            viewUsers(data);
        }).catch(error => {
            console.error('Error:', error);
        });
}

function viewUsers(data) {
    let html = '';
    data.forEach((user, index) => {
        html += `<tr>
            <td>${index + 1}</td>
            <td>${user.nombres} ${user.apellidos}</td>
            <td>${user.nombre_perfil}</td>
            <td>${user.correo}</td>
            <td>
                <button class="btn btn-warning btnEdit" data-id="${user.id}">Editar</button>
                <button class="btn btn-danger btnDelete" data-id="${user.id}">Eliminar</button>
            </td>
        </tr>`;
    });
    document.querySelector('#tbodyUsuarios').innerHTML = html;
}

usersAll();