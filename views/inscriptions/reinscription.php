<?php
// views/inscriptions/reinscription.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinscription</title>
</head>
<body>

<h1>Réinscription</h1>

<a href="/gestionecole/inscription/choix">← Retour au choix</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p>Année scolaire : <strong><?= htmlspecialchars($anneeActive['libelle']) ?></strong></p>

<hr>

<!-- RECHERCHE PAR MATRICULE -->
<h3>Rechercher un élève</h3>

<form action="/gestionecole/inscription/reinscription" method="GET">
    <div>
        <label for="matricule">Matricule de l'élève</label><br>
        <input
            type="text"
            id="matricule"
            name="matricule"
            value="<?= htmlspecialchars($_GET['matricule'] ?? '') ?>"
            placeholder="Ex: MAT-2024-001"
        >
        <button type="submit">Rechercher</button>
    </div>
</form>

<br>

<!-- RÉSULTAT RECHERCHE -->
<?php if (isset($_GET['matricule']) && !$eleve): ?>
    <p style="color:orange">Aucun élève trouvé.</p>
<?php endif; ?>

<?php if ($eleve): ?>
    <hr>
    <h3>Élève trouvé</h3>

    <table border="1" cellpadding="8">
        <tr>
            <td>Matricule</td>
            <td><strong><?= htmlspecialchars($eleve['matricule']) ?></strong></td>
        </tr>
        <tr>
            <td>Nom</td>
            <td><?= htmlspecialchars($eleve['nom']) ?></td>
        </tr>
        <tr>
            <td>Prénom</td>
            <td><?= htmlspecialchars($eleve['prenom']) ?></td>
        </tr>
        <tr>
            <td>Date naissance</td>
            <td><?= $eleve['date_naissance'] ?? '—' ?></td>
        </tr>
        <tr>
            <td>Domicile</td>
            <td><?= htmlspecialchars($eleve['domicile'] ?? '—') ?></td>
        </tr>
        <tr>
            <td>Nom père</td>
            <td><?= htmlspecialchars($eleve['nom_pere'] ?? '—') ?></td>
        </tr>
        <tr>
            <td>Nom mère</td>
            <td><?= htmlspecialchars($eleve['nom_mere'] ?? '—') ?></td>
        </tr>
    </table>

    <br>

    <!-- FORMULAIRE RÉINSCRIPTION -->
    <form action="/gestionecole/inscription/reinscription" method="POST">

        <input type="hidden" name="eleve_id" value="<?= $eleve['id'] ?>">

        <div>
            <label for="classe_id">Nouvelle classe *</label><br>
            <select id="classe_id" name="classe_id" required>
                <option value="">-- Choisir une classe --</option>
                <?php foreach ($classes as $classe): ?>
                    <option value="<?= $classe['id'] ?>">
                        <?= htmlspecialchars($classe['nom']) ?>
                        <?= $classe['enseignant_nom'] ? '— ' . htmlspecialchars($classe['enseignant_nom']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br><br>

        <button type="submit">Suivant → Paiement</button>
        <a href="/gestionecole/inscription/choix">Annuler</a>

    </form>

<?php endif; ?>

</body>
</html>