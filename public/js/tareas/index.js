const btnAdd = document.querySelector("#btnAdd");

btnAdd.addEventListener("click", function () {
  const myModal = new bootstrap.Modal(document.getElementById("modalTarea"));
  myModal.show();
});
