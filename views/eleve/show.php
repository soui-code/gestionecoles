<?php
// views/eleves/show.php
$currentUser = Auth::user();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Élèves de la classe <?= htmlspecialchars($classe['nom']) ?></title>
</head>
<body>

<h1>Classe : <?= htmlspecialchars($classe['nom']) ?></h1>
<p>Enseignant : <strong><?= htmlspecialchars($classe['enseignant_nom'] ?? '— Non affecté') ?></strong></p>
<p>École : <strong><?= htmlspecialchars($classe['ecole_nom']) ?></strong></p>

<hr>

<a href="/gestionecole/eleve/index">← Retour à la liste</a>

<?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
    &nbsp;|&nbsp;
    <a href="/gestionecole/inscription/choix">+ Ajouter un élève</a>
<?php endif; ?>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<h2>Élèves (<?= count($eleves) ?>)</h2>

<?php if (empty($eleves)): ?>
    <p>Aucun élève dans cette classe.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date naissance</th>
                <th>Parent</th>
                <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eleves as $eleve): ?>
            <tr>
                <td><?= $eleve['id'] ?></td>
                <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                <td><?= htmlspecialchars($eleve['nom']) ?></td>
                <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                <td><?= $eleve['date_naissance'] ?? '—' ?></td>
                <td><?= htmlspecialchars($eleve['parent_nom'] ?? '—') ?></td>
                <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
                <td>
                    <a href="/gestionecole/eleve/edit?id=<?= $eleve['id'] ?>">Modifier</a>
                    |
                    
                        <a href="/gestionecole/eleve/delete?id=<?= $eleve['id'] ?>"
                        onclick="return confirm('Supprimer cet élève ?')"
                    >Supprimer</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>