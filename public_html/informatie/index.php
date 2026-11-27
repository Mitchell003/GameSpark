<?php

require_once __DIR__ . '/../../app/conn.php';

$filterOpties = [
    'platform' => ['pc', 'playstation', 'xbox', 'nintendo'],
    'genre' => ['action', 'adventure', 'rpg', 'strategy', 'horror'],
    'status' => ['wishlist', 'inbezit', 'speeltnu', 'uitgespeeld'],
    'beoordeling' => ['1', '2', '3', '4', '5'],
];
$filterNamen = [
    'platform' => [
        'pc' => 'PC',
        'playstation' => 'Playstation',
        'xbox' => 'Xbox',
        'nintendo' => 'Nintendo',
    ],
    'genre' => [
        'action' => 'Actie',
        'adventure' => 'Avontuur',
        'rpg' => 'RPG',
        'strategy' => 'Strategie',
        'horror' => 'Horror',
    ],
    'status' => [
        'wishlist' => 'Wishlist',
        'inbezit' => 'In bezit',
        'speeltnu' => 'Speelt nu',
        'uitgespeeld' => 'Uitgespeeld',
    ],
];
$filters = [];
$filterError = '';
$where = [];
$parameters = [];

foreach ($filterOpties as $veld => $opties) {
    $waarde = $_GET[$veld] ?? '';
    if (!is_string($waarde)) {
        $waarde = '';
    }

    if ($waarde !== '' && !in_array($waarde, $opties, true)) {
        $waarde = '';
        $filterError = 'Ongeldige filters zijn genegeerd.';
    }

    $filters[$veld] = $waarde;
    if ($waarde !== '') {
        $where[] = $veld . ' = :' . $veld;
        $parameters[':' . $veld] = $waarde;
    }
}

$query = 'SELECT * FROM informatie';
if ($where !== []) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}
$query .= ' ORDER BY beoordeling DESC';
$statement = $conn->prepare($query);
$statement->execute($parameters);
$informatie = $statement->fetchAll();
$totaalStatement = $conn->query('SELECT COUNT(*) FROM informatie');
$totaalGames = (int) $totaalStatement->fetchColumn();
$heeftFilter = in_array(true, array_map(static function (string $waarde): bool {
    return $waarde !== '';
}, $filters), true);

function escape(string $waarde): string
{
    return htmlspecialchars($waarde, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <!-- Stel de tekencodering, mobiele weergave en stylesheet van de overzichtspagina in. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Games</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <!-- Hergebruik de gedeelde navigatie. -->
    <?php require_once '../header.php';
    ?>
    <main>
        <!-- Paginatitel en link naar het formulier om een game toe te voegen. -->
        <div class="titelandbutton">
            <h2>Games</h2>
            <a href="create.php" class="button-game">+ Game toevoegen</a>
        </div>
        <form class="filters-form" method="get" action="index.php">
            <div class="categorie-titles">
                <div class="categorie-filter">
                    <label for="platform">Platform</label>
                    <select name="platform" id="platform">
                        <option value="">Alle platforms</option>
                        <?php foreach ($filterOpties['platform'] as $optie): ?>
                            <option value="<?= escape($optie) ?>" <?= $filters['platform'] === $optie ? 'selected' : '' ?>>
                                <?= escape($filterNamen['platform'][$optie]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="categorie-filter">
                    <label for="genre">Genre</label>
                    <select name="genre" id="genre">
                        <option value="">Alle genres</option>
                        <?php foreach ($filterOpties['genre'] as $optie): ?>
                            <option value="<?= escape($optie) ?>" <?= $filters['genre'] === $optie ? 'selected' : '' ?>>
                                <?= escape($filterNamen['genre'][$optie]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="categorie-filter">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="">Alle statussen</option>
                        <?php foreach ($filterOpties['status'] as $optie): ?>
                            <option value="<?= escape($optie) ?>" <?= $filters['status'] === $optie ? 'selected' : '' ?>>
                                <?= escape($filterNamen['status'][$optie]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="categorie-filter">
                    <label for="beoordeling">Beoordeling</label>
                    <select name="beoordeling" id="beoordeling">
                        <option value="">Alle scores</option>
                        <?php foreach ($filterOpties['beoordeling'] as $optie): ?>
                            <option value="<?= escape($optie) ?>" <?= $filters['beoordeling'] === $optie ? 'selected' : '' ?>>
                                <?= escape($optie) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit">Filter</button>
                <?php if ($heeftFilter): ?>
                    <a href="index.php">Wis filters</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($filterError !== ''): ?>
            <p class="error" role="alert"><?= escape($filterError) ?></p>
        <?php endif; ?>
        <p class="results-count">
            <?php if ($heeftFilter): ?>
                <?= count($informatie) ?> van <?= $totaalGames ?> games
            <?php else: ?>
                <?= $totaalGames ?> games
            <?php endif; ?>
        </p>

<?php if ($informatie === [] && $totaalGames === 0): ?>
    <p class="empty-games">Er zijn nog geen games toegevoegd.</p>
<?php elseif ($informatie === []): ?>
    <p class="empty-games">Geen games gevonden met deze filters.</p>
<?php else: ?>
    <table class="games-table">
        <thead>
            <tr>
                <th scope="col">Afbeelding</th>
                <th scope="col">Naam</th>
                <th scope="col">Platform</th>
                <th scope="col">Genre</th>
                <th scope="col">Releasejaar</th>
                <th scope="col">Status</th>
                <th scope="col">Beoordeling</th>
                <th scope="col">Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($informatie as $game): ?>
                <tr>
                    <td>
                        <img
                            class="game-image"
                            src="<?= htmlspecialchars($game['afbeelding'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="Afbeelding van <?= htmlspecialchars($game['naam'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </td>
                    <td><?= htmlspecialchars($game['naam'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= escape($filterNamen['platform'][$game['platform']] ?? $game['platform']) ?></td>
                    <td><?= escape($filterNamen['genre'][$game['genre']] ?? $game['genre']) ?></td>
                    <td><?= htmlspecialchars($game['releasejaar'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= escape($filterNamen['status'][$game['status']] ?? $game['status']) ?></td>
                    <td><?= htmlspecialchars($game['beoordeling'], ENT_QUOTES, 'UTF-8') ?>/5</td>
                    <td><a href="edit.php?id=<?= (int) $game['id'] ?>">Aanpassen</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


    </main>
    <!-- Hergebruik de gedeelde voettekst. -->
    <?php require_once '../footer.php';
    ?>
</body>
</html>