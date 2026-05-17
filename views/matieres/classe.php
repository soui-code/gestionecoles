<?php
// views/matieres/classe.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matières — <?= htmlspecialchars($classe['nom']) ?></title>
</head>
<body>

<h1>Matières — Classe <?= htmlspecialchars($classe['nom']) ?></h1>

<a href="/gestionecole/matiere/index">← Retour à la liste</a>
&nbsp;|&nbsp;
<?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
    <a href="/gestionecole/matiere/create?classe_id=<?= $classe['id'] ?>">+ Ajouter une matière</a>
<?php endif; ?>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p>
    Enseignant : <strong><?= htmlspecialchars($classe['enseignant_nom'] ?? '— Non affecté') ?></strong>
</p>

<?php if (empty($matieres)): ?>
    <p style="color:orange">⚠ Aucune matière définie pour cette classe.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Matière</th>
                <th>Note sur</th>
                <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matieres as $i => $matiere): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($matiere['nom']) ?></td>
            
                <td><?= $matiere['note_sur'] ?></td>
                <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
                <td>
                    <a href="/gestionecole/matiere/edit?id=<?= $matiere['id'] ?>">Modifier</a>
                    |
                    
                        <a href="/gestionecole/matiere/delete?id=<?= $matiere['id'] ?>"
                        onclick="return confirm('Supprimer cette matière ?')"
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