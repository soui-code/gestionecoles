<?php
// views/scolarites/create.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Définir un tarif de scolarité</title>
</head>
<body>

<h1>Définir un tarif de scolarité</h1>

<a href="/gestionecole/scolarite/index">← Retour à la liste</a>

<br><br>

<p>Année scolaire : <strong><?= htmlspecialchars($anneeActive['libelle']) ?></strong></p>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/scolarite/create" method="POST">

    <div>
        <label for="classe_id">Classe *</label><br>
        <select id="classe_id" name="classe_id" required>
            <option value="">-- Choisir une classe --</option>
            <?php foreach ($classes as $classe): ?>
                <option
                    value="<?= $classe['id'] ?>"
                    <?= ($_POST['classe_id'] ?? '') == $classe['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($classe['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <div>
        <label for="montant_inscription">Frais d'inscription (FCFA)</label><br>
        <input
            type="number"
            id="montant_inscription"
            name="montant_inscription"
            value="<?= htmlspecialchars($_POST['montant_inscription'] ?? '0') ?>"
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
            value="<?= htmlspecialchars($_POST['montant_mensuel'] ?? '0') ?>"
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
            value="<?= htmlspecialchars($_POST['montant_annuel'] ?? '0') ?>"
            min="0"
            step="100"
            required
        >
    </div>

    <br>

    <button type="submit">Enregistrer</button>
    <a href="/gestionecole/scolarite/index">Annuler</a>

</form>

</body>
</html>