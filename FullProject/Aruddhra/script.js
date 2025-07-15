document.getElementById('parkForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const vehicleNumber = document.getElementById('vehicleNumber').value;
    const vehicleType = document.getElementById('vehicleType').value;
    fetch('park_vehicle.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ vehicleNumber, vehicleType })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('token').textContent = data.token;
        displayParking();
    });
});

function displayParking() {
    fetch('display_parking.php')
        .then(response => response.json())
        .then(data => {
            const parkingDisplay = document.getElementById('parkingDisplay');
            parkingDisplay.innerHTML = '';
            data.forEach((carriage, index) => {
                const carriageDiv = document.createElement('div');
                carriageDiv.classList.add('carriage');
                carriageDiv.innerHTML = `<h2>Carriage ${index + 1}</h2>`;
                carriage.forEach((space, spaceIndex) => {
                    const spaceDiv = document.createElement('div');
                    spaceDiv.classList.add('space');
                    spaceDiv.textContent = `Space ${spaceIndex + 1}: ${space.status}`;
                    if (space.status !== 'Empty') {
                        const tooltip = document.createElement('span');
                        tooltip.classList.add('tooltip');
                        tooltip.textContent = `Vehicle Number: ${space.vehicle_number}, Token: ${space.token}`;
                        spaceDiv.appendChild(tooltip);
                    }
                    carriageDiv.appendChild(spaceDiv);
                });
                parkingDisplay.appendChild(carriageDiv);
            });
        });
}

// Initial display
displayParking();
