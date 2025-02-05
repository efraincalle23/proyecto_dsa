document.addEventListener("DOMContentLoaded", function () {
    const images = document.querySelectorAll(".background-image");
    let currentIndex = 0;

    function changeBackground() {
        images.forEach((image) => image.classList.remove("active"));
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].classList.add("active");
    }

    setInterval(changeBackground, 5000); // Cambia cada 5 segundos
});
