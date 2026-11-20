<?php
session_start();

if (!isset($_SESSION['games'])) {
    $_SESSION['games'] = [];
}

$statuses = ['Wishlist', 'In bezit', 'Speelt nu', 'Uitgespeeld'];
$errors = [];
$filterStatus = trim((string) ($_GET['filter_status'] ?? ''));
$filterError = '';
$imageUrl = '';
$form = [
    'name' => '',
    'platform' => '',
    'release_year' => '',
    'status' => '',
    'rating' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageUrl = trim((string) ($_POST['image_url'] ?? ''));

    foreach ($form as $field => $value) {
        $form[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    foreach ($form as $field => $value) {
        if ($value === '') {
            $errors[$field] = 'Dit veld is verplicht.';
        }
    }

    if ($form['release_year'] !== '' &&
        (!ctype_digit($form['release_year']) ||
            (int) $form['release_year'] < 1 ||
            (int) $form['release_year'] > 9999)) {
        $errors['release_year'] = 'Vul een geldig releasejaar in.';
    }

    if ($form['status'] !== '' && !in_array($form['status'], $statuses, true)) {
        $errors['status'] = 'Kies een geldige status.';
    }

    if ($form['rating'] !== '' && !in_array($form['rating'], ['1', '2', '3', '4', '5'], true)) {
        $errors['rating'] = 'Kies een rating van 1 t/m 5 sterren.';
    }

    if (!$errors) {
        $_SESSION['games'][] = $form + ['image_url' => $imageUrl];
        header('Location: index.php');
        exit;
    }
}

if ($filterStatus !== '' && !in_array($filterStatus, $statuses, true)) {
    $filterStatus = '';
    $filterError = 'Ongeldige statusfilter. Alle games worden getoond.';
}

$visibleGames = array_values(array_filter(
    $_SESSION['games'],
    static function (array $game) use ($filterStatus): bool {
        return $filterStatus === '' || $game['status'] === $filterStatus;
    }
));

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function isHttpUrl(string $value): bool
{
    if (!filter_var($value, FILTER_VALIDATE_URL)) {
        return false;
    }

    $scheme = parse_url($value, PHP_URL_SCHEME);
    return is_string($scheme) && in_array(strtolower($scheme), ['http', 'https'], true);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Games bijhouden | GameSpark</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="page-content">
        <section class="intro">
            <p class="eyebrow">Jouw gamecollectie</p>
            <h1>Houd je games bij</h1>
            <p>Voeg een game toe en bekijk je collectie in één overzicht.</p>
        </section>

        <section class="panel" aria-labelledby="add-game-title">
            <h2 id="add-game-title">Game toevoegen</h2>
            <form method="post" action="index.php">
                <div class="form-grid">
                    <div class="form-field">
                        <label for="name">Naam</label>
                        <input id="name" name="name" type="text" required
                               value="<?= escape($form['name']) ?>"
                               aria-invalid="<?= isset($errors['name']) ? 'true' : 'false' ?>">
                        <?php if (isset($errors['name'])): ?>
                            <p class="error"><?= escape($errors['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="platform">Platform</label>
                        <input id="platform" name="platform" type="text" required
                               value="<?= escape($form['platform']) ?>"
                               aria-invalid="<?= isset($errors['platform']) ? 'true' : 'false' ?>">
                        <?php if (isset($errors['platform'])): ?>
                            <p class="error"><?= escape($errors['platform']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="release_year">Releasejaar</label>
                        <input id="release_year" name="release_year" type="number" min="1" max="9999" required
                               value="<?= escape($form['release_year']) ?>"
                               aria-invalid="<?= isset($errors['release_year']) ? 'true' : 'false' ?>">
                        <?php if (isset($errors['release_year'])): ?>
                            <p class="error"><?= escape($errors['release_year']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required
                                aria-invalid="<?= isset($errors['status']) ? 'true' : 'false' ?>">
                            <option value="">Kies een status</option>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= escape($status) ?>" <?= $form['status'] === $status ? 'selected' : '' ?>>
                                    <?= escape($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['status'])): ?>
                            <p class="error"><?= escape($errors['status']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="rating">Rating (1 t/m 5 sterren)</label>
                        <select id="rating" name="rating" required
                                aria-invalid="<?= isset($errors['rating']) ? 'true' : 'false' ?>">
                            <option value="">Kies een rating</option>
                            <?php for ($rating = 1; $rating <= 5; $rating++): ?>
                                <option value="<?= $rating ?>" <?= $form['rating'] === (string) $rating ? 'selected' : '' ?>>
                                    <?= $rating ?> <?= $rating === 1 ? 'ster' : 'sterren' ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <?php if (isset($errors['rating'])): ?>
                            <p class="error" role="alert"><?= escape($errors['rating']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="image_url">Miniatuurafbeelding-URL (optioneel)</label>
                        <input id="image_url" name="image_url" type="text"
                               placeholder="https://voorbeeld.nl/game.jpg"
                               value="<?= escape($imageUrl) ?>">
                    </div>
                </div>
                <button type="submit">Game opslaan</button>
            </form>
        </section>

        <section class="panel" aria-labelledby="games-title">
            <div class="list-heading">
                <h2 id="games-title">Mijn games</h2>
                <span class="game-count">
                    <?php if ($filterStatus !== ''): ?>
                        <?= count($visibleGames) ?> van <?= count($_SESSION['games']) ?> games
                    <?php else: ?>
                        <?= count($_SESSION['games']) ?> games
                    <?php endif; ?>
                </span>
            </div>
            <form class="filter-form" method="get" action="index.php">
                <div class="filter-field">
                    <label for="filter_status">Filter op status</label>
                    <select id="filter_status" name="filter_status">
                        <option value="">Alle statussen</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= escape($status) ?>" <?= $filterStatus === $status ? 'selected' : '' ?>>
                                <?= escape($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Filter</button>
                <?php if ($filterStatus !== ''): ?>
                    <a href="index.php">Wis filter</a>
                <?php endif; ?>
            </form>
            <?php if ($filterError !== ''): ?>
                <p class="error" role="alert"><?= escape($filterError) ?></p>
            <?php endif; ?>
            <?php if ($visibleGames): ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Afbeelding</th>
                                <th scope="col">Naam</th>
                                <th scope="col">Platform</th>
                                <th scope="col">Releasejaar</th>
                                <th scope="col">Status</th>
                                <th scope="col">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visibleGames as $game): ?>
                                <?php
                                $rating = in_array((string) ($game['rating'] ?? ''), ['1', '2', '3', '4', '5'], true)
                                    ? (int) $game['rating']
                                    : null;
                                $gameImageUrl = (string) ($game['image_url'] ?? '');
                                ?>
                                <tr>
                                    <td>
                                        <div class="game-thumbnail">
                                            <?php if (isHttpUrl($gameImageUrl)): ?>
                                                <img src="<?= escape($gameImageUrl) ?>"
                                                     alt="Miniatuur van <?= escape($game['name']) ?>"
                                                     loading="lazy"
                                                     onerror="this.hidden=true; this.nextElementSibling.hidden=false">
                                                <span class="thumbnail-fallback" role="img"
                                                      aria-label="Afbeelding niet beschikbaar" hidden>🎮</span>
                                            <?php else: ?>
                                                <span class="thumbnail-fallback" role="img"
                                                      aria-label="Geen afbeelding beschikbaar">🎮</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= escape($game['name']) ?></td>
                                    <td><?= escape($game['platform']) ?></td>
                                    <td><?= escape($game['release_year']) ?></td>
                                    <td><span class="status"><?= escape($game['status']) ?></span></td>
                                    <td>
                                        <?php if ($rating !== null): ?>
                                            <span class="rating" aria-label="<?= $rating ?> van 5 sterren">
                                                <span class="rating-stars"><?= str_repeat('&#9733;', $rating) ?><?= str_repeat('&#9734;', 5 - $rating) ?></span>
                                                <span class="rating-value"><?= $rating ?>/5</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="rating" aria-label="Geen rating">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($_SESSION['games']): ?>
                <p class="empty-state">Geen games gevonden met deze status.</p>
            <?php else: ?>
                <p class="empty-state">Je lijst is nog leeg. Voeg hierboven je eerste game toe.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
