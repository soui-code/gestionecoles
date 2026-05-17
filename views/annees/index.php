<?php
// views/annees/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Années scolaires</title>
</head>
<body>

<h1>Années scolaires</h1>

<a href="/gestionecole/annee/create">+ Ajouter une année</a>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($anneeActive): ?>
    <p>
        Année scolaire active :
        <strong style="color:green"><?= htmlspecialchars($anneeActive['libelle']) ?></strong>
    </p>
<?php else: ?>
    <p style="color:orange">⚠ Aucune année scolaire active.</p>
<?php endif; ?>

<br>

<?php if (empty($annees)): ?>
    <p>Aucune année scolaire enregistrée.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Libellé</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($annees as $annee): ?>
            <tr>
                <td><?= $annee['id'] ?></td>
                <td><?= htmlspecialchars($annee['libelle']) ?></td>
                <td>
                    <?php if ($annee['etat'] == 1): ?>
                        <span style="color:green">✔ Active</span>
                    <?php else: ?>
                        <span style="color:gray">Inactive</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($annee['etat'] != 1): ?>
                        <a href="/gestionecole/annee/activate?id=<?= $annee['id'] ?>"
                           onclick="return confirm('Activer l\'année <?= htmlspecialchars($annee['libelle']) ?> ?')"
                        >Activer</a>
                        |
                        <a href="/gestionecole/annee/edit?id=<?= $annee['id'] ?>">Modifier</a>
                        |
                        
                            <a href="/gestionecole/annee/delete?id=<?= $annee['id'] ?>"
                            onclick="return confirm('Supprimer l\'année <?= htmlspecialchars($annee['libelle']) ?> ?')"
                        >Supprimer</a>
                    <?php else: ?>
                        <span style="color:gray">— Année en cours</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>