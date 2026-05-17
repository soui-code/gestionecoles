<?php
// views/scolarites/edit.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un tarif de scolarité</title>
</head>
<body>

<h1>Modifier le tarif de scolarité</h1>

<a href="/gestionecole/scolarite/index">← Retour à la liste</a>

<br><br>

<p>Année scolaire : <strong><?= htmlspecialchars($scolarite['annee_libelle']) ?></strong></p>
<p>Classe : <strong><?= htmlspecialchars($scolarite['classe_nom']) ?></strong></p>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/scolarite/edit?id=<?= $scolarite['id'] ?>" method="POST">

    <div>
        <label for="montant_inscription">Frais d'inscription (FCFA)</label><br>
        <input
            type="number"
            id="montant_inscription"
            name="montant_inscription"
            value="<?= htmlspecialchars($_POST['montant_inscription'] ?? $scolarite['montant_inscription']) ?>"
            min="0"
            step="100"
        >
    </div>

    <br>

    <div>
        <label for="montant_mensuel">Mensualité (FCFA)</label><br>
        <input
            type="number"
            id="montant_mensuel"
            name="montant_mensuel"
            value="<?= htmlspecialchars($_POST['montant_mensuel'] ?? $scolarite['montant_mensuel']) ?>"
            min="0"
            step="100"
        >
    </div>

    <br>

    <div>
        <label for="montant_annuel">Montant annuel (FCFA) *</label><br>
        <input
            type="number"
            id="montant_annuel"
            name="montant_annuel"
            value="<?= htmlspecialchars($_POST['montant_annuel'] ?? $scolarite['montant_annuel']) ?>"
            min="0"
            step="100"
            required
        >
    </div>

    <br>

    <button type="submit">Enregistrer les modifications</button>
    <a href="/gestionecole/scolarite/index">Annuler</a>

</form>

</body>
</html>