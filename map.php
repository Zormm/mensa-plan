<?php
?>

<div id="map-body"> </div>
<div class="legend">
    <div class="legend-item"><span class="available"></span>Verfügbar</div>
    <div class="legend-item"><span class="selected"></span>Ausgewählt</div>
    <div class="legend-item"><span class="booked"></span>Gebucht</div>
</div>
<button class="book-button" id="bookButton" disabled>Buchen</button>


<script defer>
    const mapBody = document.getElementById('map-body');

    for (let i = 0; i < 12; i++) {
        mapBody.innerHTML += '<div class="map-table"></div>';
    }
    const mapTables = document.getElementsByClassName('map-table');
    for (item of mapTables) {
        for (let i = 0; i < 9; i++) {
            if (i == 4) {
                item.innerHTML += '<div class="map-table-table"></div>';
            } else {
                item.innerHTML += '<div class="map-table-seat"></div>';
            }
        }
    }

    const seats = document.querySelectorAll('.map-table-seat');
    const bookButton = document.getElementById('bookButton');
    let selectedSeat = null;

    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            // Ignore if seat is already booked
            if (seat.classList.contains('booked')) return;

            // Toggle selection
            if (seat === selectedSeat) {
                seat.classList.remove('selected');
                selectedSeat = null;
                bookButton.disabled = true;
            } else {
                // Deselect other seats
                seats.forEach(s => s.classList.remove('selected'));
                seat.classList.add('selected');
                selectedSeat = seat;
                bookButton.disabled = false;
            }
        });
    });

    // Book selected seat
    bookButton.addEventListener('click', () => {
        if (selectedSeat) {
            selectedSeat.classList.remove('selected');
            selectedSeat.classList.add('booked');
            selectedSeat = null;
            bookButton.disabled = true;
        }
    });
</script>
