<?php
// views/matieres/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matières</title>
</head>
<body>

<h1>Matières par classe</h1>

<br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($matieresByClasse)): ?>
    <p>Aucune matière enregistrée.</p>
<?php else: ?>
    <?php foreach ($matieresByClasse as $classeNom => $matieres): ?>

        <h3>
            Classe : <?= htmlspecialchars($classeNom ?? '— Non affectée') ?>
            <small>
                (<a href="/gestionecole/matiere/create?classe_id=<?= $matieres[0]['classe_id'] ?>">
                    + Ajouter une matière
                </a>)
            </small>
        </h3>

        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Note sur</th>
                    <?php if ($currentUser['role'] === 'super_admin'): ?>
                    <th>École</th>
                    <?php endif; ?>
                    <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
                    <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matieres as $matiere): ?>
                <tr>
                    <td><?= htmlspecialchars($matiere['nom']) ?></td>
                    <td><?= $matiere['note_sur'] ?></td>
                    <?php if ($currentUser['role'] === 'super_admin'): ?>
                    <td><?= htmlspecialchars($matiere['ecole_nom'] ?? '—') ?></td>
                    <?php endif; ?>
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

        <br>

    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>