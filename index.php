<?php
session_start();

if (!isset($_SESSION['games'])) {
    $_SESSION['games'] = [];
}

$platforms = [
    'pc' => 'PC',
    'playstation' => 'Playstation',
    'xbox' => 'Xbox',
    'nintendo' => 'Nintendo',
];
$genres = [
    'action' => 'Action',
    'adventure' => 'Adventure',
    'rpg' => 'RPG',
    'strategy' => 'Strategy',
    'horror' => 'Horror',
];
$statuses = [
    'wishlist' => 'Wishlist',
    'inbezit' => 'In bezit',
    'speeltnu' => 'Speelt nu',
    'uitgespeeld' => 'Uitgespeeld',
];
$ratings = ['1', '2', '3', '4', '5'];
$form = [
    'name' => '',
    'platform' => '',
    'release_year' => '',
    'beoordeling' => '',
    'genre' => '',
    'status' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete_game') {
        $gameIndex = filter_var($_POST['game_index'] ?? null, FILTER_VALIDATE_INT);

        if ($gameIndex !== false && $gameIndex !== null && isset($_SESSION['games'][$gameIndex])) {
            unset($_SESSION['games'][$gameIndex]);
            $_SESSION['games'] = array_values($_SESSION['games']);
        }

        $queryString = http_build_query($_GET);
        header('Location: index.php' . ($queryString !== '' ? '?' . $queryString : ''));
        exit;
    }

    foreach ($form as $field => $value) {
        $form[$field] = trim((string) ($_POST[$field] ?? ''));
        if ($form[$field] === '') {
            $errors[$field] = 'Dit veld is verplicht.';
        }
    }

    if ($form['release_year'] !== '' &&
        (!ctype_digit($form['release_year']) ||
            (int) $form['release_year'] < 1 ||
            (int) $form['release_year'] > 9999)) {
        $errors['release_year'] = 'Vul een geldig releasejaar in.';
    }

    foreach ([
        'platform' => $platforms,
        'genre' => $genres,
        'status' => $statuses,
    ] as $field => $options) {
        if ($form[$field] !== '' && !array_key_exists($form[$field], $options)) {
            $errors[$field] = 'Kies een geldige optie.';
        }
    }

    if ($form['beoordeling'] !== '' && !in_array($form['beoordeling'], $ratings, true)) {
        $errors['beoordeling'] = 'Kies een beoordeling van 1 t/m 5.';
    }

    if (!$errors) {
        foreach ($_SESSION['games'] as $game) {
            if (strcasecmp(trim((string) ($game['name'] ?? '')), $form['name']) === 0 &&
                (int) ($game['release_year'] ?? 0) === (int) $form['release_year']) {
                $errors['name'] = 'Deze game met dit releasejaar is al opgeslagen.';
                break;
            }
        }
    }

    if (!$errors) {
        $_SESSION['games'][] = $form;
        header('Location: index.php');
        exit;
    }
}

$searchTerm = isset($_GET['search']) && is_string($_GET['search'])
    ? trim($_GET['search'])
    : '';
$filterOptions = [
    'platform' => array_keys($platforms),
    'genre' => array_keys($genres),
    'status' => array_keys($statuses),
    'beoordeling' => $ratings,
];
$filters = [];
foreach ($filterOptions as $field => $options) {
    $selected = $_GET[$field] ?? 'all';
    $filters[$field] = is_string($selected) &&
        ($selected === 'all' || in_array($selected, $options, true))
        ? $selected
        : 'all';
}
$hasActiveFilters = $searchTerm !== '';
foreach ($filters as $value) {
    if ($value !== 'all') {
        $hasActiveFilters = true;
        break;
    }
}

