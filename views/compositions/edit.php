<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
  <h1>Modifier une composition</h1>  
    <form method="POST">
            <div>
                <label for="libelle">Libellé:</label>
                <input type="text" name="libelle" id="libelle" value="<?= htmlspecialchars($composition['libelle']) ?>" required>
            </div>
            <div>
                <label for="date_debut">Date de début:</label>
                <input type="date" name="date_debut" id="date_debut" value="<?= $composition['date_debut'] ?>" required>
            </div>
            <div>
                <label for="date_fin">Date de fin:</label>
                <input type="date" name="date_fin" id="date_fin" value="<?= $composition['date_fin'] ?>" required>
            </div>
            <button type="submit">Modifier</button>
        </form>
</body>
</html>