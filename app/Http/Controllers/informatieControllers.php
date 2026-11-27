<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Deze pagina accepteert alleen POST-verzoeken.');
}

$velden = ['naam', 'afbeelding', 'platform', 'genre', 'releasejaar', 'status', 'beoordeling'];
foreach ($velden as $veld) {
    if (!isset($_POST[$veld]) || !is_string($_POST[$veld])) {
        http_response_code(400);
        exit('Controleer de ingevulde gamegegevens en probeer het opnieuw.');
    }
}

$naam = trim($_POST['naam']);
$afbeelding = trim($_POST['afbeelding']);
$platform = $_POST['platform'];
$genre = $_POST['genre'];
$releasejaar = $_POST['releasejaar'];
$status = $_POST['status'];
$beoordeling = $_POST['beoordeling'];

$platforms = ['pc', 'playstation', 'xbox', 'nintendo'];
$genres = ['action', 'adventure', 'rpg', 'strategy', 'horror'];
$statussen = ['wishlist', 'inbezit', 'speeltnu', 'uitgespeeld'];

if (
    $naam === '' || strlen($naam) > 100 ||
    strlen($afbeelding) > 1000 || filter_var($afbeelding, FILTER_VALIDATE_URL) === false ||
    !in_array($platform, $platforms, true) ||
    !in_array($genre, $genres, true) ||
    filter_var($releasejaar, FILTER_VALIDATE_INT) === false ||
    (int) $releasejaar < 1900 || (int) $releasejaar > 2026 ||
    !in_array($status, $statussen, true) ||
    filter_var($beoordeling, FILTER_VALIDATE_INT) === false ||
    (int) $beoordeling < 1 || (int) $beoordeling > 5
) {
    http_response_code(400);
    exit('Controleer de ingevulde gamegegevens en probeer het opnieuw.');
}

require_once __DIR__ . '/../../conn.php';

$stmt = $conn->prepare(
    'INSERT INTO informatie (naam, afbeelding, platform, genre, releasejaar, status, beoordeling)
     VALUES (:naam, :afbeelding, :platform, :genre, :releasejaar, :status, :beoordeling)'
);
$stmt->execute([
    'naam' => $naam,
    'afbeelding' => $afbeelding,
    'platform' => $platform,
    'genre' => $genre,
    'releasejaar' => (string) $releasejaar,
    'status' => $status,
    'beoordeling' => (string) $beoordeling,
]);

header('Location: ../../../public_html/informatie/index.php');
exit;