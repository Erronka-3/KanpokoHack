const https = require('https');
const fs = require('fs');
const path = require('path');

// Cargar el certificado y la clave privada
const options = {
    key: fs.readFileSync('key.pem'),
    cert: fs.readFileSync('cert.pem')
};

// Crear el servidor HTTPS
const server = https.createServer(options, (req, res) => {
    // Verificar si la solicitud es para la raíz
    if (req.url === '/' || req.url === '/index.html') {
        // Construir la ruta al archivo index.html en la carpeta frontend
        const filePath = path.join(__dirname, 'frontend', 'index.html');
        
        // Leer el archivo index.html
        fs.readFile(filePath, (err, data) => {
            if (err) {
                res.writeHead(500, { 'Content-Type': 'text/plain' });
                res.end('Error interno del servidor');
            } else {
                res.writeHead(200, { 'Content-Type': 'text/html' });
                res.end(data);
            }
        });
    } else {
        // Si la URL no es raíz, devolver un error 404
        res.writeHead(404, { 'Content-Type': 'text/plain' });
        res.end('404 No encontrado');
    }
});

// Iniciar el servidor en el puerto 3000
server.listen(3000, 'localhost', () => {
    console.log('Servidor HTTPS corriendo en https://localhost:3000/');
});
