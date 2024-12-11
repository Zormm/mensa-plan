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

// Funktion, um das erste valide Datum mit API-Daten zu finden
function findNextValidDate($baseDate, $apiBaseUrl, $canteenId, $maxDays = 14) {
    for ($i = 0; $i <= $maxDays; $i++) {
        $currentDate = date('Y-m-d', strtotime("$baseDate +$i days"));
        $apiUrl = "$apiBaseUrl/$canteenId/$currentDate?expand=true";

        // API-Daten abrufen
        $response = file_get_contents($apiUrl);

        // Prüfen, ob die Antwort valide ist und nicht leer
        if ($response !== false && !empty(json_decode($response, true))) {
            return [
                'date' => $currentDate,
                'data' => json_decode($response, true)
            ];
        }
    }

    // Wenn nach maxDays keine validen Daten gefunden werden
    return null;
}

// Basis-URL und Kantinen-ID definieren
$apiBaseUrl = 'https://mobil.itmc.tu-dortmund.de/canteen-menu/v3/canteens';
$canteenId = '341';

// Aktuelles Datum abrufen
$currentDate = date('Y-m-d');

// Nächste verfügbare Daten abrufen
$result = findNextValidDate($currentDate, $apiBaseUrl, $canteenId);

if ($result !== null) {
    $validDate = $result['date'];
    $data = $result['data'];

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
        echo "Gerichte für " . $validDate . ":\n";
        foreach ($filteredMeals as $meal) {
            echo "Gericht: " . $meal['title'] . " | Preis (Student): " . $meal['price'] . "\n";
        }
    } else {
        echo "Keine Gerichte für das Datum $validDate gefunden.\n";
    }
} else {
    echo "Keine gültigen Menü-Daten für die nächsten zwei Wochen gefunden.\n";
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
