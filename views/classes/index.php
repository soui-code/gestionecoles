<?php
// views/classes/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des classes</title>
</head>
<body>

<h1>Liste des classes</h1>

<?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
    <a href="/gestionecole/classe/create">+ Ajouter une classe</a>
<?php endif; ?>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($classes)): ?>
    <p>Aucune classe enregistrée.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Classe</th>
                <th>Enseignant</th>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <th>École</th>
                <?php endif; ?>
                <th>Élèves</th>
                <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $classe): ?>
            <tr>
                <td><?= $classe['id'] ?></td>
                <td><?= htmlspecialchars($classe['nom']) ?></td>
                <td><?= htmlspecialchars($classe['enseignant_nom'] ?? '— Non affecté') ?></td>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <td><?= htmlspecialchars($classe['ecole_nom'] ?? '—') ?></td>
                <?php endif; ?>
                <td>
                    <a href="/gestionecole/eleve/show?classe_id=<?= $classe['id'] ?>">Voir élèves</a>
                    &nbsp;|&nbsp;
                    <a href="/gestionecole/eleve/listeClasse?classe_id=<?= $classe['id'] ?>">📋 Liste</a>
                </td>
                <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
                <td>
                    <a href="/gestionecole/classe/edit?id=<?= $classe['id'] ?>">Modifier</a>
                    |
                    
                        <a href="/gestionecole/classe/delete?id=<?= $classe['id'] ?>"
                        onclick="return confirm('Supprimer cette classe ?')"
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