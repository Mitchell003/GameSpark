<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Deze pagina accepteert alleen POST-verzoeken.');
}

$errors = [];
$velden = ['naam', 'afbeelding', 'platform', 'genre', 'releasejaar', 'status', 'beoordeling'];
$gegevens = [];

foreach ($velden as $veld) {
    if (!isset($_POST[$veld]) || !is_string($_POST[$veld])) {
        $errors[] = 'Vul alle gamegegevens in.';
        break;
    }

    $gegevens[$veld] = trim($_POST[$veld]);
}

if ($errors === []) {
    $toegestanePlatforms = ['pc', 'playstation', 'xbox', 'nintendo'];
    $toegestaneGenres = ['action', 'adventure', 'rpg', 'strategy', 'horror'];
    $toegestaneStatussen = ['wishlist', 'inbezit', 'speeltnu', 'uitgespeeld'];

    if ($gegevens['naam'] === '' || strlen($gegevens['naam']) > 100) {
        $errors[] = 'Vul een gamenaam van maximaal 100 tekens in.';
    }

    if (
        strlen($gegevens['afbeelding']) > 1000 ||
        filter_var($gegevens['afbeelding'], FILTER_VALIDATE_URL) === false
    ) {
        $errors[] = 'Vul een geldige afbeeldings-URL in van maximaal 1000 tekens.';
    }

    if (!in_array($gegevens['platform'], $toegestanePlatforms, true)) {
        $errors[] = 'Kies een geldig platform.';
    }

    if (!in_array($gegevens['genre'], $toegestaneGenres, true)) {
        $errors[] = 'Kies een geldig genre.';
    }

    $releasejaar = filter_var($gegevens['releasejaar'], FILTER_VALIDATE_INT);
    if ($releasejaar === false || $releasejaar < 1900 || $releasejaar > 2026) {
        $errors[] = 'Vul een geldig releasejaar tussen 1900 en 2026 in.';
    }

    if (!in_array($gegevens['status'], $toegestaneStatussen, true)) {
        $errors[] = 'Kies een geldige status.';
    }

    $beoordeling = filter_var($gegevens['beoordeling'], FILTER_VALIDATE_INT);
    if ($beoordeling === false || $beoordeling < 1 || $beoordeling > 5) {
        $errors[] = 'Vul een beoordeling van 1 tot en met 5 in.';
    }
}

if ($errors !== []) {
    http_response_code(422);
    echo '<h1>Controleer de gamegegevens</h1><ul>';

    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }

    echo '</ul>';
    exit;
}

require_once __DIR__ . '/../../conn.php';

$duplicateStatement = $conn->prepare(
    'SELECT 1 FROM informatie WHERE naam = :naam AND releasejaar = :releasejaar LIMIT 1'
);
$duplicateStatement->execute([
    ':naam' => $gegevens['naam'],
    ':releasejaar' => (string) $releasejaar,
]);

if ($duplicateStatement->fetchColumn() !== false) {
    http_response_code(409);
    echo '<h1>Game bestaat al</h1><p>Deze game met dit releasejaar is al opgeslagen.</p>';
    exit;
}

$query = 'INSERT INTO informatie
    (naam, afbeelding, platform, genre, releasejaar, status, beoordeling)
    VALUES (:naam, :afbeelding, :platform, :genre, :releasejaar, :status, :beoordeling)';
$statement = $conn->prepare($query);
try {
    $statement->execute([
        ':naam' => $gegevens['naam'],
        ':afbeelding' => $gegevens['afbeelding'],
        ':platform' => $gegevens['platform'],
        ':genre' => $gegevens['genre'],
        ':releasejaar' => (string) $releasejaar,
        ':status' => $gegevens['status'],
        ':beoordeling' => (string) $beoordeling,
    ]);
} catch (PDOException $exception) {
    if ($exception->getCode() !== '23000') {
        throw $exception;
    }

    http_response_code(409);
    echo '<h1>Game bestaat al</h1><p>Deze game met dit releasejaar is al opgeslagen.</p>';
    exit;
}

header('Location: ../../../public_html/informatie/index.php');
exit;
