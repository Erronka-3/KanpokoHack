  // Para que se cargue en el menu 'active' cada apartado
  document.addEventListener("DOMContentLoaded", function(event) {
    // Función para manejar la visibilidad del menú lateral
    const showNavbar = (toggleId, navId, bodyId, headerId) => {
        const toggle = document.getElementById(toggleId),
            nav = document.getElementById(navId),
            bodypd = document.getElementById(bodyId),
            headerpd = document.getElementById(headerId);

        if (toggle && nav && bodypd && headerpd) {
            toggle.addEventListener('click', () => {
                nav.classList.toggle('show');
                toggle.classList.toggle('bx-x');
                bodypd.classList.toggle('body-pd');
                headerpd.classList.toggle('body-pd');
            });
        }
    };

    showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

    // Obtener el parámetro 'route' de la URL actual
    const urlParams = new URLSearchParams(window.location.search);
    const currentRoute = urlParams.get('route'); // Obtener el valor de 'route'

    const linkColor = document.querySelectorAll('.nav_link');

    // Función para aplicar la clase 'active' al enlace correspondiente
    function colorLink() {
        linkColor.forEach(l => l.classList.remove('active')); // Remover clase 'active' de todos los enlaces

        // Obtener el valor del parámetro 'route' del enlace
        const linkRoute = new URLSearchParams(l.getAttribute("href").split('?')[1]).get('route');

        // Añadir la clase 'active' si coincide el valor de 'route'
        if (linkRoute === currentRoute) {
            l.classList.add('active');
        }
    }

    // Añadir el evento de clic a cada enlace
    linkColor.forEach(l => l.addEventListener('click', colorLink));

    // Marcar el enlace activo al cargar la página
    linkColor.forEach(link => {
        // Obtener el valor del parámetro 'route' del enlace
        const linkRoute = new URLSearchParams(link.getAttribute("href").split('?')[1]).get('route');

        // Comparar el parámetro 'route'
        if (linkRoute === currentRoute) {
            link.classList.add('active'); // Marcar como activo el enlace correspondiente
        }
    });
});