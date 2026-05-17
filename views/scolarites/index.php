<?php
// views/scolarites/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Scolarités</title>
</head>
<body>

<h1>Tarifs de scolarité — <?= htmlspecialchars($anneeActive['libelle']) ?></h1>

<a href="/gestionecole/scolarite/create">+ Définir un tarif</a>
&nbsp;|&nbsp;
<a href="/gestionecole/scolarite/resume">📊 Résumé financier</a>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- CLASSES SANS TARIF -->
<?php if (!empty($classesSansTarif)): ?>
    <p style="color:orange">
        ⚠ <?= count($classesSansTarif) ?> classe(s) sans tarif défini :
        <?php foreach ($classesSansTarif as $classe): ?>
            <strong><?= htmlspecialchars($classe['nom']) ?></strong>
        <?php endforeach; ?>
    </p>
<?php endif; ?>

<!-- TABLEAU DES TARIFS -->
<?php if (empty($scolarites)): ?>
    <p>Aucun tarif défini pour cette année.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Classe</th>
                <th>Frais inscription</th>
                <th>Mensualité</th>
                <th>Montant annuel</th>
                <th>Élèves</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scolarites as $scolarite): ?>
            <tr>
                <td><?= htmlspecialchars($scolarite['classe_nom']) ?></td>
                <td><?= number_format($scolarite['montant_inscription'], 0, ',', ' ') ?> FCFA</td>
                <td><?= number_format($scolarite['montant_mensuel'], 0, ',', ' ') ?> FCFA</td>
                <td><strong><?= number_format($scolarite['montant_annuel'], 0, ',', ' ') ?> FCFA</strong></td>
                <td>
                    <a href="/gestionecole/scolarite/eleves?classe_id=<?= $scolarite['classe_id'] ?>">
                        Voir élèves
                    </a>
                </td>
                <td>
                    <a href="/gestionecole/scolarite/edit?id=<?= $scolarite['id'] ?>">Modifier</a>
                    |
                    
                        <a href="/gestionecole/scolarite/delete?id=<?= $scolarite['id'] ?>"
                        onclick="return confirm('Supprimer ce tarif ?')"
                    >Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>