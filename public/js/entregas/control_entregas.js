$(document).ready(function () {
    $('#example').DataTable();
});

const btnAdd = document.getElementById('btnAdd');
const formEntrega = document.getElementById('formEntrega');

btnAdd.addEventListener('click', function () {
    const myModal = new bootstrap.Modal(document.getElementById('modalEntrega'));
    myModal.show();
});

formEntrega.addEventListener('submit', function (e) {
    e.preventDefault();

    fetch('/entregas/guardar', {
        method: 'POST',
        body: new FormData(formEntrega)
    }).then(response => response.json())
        .then(data => {
            alert('Error al guardar la entrega');
            /*if (data.success) {
                alert('Entrega guardada con éxito');
                location.reload();
            } else {
                alert('Error al guardar la entrega');
            }*/
        }).catch(error => {
            console.error('Error:', error);
            alert('Error al guardar la entrega');
        });
});