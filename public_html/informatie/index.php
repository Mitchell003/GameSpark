<?php

require_once __DIR__ . '/../../app/conn.php';

$query = 'SELECT * FROM informatie ORDER BY beoordeling DESC';
$statement = $conn->prepare($query);
$statement->execute();
$informatie = $statement->fetchAll();
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
        <!-- Keuzevelden voor de beschikbare gamefilters; ze zijn hier nog niet aan filterlogica gekoppeld. -->
        <div class="categorie-titles">
            <!-- Filterkeuze op gameplatform. -->
            <div class="categorie-filter">
                <label for="platform">Platform</label>
                <select name="platform" id="platform">
                    <option value="all">Alle platforms</option>
                    <option value="pc">PC</option>
                    <option value="playstation">Playstation</option>
                    <option value="xbox">Xbox</option>
                    <option value="nintendo">Nintendo</option>
                </select>
            </div>
            <!-- Filterkeuze op gamegenre. -->
            <div class="categorie-filter">
                <label for="genre">Genre</label>
                <select name="genre" id="genre">
                    <option value="all">Alle genres</option>
                    <option value="action">Action</option>
                    <option value="adventure">Adventure</option>
                    <option value="rpg">RPG</option>
                    <option value="strategy">Strategy</option>
                    <option value="horror">Horror</option>
                </select>
            </div>
            <!-- Filterkeuze op de status van de game. -->
            <div class="categorie-filter">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="all">Alle statussen</option>
                    <option value="wishlist">Wishlist</option>
                    <option value="inbezit">In bezit</option>
                    <option value="speeltnu">Speelt nu</option>
                    <option value="uitgespeeld">Uitgespeeld</option>
                </select>
            </div>
            <!-- Filterkeuze op beoordeling, van 1 tot en met 5. -->
            <div class="categorie-filter">
                <label for="beoordeling">Beoordeling</label>
                <select name="beoordeling" id="beoordeling">
                    <option value="all">Alle scores</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
        </div>

<?php if ($informatie === []): ?>
    <p class="empty-games">Er zijn nog geen games toegevoegd.</p>
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
                    <td><?= htmlspecialchars($platformNamen[$game['platform']] ?? $game['platform'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($genreNamen[$game['genre']] ?? $game['genre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($game['releasejaar'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($statusNamen[$game['status']] ?? $game['status'], ENT_QUOTES, 'UTF-8') ?></td>
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