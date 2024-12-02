function mostrarFechaHora() {
    const ahora = new Date();
    const opcionesFecha = { year: 'numeric', month: 'long', day: 'numeric' };
    const opcionesHora = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
    const fecha = ahora.toLocaleDateString('es-ES', opcionesFecha);
    const hora = ahora.toLocaleTimeString('es-ES', opcionesHora);
    document.getElementById('fechaHora').innerHTML = `${fecha} ${hora}`;
}

window.onload = function() {
    mostrarFechaHora();
    setInterval(mostrarFechaHora, 1000); // Actualiza cada segundo

// Manejo del formulario de inicio de sesión
const loginForm = document.getElementById('loginForm');
loginForm.addEventListener('submit', function(event) {
    event.preventDefault(); // Evita el envío del formulario por defecto

    const usuario = loginForm[0].value; // Captura el valor del usuario
    const password = loginForm[1].value; // Captura el valor de la contraseña

    // Aquí puedes agregar la lógica para verificar las credenciales
    // Por ejemplo, compararlas con un conjunto de datos predefinido
    if (usuario === "usuarioEjemplo" && password === "contraseñaEjemplo") {
        alert("Inicio de sesión exitoso");
        // Redirigir a otra página o realizar otra acción
    } else {
        alert("Usuario o contraseña incorrectos");
    }
});
};