<?php
// views/inscriptions/paiement.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$data = $_SESSION['inscription_data'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement scolarité</title>
</head>
<body>

<h1>Paiement de la scolarité</h1>

<a href="/gestionecole/inscription/<?= $data['type'] === 'inscription' ? 'create' : 'reinscription' ?>">
    ← Retour
</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- RÉCAPITULATIF -->
<h3>Récapitulatif</h3>

<table border="1" cellpadding="8">
    <tr>
        <td>Type</td>
        <td>
            <?= $data['type'] === 'inscription'
                ? '📝 Nouvelle inscription'
                : '🔄 Réinscription'
            ?>
        </td>
    </tr>
    <tr>
        <td>Élève</td>
        <td>
            <strong>
                <?= htmlspecialchars($data['eleve']['nom']) ?>
                <?= htmlspecialchars($data['eleve']['prenom']) ?>
            </strong>
        </td>
    </tr>
    <tr>
        <td>Matricule</td>
        <td><?= htmlspecialchars($data['eleve']['matricule']) ?></td>
    </tr>
    <tr>
        <td>Classe</td>
        <td><?= htmlspecialchars($classe['nom']) ?></td>
    </tr>
    <tr>
        <td>Année scolaire</td>
        <td><?= htmlspecialchars($data['annee_libelle']) ?></td>
    </tr>
    <tr>
        <td>Scolarité annuelle</td>
        <td>
            <?php if ($data['montant_scolarite'] > 0): ?>
                <strong><?= number_format($data['montant_scolarite'], 0, ',', ' ') ?> FCFA</strong>
            <?php else: ?>
                <span style="color:orange">⚠ Scolarité non définie pour cette classe</span>
            <?php endif; ?>
        </td>
    </tr>
</table>

<br>

<!-- FORMULAIRE PAIEMENT -->
<h3>Enregistrer le paiement</h3>

<form action="/gestionecole/inscription/paiement" method="POST">

    <div>
        <label for="montant">Montant payé (FCFA) *</label><br>
        <input
            type="number"
            id="montant"
            name="montant"
            value="<?= htmlspecialchars($_POST['montant'] ?? $data['montant_scolarite']) ?>"
            min="0"
            step="100"
            required
        >
    </div>

    <br>

    <div>
        <label for="statut">Statut du paiement *</label><br>
        <select id="statut" name="statut" required>
            <option value="">-- Choisir --</option>
            <option value="paye"    <?= ($_POST['statut'] ?? '') === 'paye'    ? 'selected' : '' ?>>
                ✔ Payé intégralement
            </option>
            <option value="partiel" <?= ($_POST['statut'] ?? '') === 'partiel' ? 'selected' : '' ?>>
                ⚡ Paiement partiel
            </option>
            <option value="impaye"  <?= ($_POST['statut'] ?? '') === 'impaye'  ? 'selected' : '' ?>>
                ✖ Impayé
            </option>
        </select>
    </div>

    <br>

    <div>
        <label for="date_paiement">Date du paiement *</label><br>
        <input
            type="datetime-local"
            id="date_paiement"
            name="date_paiement"
            value="<?= htmlspecialchars($_POST['date_paiement'] ?? date('Y-m-d\TH:i')) ?>"
            required
        >
    </div>

    <br><br>

    <button type="submit">✔ Valider et générer la facture</button>
    <a href="/gestionecole/inscription/choix">Annuler</a>

</form>

</body>
</html>