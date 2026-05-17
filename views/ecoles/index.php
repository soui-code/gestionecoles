<?php
// views/ecoles/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des écoles</title>
</head>
<body>

<h1>Liste des écoles</h1>

<a href="/gestionecole/ecole/create">+ Ajouter une école</a>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($ecoles)): ?>
    <p>Aucune école enregistrée.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Date création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ecoles as $ecole): ?>
            <tr>
                <td><?= $ecole['id'] ?></td>
                <td><?= htmlspecialchars($ecole['nom']) ?></td>
                <td><?= htmlspecialchars($ecole['adresse']) ?></td>
                <td><?= htmlspecialchars($ecole['telephone']) ?></td>
                <td><?= htmlspecialchars($ecole['email']) ?></td>
                <td><?= $ecole['date_creation'] ?></td>
                <td>
                    <a href="/gestionecole/ecole/edit?id=<?= $ecole['id'] ?>">Modifier</a>
                    |
                    <a 
                        href="/gestionecole/ecole/delete?id=<?= $ecole['id'] ?>"
                        onclick="return confirm('Supprimer cette école ?')"
                    >Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>