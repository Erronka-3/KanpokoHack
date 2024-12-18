function openModal(userId) {
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    const firstName = userRow.querySelector('.firstName-column').textContent;
    const lastName = userRow.querySelector('.lastName-column').textContent;
    const email = userRow.querySelector('.email-column').textContent;
    const status = userRow.querySelector('.status-column').textContent === 'Alta' ? 'alta' : 'baja';

    document.getElementById('user_id').value = userId;
    document.getElementById('firstName').value = firstName;
    document.getElementById('lastName').value = lastName;
    document.getElementById('email').value = email;
    document.getElementById('status').value = status;

    document.getElementById('modal').style.display = 'block';
}

async function updateUser(event) {
    event.preventDefault();

    const userId = document.getElementById('user_id').value;
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const email = document.getElementById('email').value;
    const status = document.getElementById('status').value;

    try {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                user_id: userId,
                firstName: firstName,
                lastName: lastName,
                email: email,
                status: status
            })
        });

        if (response.ok) {
            closeModal();

            // Actualizar la fila de la tabla
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (userRow) {
                userRow.querySelector('.firstName-column').textContent = firstName;
                userRow.querySelector('.lastName-column').textContent = lastName;
                userRow.querySelector('.email-column').textContent = email;
                userRow.querySelector('.status-column').textContent = status === 'alta' ? 'Alta' : 'Baja';
            }
        } else {
            console.error('Error al actualizar el usuario:', response.statusText);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
    }
}


function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

async function updateUserStatus(event) {
    event.preventDefault();

    const userId = document.getElementById('user_id').value;
    const status = document.getElementById('status').value;

    try {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                user_id: userId,
                status: status
            })
        });

        if (response.ok) {
            closeModal();

            // Actualizar el estado en la tabla
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (userRow) {
                userRow.querySelector('.status-column').textContent = status === 'alta' ? 'Alta' : 'Baja';
            }
        } else {
            console.error('Error al actualizar el estado:', response.statusText);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
    }
}