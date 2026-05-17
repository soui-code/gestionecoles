<?php
// views/enseignants/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des enseignants</title>
</head>
<body>

<h1>Enseignants et Secrétaires</h1>

<a href="/gestionecole/enseignant/create">+ Ajouter</a>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($enseignants)): ?>
    <p>Aucun enseignant ou secrétaire enregistré.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôle</th>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <th>École</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($enseignants as $enseignant): ?>
            <tr>
                <td><?= $enseignant['id'] ?></td>
                <td><?= htmlspecialchars($enseignant['nom']) ?></td>
                <td><?= htmlspecialchars($enseignant['prenom'] ?? '—') ?></td>
                <td><?= htmlspecialchars($enseignant['email'] ?? '—') ?></td>
                <td><?= htmlspecialchars($enseignant['tel'] ?? '—') ?></td>
                <td><?= $enseignant['role'] === 'secretaire' ? 'Secrétaire' : 'Enseignant' ?></td>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <td><?= htmlspecialchars($enseignant['ecole_nom'] ?? '—') ?></td>
                <?php endif; ?>
                <td>
                    <a href="/gestionecole/enseignant/edit?id=<?= $enseignant['id'] ?>">Modifier</a>
                    |
                    
                        <a href="/gestionecole/enseignant/delete?id=<?= $enseignant['id'] ?>"
                        onclick="return confirm('Supprimer cet(te) <?= $enseignant['role'] === 'secretaire' ? 'secrétaire' : 'enseignant' ?> ?')"
                    >Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>