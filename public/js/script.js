document.querySelectorAll(".toggle-submenu").forEach(function (item) {
    item.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(item.getAttribute("data-target"));
        target.classList.toggle("show");
    });
});
document.getElementById("logout-link").addEventListener("click", function (e) {
    e.preventDefault();
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¿Deseas cerrar sesión?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, cerrar sesión",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("logout-form").submit();
        }
    });
});
