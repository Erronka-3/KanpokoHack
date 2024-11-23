document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Evitar el comportamiento predeterminado del formulario

    // Obtener los valores del formulario
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Enviar los datos al backend
    fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password })  // Convertir los datos a JSON
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Credenciales incorrectas');
        }
        return response.json();  // Si la autenticación es correcta, procesamos la respuesta.
    })
    .then(data => {
        // Aquí podrías redirigir al usuario a su panel de control o página principal
        window.location.href = '/dashboard.html';  // O lo que corresponda
    })
    .catch(error => {
        // Mostrar el mensaje de error si ocurre un problema con el login
        document.getElementById('error-message').textContent = error.message;
        document.getElementById('error-message').style.display = 'block';
    });
});