$visibleGames = array_filter(
    $_SESSION['games'],
    static function (array $game) use ($searchTerm, $filters): bool {
        if ($searchTerm !== '' &&
            stripos((string) ($game['name'] ?? ''), $searchTerm) === false &&
            (string) ($game['release_year'] ?? '') !== $searchTerm) {
            return false;
        }

        foreach ($filters as $field => $value) {
            if ($value !== 'all' && ($game[$field] ?? '') !== $value) {
                return false;
            }
        }

        return true;
    }
);

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Games</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require_once 'header.php';
    ?>
    <main>
        <div class="titelandbutton">
            <h2>Games</h2>
            <details class="add-game" <?= $errors ? 'open' : '' ?>>
                <summary class="button-game">+ Game toevoegen</summary>
                <form class="game-form" id="add-game-form" method="post" action="index.php">
                    <h3>Gamegegevens</h3>
                    <div class="game-form-grid">
                        <div class="game-form-field">
                            <label for="game-name">Naam</label>
                            <input id="game-name" name="name" type="text" required
                                   value="<?= escape($form['name']) ?>"
                                   aria-invalid="<?= isset($errors['name']) ? 'true' : 'false' ?>">
                            <?php if (isset($errors['name'])): ?>
                                <p class="error" role="alert"><?= escape($errors['name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="game-form-field">
                            <label for="game-platform">Platform</label>
                            <select id="game-platform" name="platform" required
                                    aria-invalid="<?= isset($errors['platform']) ? 'true' : 'false' ?>">
                                <option value="">Kies een platform</option>
                                <?php foreach ($platforms as $value => $label): ?>
                                    <option value="<?= escape($value) ?>" <?= $form['platform'] === $value ? 'selected' : '' ?>>
                                        <?= escape($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['platform'])): ?>
                                <p class="error" role="alert"><?= escape($errors['platform']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="game-form-field">
                            <label for="game-release-year">Releasejaar</label>
                            <input id="game-release-year" name="release_year" type="number" min="1" max="9999" required
                                   value="<?= escape($form['release_year']) ?>"
                                   aria-invalid="<?= isset($errors['release_year']) ? 'true' : 'false' ?>">
                            <?php if (isset($errors['release_year'])): ?>
                                <p class="error" role="alert"><?= escape($errors['release_year']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="game-form-field">
                            <label for="game-beoordeling">Beoordeling</label>
                            <select id="game-beoordeling" name="beoordeling" required
                                    aria-invalid="<?= isset($errors['beoordeling']) ? 'true' : 'false' ?>">
                                <option value="">Kies een beoordeling</option>
                                <?php foreach ($ratings as $rating): ?>
                                    <option value="<?= escape($rating) ?>" <?= $form['beoordeling'] === $rating ? 'selected' : '' ?>>
                                        <?= escape($rating) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['beoordeling'])): ?>
                                <p class="error" role="alert"><?= escape($errors['beoordeling']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="game-form-field">
                            <label for="game-genre">Genre</label>
                            <select id="game-genre" name="genre" required
                                    aria-invalid="<?= isset($errors['genre']) ? 'true' : 'false' ?>">
                                <option value="">Kies een genre</option>
                                <?php foreach ($genres as $value => $label): ?>
                                    <option value="<?= escape($value) ?>" <?= $form['genre'] === $value ? 'selected' : '' ?>>
                                        <?= escape($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['genre'])): ?>
                                <p class="error" role="alert"><?= escape($errors['genre']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="game-form-field">
                            <label for="game-status">Status</label>
                            <select id="game-status" name="status" required
                                    aria-invalid="<?= isset($errors['status']) ? 'true' : 'false' ?>">
                                <option value="">Kies een status</option>
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?= escape($value) ?>" <?= $form['status'] === $value ? 'selected' : '' ?>>
                                        <?= escape($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <p class="error" role="alert"><?= escape($errors['status']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="save-game-button" type="submit">Game opslaan</button>
                </form>
            </details>
        </div>
        <form class="filters-form" method="get" action="index.php">
            <div class="categorie-titles">
                <div class="categorie-filter search-filter">
                    <label for="search">Zoek op naam of releasejaar</label>
                    <input id="search" name="search" type="search"
                           value="<?= escape($searchTerm) ?>"
                           placeholder="Bijv. Portal of 2011">
                </div>
                <div class="categorie-filter">
                    <label for="platform">Platform</label>
                    <select name="platform" id="platform">
                        <option value="all">Alle platforms</option>
                        <?php foreach ($platforms as $value => $label): ?>
                            <option value="<?= escape($value) ?>" <?= $filters['platform'] === $value ? 'selected' : '' ?>>
                                <?= escape($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="categorie-filter">
                    <label for="genre">Genre</label>
                    <select name="genre" id="genre">
                        <option value="all">Alle genres</option>
                        <?php foreach ($genres as $value => $label): ?>
                            <option value="<?= escape($value) ?>" <?= $filters['genre'] === $value ? 'selected' : '' ?>>
                                <?= escape($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="categorie-filter">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="all">Alle statussen</option>
                        <?php foreach ($statuses as $value => $label): ?>
                            <option value="<?= escape($value) ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>>
                                <?= escape($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="categorie-filter">
                    <label for="beoordeling">Beoordeling</label>
                    <select name="beoordeling" id="beoordeling">
                        <option value="all">Alle scores</option>
                        <?php foreach ($ratings as $rating): ?>
                            <option value="<?= escape($rating) ?>" <?= $filters['beoordeling'] === $rating ? 'selected' : '' ?>>
                                <?= escape($rating) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit">Zoeken en filteren</button>
                <?php if ($hasActiveFilters): ?>
                    <a href="index.php">Wis zoekopdracht en filters</a>
                <?php endif; ?>
            </div>
        </form>
        <section class="game-list" aria-labelledby="games-list-title">
            <h2 id="games-list-title">Opgeslagen games</h2>
            <?php if ($visibleGames): ?>
                <?php if ($hasActiveFilters): ?>
                    <p class="results-count"><?= count($visibleGames) ?> van <?= count($_SESSION['games']) ?> games</p>
                <?php endif; ?>
                <div class="games-table-wrap">
                    <table class="games-table">
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Platform</th>
                                <th>Releasejaar</th>
                                <th>Beoordeling</th>
                                <th>Genre</th>
                                <th>Status</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visibleGames as $gameIndex => $game): ?>
                                <tr>
                                    <td><?= escape((string) ($game['name'] ?? '-')) ?></td>
                                    <td><?= escape($platforms[$game['platform'] ?? ''] ?? '-') ?></td>
                                    <td><?= escape((string) ($game['release_year'] ?? '-')) ?></td>
                                    <td><?= isset($game['beoordeling']) && $game['beoordeling'] !== ''
                                        ? escape((string) $game['beoordeling']) . '/5'
                                        : '-' ?></td>
                                    <td><?= escape($genres[$game['genre'] ?? ''] ?? '-') ?></td>
                                    <td><?= escape($statuses[$game['status'] ?? ''] ?? '-') ?></td>
                                    <td>
                                        <form method="post" action="index.php<?= $hasActiveFilters ? '?' . escape(http_build_query($_GET)) : '' ?>"
                                              onsubmit="return confirm(this.dataset.confirmMessage)"
                                              data-confirm-message="<?= escape('Weet je zeker dat je ' . ($game['name'] ?? 'deze game') . ' (' . ($game['release_year'] ?? 'onbekend jaar') . ') wilt verwijderen?') ?>">
                                            <input type="hidden" name="action" value="delete_game">
                                            <input type="hidden" name="game_index" value="<?= (int) $gameIndex ?>">
                                            <button type="submit">Verwijderen</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($_SESSION['games']): ?>
                <p class="empty-games">Geen games gevonden voor deze zoekopdracht en filters.</p>
            <?php else: ?>
                <p class="empty-games">Er zijn nog geen games opgeslagen.</p>
            <?php endif; ?>
        </section>
    </main>
    <?php require_once 'footer.php';
    ?>
</body>
</html>
