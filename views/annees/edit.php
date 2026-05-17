<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Année Scolaire</title>
</head>
<body>

    <h2>Modifier l'Année Scolaire</h2>

    <a href="/gestionecole/annee/index">← Retour à la liste</a>

    <br><br>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color:green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="POST" action="/gestionecole/annee/edit?id=<?= $annee['id'] ?>">

        <label for="libelle">Libellé de l'année *</label><br>
        <input
            type="text"
            id="libelle"
            name="libelle"
            placeholder="Ex : 2025-2026"
            maxlength="9"
            value="<?= htmlspecialchars($_POST['libelle'] ?? $annee['libelle']) ?>"
            required
        ><br>
        <small>Format attendu : AAAA-AAAA (ex: 2025-2026)</small>

        <br><br>

        <a href="/gestionecole/annee/index">Annuler</a>
        <button type="submit">Enregistrer</button>

    </form>

</body>
</html>