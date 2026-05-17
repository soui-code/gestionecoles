<?php
// views/compositions/create.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une composition</title>
</head>
<body>

<h1>Ajouter une composition</h1>

<a href="/gestionecole/composition/index">← Retour à la liste</a>

<br><br>

<p>Année scolaire : <strong><?= htmlspecialchars($anneeActive['libelle']) ?></strong></p>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/composition/create" method="POST">

    <div>
        <label for="libelle">Libellé *</label><br>
        <input
            type="text"
            id="libelle"
            name="libelle"
            value="<?= htmlspecialchars($_POST['libelle'] ?? 'Composition ' . $nextOrdre) ?>"
            placeholder="Ex: Composition 1"
            required
        >
    </div>

    <br>

    <div>
        <label for="ordre">Ordre *</label><br>
        <input
            type="number"
            id="ordre"
            name="ordre"
            value="<?= htmlspecialchars($_POST['ordre'] ?? $nextOrdre) ?>"
            min="1"
            required
        >
        <small>Ordre suggéré : <?= $nextOrdre ?></small>
    </div>

    <br>

    <div>
        <label for="date_debut">Date de début *</label><br>
        <input
            type="date"
            id="date_debut"
            name="date_debut"
            value="<?= htmlspecialchars($_POST['date_debut'] ?? '') ?>"
            required
        >
    </div>

    <br>

    <div>
        <label for="date_fin">Date de fin *</label><br>
        <input
            type="date"
            id="date_fin"
            name="date_fin"
            value="<?= htmlspecialchars($_POST['date_fin'] ?? '') ?>"
            required
        >
    </div>

    <br>

    <button type="submit">Enregistrer</button>
    <a href="/gestionecole/composition/index">Annuler</a>

</form>

</body>
</html> 