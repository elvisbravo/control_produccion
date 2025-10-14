document.addEventListener("DOMContentLoaded", function () {
    loadCargos();
});

const btnAdd = document.getElementById('btnAdd');
const formCargo = document.getElementById('formCargo');
const titleModalCargo = document.getElementById('titleModalCargo');
const idCargo = document.getElementById('idCargo');
const namePerfil = document.getElementById('namePerfil');
const listPermisos = document.getElementById("listPermisos");
const btnGuardarPermisos = document.getElementById('btnGuardarPermisos');
const formPermisos = document.getElementById('formPermisos');
const btnHtmlPermiso = document.getElementById('btnHtmlPermiso');

function loadCargos() {
    fetch('/permisos/cargos')
        .then(response => response.json())
        .then(data => {
            viewCargos(data);
        });
}

function viewCargos(data) {
    const loadCargos = document.getElementById('loadCargos');

    let html = '';

    data.forEach(cargo => {
        html += `
        <div class="d-flex align-items-center">
            <div>
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="cargo-${cargo.id}" value="${cargo.id}" onclick="selectCargo(${cargo.id}, '${cargo.nombre_perfil}')">
                <label class="form-check-label fs-6" for="cargo-${cargo.id}">${cargo.nombre_perfil}</label>
            </div>
            <div class="dropdown ms-auto">
                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-horizontal-rounded font-22 text-option"></i></a>
                <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:;" onclick="editCargo(${cargo.id}, '${cargo.nombre_perfil}')">Editar</a></li>
                <li><a class="dropdown-item" href="javascript:;" onclick="deleteCargo(${cargo.id})">Eliminar</a></li>
                </ul>
            </div>
        </div>
        
        `;
    });

    loadCargos.innerHTML = html;
}

btnAdd.addEventListener('click', function () {
    titleModalCargo.textContent = 'Agregar Cargo';
    const modalCargo = new bootstrap.Modal(document.getElementById('modalCargo'));
    modalCargo.show();

    idCargo.value = 0;
});

formCargo.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(formCargo);

    fetch('/permisos/cargos', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status == 'ok') {
                loadCargos();
                const modalCargo = bootstrap.Modal.getInstance(document.getElementById('modalCargo'));
                modalCargo.hide();
            } else {
                alert('error ' + data.message)
            }
        });
});

function editCargo(id, nombre) {

    titleModalCargo.textContent = 'Editar Cargo';
    const modalCargo = new bootstrap.Modal(document.getElementById('modalCargo'));
    modalCargo.show();

    idCargo.value = id;

    const nameCargo = document.getElementById('nameCargo');
    nameCargo.value = nombre;
}

function selectCargo(id, nombre) {
    namePerfil.textContent = nombre;

    btnHtmlPermiso.innerHTML = `<a href="javascript:;" class="btn btn-sm btn-outline-success" id="btnGuardarPermisos">Guardar</a>`;

    fetch('/permisos/cargos/' + id)
        .then(res => res.json())
        .then(data => {
            const perfil_id = document.getElementById('perfil_id');
            perfil_id.value = data.idperfil;

            viewPermisos(data)

        })
}

function viewPermisos(data) {
    let html = "";

    const modulos = data.modulos;

    modulos.forEach((modulo) => {
        let hijos = modulo.hijos;

        let htmlHijos = "";

        let cantidadHijos = hijos.length;
        let contPermisos = 0;

        hijos.forEach((hij) => {
            let acciones = hij.acciones;

            let htmlAcciones = "";

            if (acciones.length > 0) {
                htmlAcciones += `<ul class="list-inline mx-1">`;

                acciones.forEach((accion) => {
                    let checked = accion.permiso == 1 ? "checked" : "";

                    htmlAcciones += `
                        <li class="list-inline-item ms-4">
                        <input class="form-check-input" type="checkbox" id="accion${hij.id}${accion.accion_id}" name="permisosAcciones-${hij.id}[]" value="${accion.accion_id}" ${checked} />
                        <label class="form-check-label" for="accion${hij.id}${accion.accion_id}">${accion.nombre_accion}</label>
                        </li>
                    `;
                });

                htmlAcciones += `</ul>`;
            }

            let checked = hij.permiso == 1 ? "checked" : "";

            if (hij.permiso == 1) {
                contPermisos++;
            }

            htmlHijos += `
                <li class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="permisos[]" id="hijo-${hij.id}" value="${hij.id}" ${checked}>
                <label class="form-check-label" for="hijo-${hij.id}">${hij.nombre_modulo}</label>

                ${htmlAcciones}
                </li>
            `;
        });

        let checkPadre = "";

        if (contPermisos == cantidadHijos) {
            checkPadre = "checked";
        }

        html += `
        <li class="mb-3">
            <strong>→ <input class="form-check-input" type="checkbox" ${checkPadre} id="modulo-${modulo.id}"> ${modulo.nombre_modulo}</strong>
            <ul class="ms-2" id="modulo">
            ${htmlHijos}
            </ul>
        </li>
        `;
    });

    listPermisos.innerHTML = html;
}

btnGuardarPermisos.addEventListener('click', () => {
    const formData = new FormData(formPermisos);

    fetch('/permisos/guardar', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'ok') {
            alert(data.message)
        } else {
            alert(data.message)
        }
        
    })
})