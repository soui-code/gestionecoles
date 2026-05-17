<?php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des utilisateurs</title>
</head>
<body>

<h1>Liste des utilisateurs</h1>

<a href="/gestionecole/user/create">+ Ajouter un utilisateur</a>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($users)): ?>
    <p>Aucun utilisateur enregistré.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <th>École</th>
                <?php endif; ?>
                <th>Créé le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['nom']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['role'] ?></td>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <td><?= htmlspecialchars($user['ecole_nom'] ?? '—') ?></td>
                <?php endif; ?>
                <td><?= $user['created_at'] ?></td>
                <td>
                    <a href="/gestionecole/user/edit?id=<?= $user['id'] ?>">Modifier</a>
                    <?php if ($user['id'] != $currentUser['id']): ?>
                    |
                    
                        <a href="/gestionecole/user/delete?id=<?= $user['id'] ?>"
                        onclick="return confirm('Supprimer cet utilisateur ?')"
                    >Supprimer</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>