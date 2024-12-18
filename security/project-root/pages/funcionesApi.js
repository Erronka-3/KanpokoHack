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
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ network: hosts.red })
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
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ip: host })
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
}