<?php
session_start();

// Verificar si se ha recibido el código de autorización
if (!isset($_SESSION['user_roles'])) {
    header("Location: index.php?route=6");
    exit;
    
}
// Obtener roles
$userRoles = $_SESSION['user_roles'];
 
// Verificar si el usuario tiene el rol "admin"
$isAdmin = in_array('admin', $userRoles);

if (!$isAdmin){

    header("Location: https://localhost/Kanpokohack/Beñat/kanpokohack/project-root/public/index.php");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Network Scanner</title>
</head>

<body>

    <h1>Network Scanner</h1>
    <div>
        <button onclick="healthCheck()">Health Check</button>
        <button onclick="simpleScan()">Simple Scan</button>
        <button onclick="vulnScanWeb()">Vuln Scan Web</button>
        <button onclick="vulnScanServer()">Vuln Scan Server</button>
    </div>
    <div id="results"></div>

    <script>
    const apiUrl = 'https://10.11.0.117:5000'; // Replace with your Raspberry Pi's IP address

    async function healthCheck() {
        try {
            const response = await fetch(`${apiUrl}/`);
            const data = await response.json();
            document.getElementById('results').innerHTML = `<p>${data.status}</p>`;
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('results').innerHTML = `<p>Error: ${error.message}</p>`;
        }
    }

    async function simpleScan() {
        try {
            const response = await fetch(`${apiUrl}/simplescan`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    network: hosts.red
                })
            });
            const data = await response.json();
            document.getElementById('results').innerHTML = createSimpleScanTable(data);
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('results').innerHTML = `<p>Error: ${error.message}</p>`;
        }
    }

    async function vulnScan(host) {
        try {
            const response = await fetch(`${apiUrl}/vulnscan`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ip: host
                })
            });
            const data = await response.json();
            document.getElementById('results').innerHTML = createVulnScanTable(data);
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('results').innerHTML = `<p>Error: ${error.message}</p>`;
        }
    }

    function vulnScanWeb() {
        vulnScan(hosts.web);
    }

    function vulnScanServer() {
        vulnScan(hosts.server);
    }

    function createSimpleScanTable(data) {
        let tableHtml = '<table><tr><th>Host</th><th>Ports</th></tr>';
        data.forEach(host => {
            tableHtml += `<tr><td>${host.host}</td><td>`;
            if (host.ports.length > 0) {
                tableHtml += '<ul>';
                host.ports.forEach(port => {
                    tableHtml += `<li>Port ${port.port}: ${port.state}</li>`;
                });
                tableHtml += '</ul>';
            } else {
                tableHtml += 'No open ports found';
            }
            tableHtml += '</td></tr>';
        });
        tableHtml += '</table>';
        return tableHtml;
    }

    function createVulnScanTable(data) {
        let tableHtml = '<h2>Vulnerability Scan Results</h2>';

        for (const [ip, hostData] of Object.entries(data)) {
            tableHtml += `<h3>Host: ${ip}</h3>`;
            tableHtml += `<table>
            <tr>
                <th>Status</th>
                <td>${hostData.status}</td>
            </tr>`;

            if (hostData.status === "scanned" && hostData.ports.length > 0) {
                tableHtml += `<tr>
                <th>Ports</th>
                <td>
                    <table>
                        <tr><th>Port</th><th>Vulnerabilities</th></tr>`;

                hostData.ports.forEach(portData => {
                    tableHtml += `<tr>
                    <td>${portData.port}</td>
                    <td>`;
                    if (portData.vulnerabilities.length > 0) {
                        tableHtml += '<ul>';
                        portData.vulnerabilities.forEach(vuln => {
                            tableHtml += `<li>${vuln}</li>`;
                        });
                        tableHtml += '</ul>';
                    } else {
                        tableHtml += 'No vulnerabilities found';
                    }
                    tableHtml += `</td></tr>`;
                });

                tableHtml += `</table>
                </td>
            </tr>`;
            } else if (hostData.status === "no vulnerabilities found" || hostData.ports.length === 0) {
                tableHtml += `<tr>
                <th>Ports</th>
                <td>No open ports or vulnerabilities found</td>
            </tr>`;
            }

            tableHtml += `</table>`;
        }

        return tableHtml;
        // Configuración de IPs para los escaneos
        const hosts = {
            red: "10.11.0.0/24",
            web: "10.11.0.3",
            server: "10.11.0.9"
        };
    }
    </script>




</body>

</html>



<a href="index.php?route=6" class="nav_link">
    <i class='bx bx-log-out nav_icon'></i>
    <span class="nav_name">SignOut</span>
</a>