<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Créer une Composition</h1>
    <form  method="POST">
        <div>
            <label for="annee_id">Année:</label>
            <select name="annee_id" id="annee_id" required>
                <?php foreach ($annees as $annee): ?>
                    <option value="<?= $annee['id'] ?>"><?= htmlspecialchars($annee['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="libelle">Libellé:</label>
            <input type="text" name="libelle" id="libelle" required>
        </div>
        <div>
            <label for="date_debut">Date de début:</label>
            <input type="date" name="date_debut" id="date_debut" required>
        </div>
        <div>
            <label for="date_fin">Date de fin:</label>
            <input type="date" name="date_fin" id="date_fin" required>
        </div>
        <button type="submit">Créer</button>
    </form>
</body>
</html>