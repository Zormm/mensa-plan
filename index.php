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

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menüplan</title>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Menüplan</h1>
    <?php if ($result !== null): ?>
        <?php
        $validDate = $result['date'];
        $data = $result['data'];
        $filteredMeals = [];

        foreach ($data as $meal) {
            if (isset($meal['title']['de']) && isset($meal['price']['student'])) {
                $filteredMeals[] = [
                    'title' => $meal['title']['de'],
                    'price' => $meal['price']['student']
                ];
            }
        }
        ?>

        <h2 class="text-xl mb-2">Gerichte für <?= htmlspecialchars($validDate) ?>:</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($filteredMeals as $meal): ?>
                <div class="bg-white shadow-md p-4 rounded-md">
                    <h3 class="font-semibold"><?= htmlspecialchars($meal['title']) ?></h3>
                    <p class="text-gray-700">Preis (Student): <?= htmlspecialchars($meal['price']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p class="text-red-500">Keine gültigen Menü-Daten für die nächsten zwei Wochen gefunden.</p>
    <?php endif; ?>
</div>
</body>
</html>
