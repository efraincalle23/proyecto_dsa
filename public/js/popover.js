document.addEventListener("DOMContentLoaded", function () {
    // Seleccionamos todos los botones con la clase `popover-trigger`
    const popoverTriggers = document.querySelectorAll(".popover-trigger");

    // Iteramos sobre cada botón y aplicamos la funcionalidad
    popoverTriggers.forEach((button) => {
        // Inicializar el popover
        const popover = new bootstrap.Popover(button, {
            trigger: "manual", // Control manual del popover
            placement: "top",
        });

        // Mostrar el popover al pasar el mouse
        button.addEventListener("mouseenter", () => {
            popover.show();
        });

        // Ocultar el popover al quitar el mouse
        button.addEventListener("mouseleave", () => {
            popover.hide();
        });
    });
});
