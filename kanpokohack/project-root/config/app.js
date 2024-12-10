const https = require('https');
const fs = require('fs');
const path = require('path');
const { createProxyMiddleware } = require('http-proxy-middleware');

// Cargar el certificado y la clave privada
const options = {
    key: fs.readFileSync('tls/key.pem'),
    cert: fs.readFileSync('tls/cert.pem')
};

// Crear el servidor HTTPS
const server = https.createServer(options, (req, res) => {
    // Redirigir todas las solicitudes a la carpeta 'phpFinal' al servidor PHP
    const proxy = createProxyMiddleware({
        target: 'http://localhost:8000', // Puerto del servidor PHP
        changeOrigin: true,
        pathRewrite: {
            '^/': '/', // Mantener las rutas como están
        },
    });

    proxy(req, res);
});

// Iniciar el servidor HTTPS
server.listen(3000, () => {
    console.log('Servidor HTTPS corriendo en https://localhost:3000/');
});
