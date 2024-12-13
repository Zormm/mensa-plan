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

if ($result !== null):
        $validDate = $result['date'];
        $data = $result['data'];
        $hauptgerichte = [];
        $beilagen = [];

        foreach ($data as $meal) {
            if (isset($meal['title']['de']) && isset($meal['price']['student'])) {
                if (($meal['counter'] == 'Beilagen')) {
                    $beilagen[] = [
                        'title' => $meal['title']['de'],
                        'price' => $meal['price']['student']
                    ];
                    continue;
                }

                $hauptgerichte[] = [
                    'title' => $meal['title']['de'],
                    'price' => $meal['price']['student']
                ];
            }
        }
        usort($hauptgerichte, function ($a, $b) {
            return $b['price'] <=> $a['price'];
        });


    // Konvertiere Datum zu "Wochentag, dd.mm.yyyy" Format
        $date = new DateTime($validDate);
        $days = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
        $weekday = $days[$date->format('w')];
        $formattedDate = sprintf('%s, den %s.%s.%s', $weekday, $date->format('d'), $date->format('m'), $date->format('Y'));

?>

<section class="flex flex-col gap-6">
        <h2 class="text-3xl mb-2">Gerichte für <?= $formattedDate ?>:</h2>
    <section>
        <h3 class="text-3xl text-primary">Hauptgerichte</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($hauptgerichte as $meal): ?>
                <div class="bg-white shadow-md p-4 rounded-md">
                    <h4 class="font-semibold"><?= htmlspecialchars($meal['title']) ?></h4>
                    <p class="">Preis (Student): <?= htmlspecialchars($meal['price']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section>
        <h4 class="text-3xl text-primary">Beilagen</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($beilagen as $meal): ?>
                <div class="bg-white shadow-md p-4 rounded-md">
                    <h3 class="font-semibold"><?= htmlspecialchars($meal['title']) ?></h3>
                    <p class="">Preis (Student): <?= htmlspecialchars($meal['price']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php else: ?>
        <p class="text-red-500">Keine gültigen Menü-Daten für die nächsten zwei Wochen gefunden.</p>
    <?php endif; ?>
</section>
