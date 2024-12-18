function updateCardStatus(cardNumber) {
    alert("Función no implementada: Actualizar estado de " + cardNumber);
}

document.getElementById('card-query-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const cardNumber = document.getElementById('card-number').value.trim();
    const card = cards.find(c => c.number === cardNumber);

    if (card) {
        document.getElementById('card-info').classList.remove('d-none');
        document.getElementById('card-info-number').textContent = card.number;
        document.getElementById('card-info-status').textContent = card.status;
        document.getElementById('card-info-transaction').textContent = card.lastTransaction;
    } else {
        alert('Tarjeta no encontrada.');
        document.getElementById('card-info').classList.add('d-none');
    }
});



document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.card-selectable');
    let activeCard = '<?php echo $currentCard; ?>'; // Tarjeta activa actual (DEBIT o CREDIT)

    // Marcar visualmente la tarjeta activa al cargar
    if (activeCard === 'DEBIT') {
        document.getElementById('debit-card').classList.add('active');
    } else if (activeCard === 'CREDIT') {
        document.getElementById('credit-card').classList.add('active');
    }

    // Evento de selección de tarjetas
    cards.forEach(card => {
        card.addEventListener('click', () => {
            const cardType = card.dataset.cardType;

            // Verificar si la tarjeta seleccionada ya está activa
            if (activeCard === cardType) {
                alert(`La tarjeta ${cardType} ya está activa.`);
                return; // Salir sin hacer nada
            }

            // Realizar la solicitud para activar la nueva tarjeta
            fetch(`<?php echo ENABLECARDS1; ?><?php echo $username; ?>/${cardType}`, {
                    method: 'GET'
                })
                .then(response => response
                    .json()) // Asegúrate de que la respuesta esté en formato JSON
                .then(data => {
                    console.log(data); // Agregar este log para depuración

                    // alert(`Tarjeta ${cardType} activada correctamente.`);
                    activeCard = cardType; // Actualizar el estado activo
                    updateCardVisuals(card); // Actualizar visualmente
                    updateCardStatusInTable(
                        cardType); // Actualizar estado en la tabla

                })

        });
    });

    // Función para actualizar la visualización de tarjetas activas
    function updateCardVisuals(selectedCard) {
        cards.forEach(card => card.classList.remove(
            'active')); // Quitar la clase activa de todas las tarjetas
        selectedCard.classList.add('active'); // Activar la tarjeta seleccionada

    }

    // Función para actualizar el estado de la tarjeta en la tabla
    function updateCardStatusInTable(cardType) {
        const statusColumn = document.querySelectorAll('table tbody tr');
        statusColumn.forEach(row => {
            const cardNumber = row.cells[0].textContent.trim();
            if (cardNumber === "<?php echo $cards[1]['number']; ?>") { // Tarjeta Débito
                row.cells[1].textContent = (cardType === 'DEBIT') ? 'Activo' : 'Inactivo';
            }
            if (cardNumber === "<?php echo $cards[0]['number']; ?>") { // Tarjeta Crédito
                row.cells[1].textContent = (cardType === 'CREDIT') ? 'Activo' : 'Inactivo';
            }
        });
    }


});