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
// Funktion zum Ermitteln des heutigen Datums oder des nächsten Wochentags
function getValidDate() {
    $date = new DateTime();
    $dayOfWeek = (int)$date->format('N'); // 1 = Montag, 7 = Sonntag

    // Wenn es Samstag (6) oder Sonntag (7) ist, auf Montag verschieben
    if ($dayOfWeek >= 6) {
        $date->modify('next monday');
    }

    return $date->format('Y-m-d');
}

// Aktuelles Datum oder nächster Werktag
$validDate = getValidDate();

// API-URL mit dem berechneten Datum
$apiUrl = "https://mobil.itmc.tu-dortmund.de/canteen-menu/v3/canteens/341/$validDate?expand=true";

// cURL-Initialisierung
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl); // Ziel-URL setzen
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Ergebnis als String zurückgeben
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]); // Optional: Header setzen

// API-Anfrage ausführen
$response = curl_exec($ch);

// Fehlerprüfung
if (curl_errno($ch)) {
    echo "cURL-Fehler: " . curl_error($ch);
    curl_close($ch);
    exit;
}

// Verbindung schließen
curl_close($ch);

// JSON-Antwort dekodieren
$data = json_decode($response, true);

// Nur deutsche Namen und Studentenpreise ausgeben
echo "<ul>";
if (isset($data['meals']) && is_array($data['meals'])) {
    foreach ($data['meals'] as $meal) {
        $nameGerman = $meal['name']['de'] ?? 'Unbekannt';
        $studentPrice = $meal['prices']['students'] ?? 'Keine Angabe';
        echo "<li>Gericht: $nameGerman - Studentenpreis: $studentPrice &euro;</li>";
    }
}
echo "</ul>";
?>

<div class="container">
    <h1>Willkommen auf der Mensa-Plan Seite</h1>
    <p>Hier seht ihr bald den aktuellen Menüplan der TU-Dortmund.</p>
    <p>Bald könnt ihr hier auch Markieren, wo ihr gerade sitzt</p>
    <p>Mit freundlichen Grüßen Jona und Leander :)</p>
</div>
</body>
</html>
