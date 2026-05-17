<?php

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une classe</title>
</head>
<body>

<h1>Ajouter une classe</h1>

<a href="/gestionecole/classe/index">← Retour à la liste</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/classe/create" method="POST">

    <div>
        <label for="nom">Nom de la classe *</label><br>
        <select id="nom" name="nom" required>
            <option value="">-- Choisir une classe --</option>
            <?php foreach (['CP1', 'CP2', 'CE1', 'CE2', 'CM1', 'CM2'] as $niveau): ?>
                <option
                    value="<?= $niveau ?>"
                    <?= ($_POST['nom'] ?? '') === $niveau ? 'selected' : '' ?>
                >
                    <?= $niveau ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <div>
        <label for="enseignant_id">Enseignant</label><br>
        <select id="enseignant_id" name="enseignant_id">
            <option value="">-- Aucun enseignant --</option>
            <?php foreach ($enseignants as $enseignant): ?>
                <option
                    value="<?= $enseignant['id'] ?>"
                    <?= ($_POST['enseignant_id'] ?? '') == $enseignant['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($enseignant['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <button type="submit">Enregistrer</button>
    <a href="/gestionecole/classe/index">Annuler</a>

</form>

</body>
</html>