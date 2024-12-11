<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensa-Plan Seite</title>
    <style>
        <?php include './src/input.css'; ?>
    </style>
</head>
<body>
<?php

// Funktion, um zu prüfen, ob ein Datum ein Feiertag ist
function isHoliday($date) {
    // Hier könnten Feiertage definiert werden, je nach Region oder Organisation
    $holidays = [
        '2024-12-25', // Beispiel für einen Feiertag (Weihnachten)
        '2024-12-26'  // Beispiel für einen Feiertag (zweiter Weihnachtstag)
    ];
    return in_array($date, $holidays);
}

// Funktion, um den nächsten Wochentag zu berechnen
function getNextAvailableDate($date) {
    // Überprüfen, ob der gegebene Tag ein Feiertag ist oder nicht auf einen Wochentag fällt
    $nextDate = $date;
    while (isHoliday($nextDate) || (date('N', strtotime($nextDate)) >= 6)) {
        $nextDate = date('Y-m-d', strtotime($nextDate . ' +1 day'));
    }
    return $nextDate;
}

// Aktuelles Datum abrufen
$currentDate = date('Y-m-d');

// Nächster verfügbarer Tag ermitteln, falls der aktuelle Tag ein Feiertag ist
$finalDate = getNextAvailableDate($currentDate);

// API-URL definieren
$apiUrl = 'https://mobil.itmc.tu-dortmund.de/canteen-menu/v3/canteens/341/' . $finalDate . '?expand=true';

// API-Aufruf initialisieren
$response = file_get_contents($apiUrl);

// Überprüfen, ob die Antwort erfolgreich war
if ($response !== false) {
    // JSON-Daten dekodieren
    $data = json_decode($response, true);

    // Ergebnis-Array für die gefilterten Gerichte
    $filteredMeals = [];

    // Iteriere durch alle Gerichte und filtere basierend auf dem aktuellen Datum
    foreach ($data as $meal) {
        // Prüfen, ob der Titel auf Deutsch vorhanden ist und den Studentenpreis enthält
        if (isset($meal['title']['de']) && isset($meal['price']['student'])) {
            $filteredMeals[] = [
                'title' => $meal['title']['de'],
                'price' => $meal['price']['student']
            ];
        }
    }

    // Ausgabe der gefilterten Gerichte
    if (count($filteredMeals) > 0) {
        echo "Gerichte für " . $finalDate . ":\n";
        foreach ($filteredMeals as $meal) {
            echo "Gericht: " . $meal['title'] . " | Preis (Student): " . $meal['price'] . "\n";
        }
    } else {
        echo "Keine Gerichte für das aktuelle Datum gefunden.\n";
    }
} else {
    echo "Fehler beim Abrufen der API-Daten.\n";
}
?>


<div class="container">
    <h1>Willkommen auf der Mensa-Plan Seite</h1>
    <p>Hier seht ihr bald den aktuellen Menüplan der TU-Dortmund.</p>
    <p>Bald könnt ihr hier auch Markieren, wo ihr gerade sitzt</p>
    <p>Mit freundlichen Grüßen Jona und Leander :)</p>
</div>
</body>
</html>
