<!DOCTYPE html>
<html lang="en">

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
            <a href="create.php" class="button-game">+ Game toevoegen</a>
        </div>
        <div class="categorie-titles">
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
    </main>
    <?php require_once 'footer.php';
    ?>
</body>
</html>