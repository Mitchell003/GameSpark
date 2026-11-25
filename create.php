<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add game!</title>
    <link rel="stylesheet" href="css/style.css">
</head>


<body>
    <?php require_once 'header.php'; ?>
    <main>
        <from action="app/Http/Controllers/informatieController.php" method="post">
            <div class="form-group">
                <h2><strong>Nieuwe game toevoegen!</strong></h2>
                <h3>Vul de informatie van je game hier in.</h3>
                <div class="naam">
                    <label for="naam">Naam</label>
                    <input type="text" id="naam" name="naam" required>
                </div>

                <div class="afbeelding">
                    <label for="afbeelding">Afbeelding [URL]</label>
                    <input type="url" id="afbeelding" name="afbeelding"
                        placeholder="https://voorbeeld.nl/afbeelding.jpg" required>
                </div>

                <div class="platform">
                    <label for="platform">Platform</label>
                    <select name="platform" id="platform" required>
                        <option value="">Selecteer een platform</option>
                        <option value="pc">PC</option>
                        <option value="playstation">Playstation</option>
                        <option value="xbox">Xbox</option>
                        <option value="nintendo">Nintendo</option>
                    </select>

                    <div class="genre">
                        <label for="genre">Genre</label>
                        <select name="genre" id="genre" required>
                            <option value="">Selecteer een genre</option>
                            <option value="action">Actie</option>
                            <option value="adventure">Avontuur</option>
                            <option value="rpg">RPG</option>
                            <option value="strategy">Strategie</option>
                            <option value="horror">Horror</option>
                        </select>

                        <div class="releasejaar">
                            <label for="releasejaar">Releasejaar</label>
                            <input type="number" id="releasejaar" name="releasejaar" min="1900" max="2026" required>

                            <div class="status">
                                <label for="status">Status</label>
                                <select name="status" id="status" required>
                                    <option value="">Selecteer een status</option>
                                    <option value="wishlist">Wishlist</option>
                                    <option value="inbezit">In bezit</option>
                                    <option value="speeltnu">Speelt nu</option>
                                    <option value="uitgespeeld">Uitgespeeld</option>
                                </select>
                            </div>

                            <div class="beoordeling">
                                <label for="beoordeling">Beoordeling</label>
                                <input type="number" id="beoordeling" name="beoordeling" min="1" max="5" required>
                            </div>

                            <div class="submit-button">
                                <button type="submit">Toevoegen</button>
                            </div>
                        </div>
    </main>
    <?php require_once 'footer.php'; ?>
</body>

</html>