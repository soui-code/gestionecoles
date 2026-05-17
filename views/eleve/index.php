<?php
// views/eleves/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des élèves</title>
</head>
<body>

<h1>Liste des élèves</h1>

<?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
    <a href="/gestionecole/inscription/choix">+ Ajouter un élève</a>
<?php endif; ?>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($eleves)): ?>
    <p>Aucun élève enregistré.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date naissance</th>
                <th>Classe</th>
                <th>Parent</th>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <th>École</th>
                <?php endif; ?>
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
                <td><?= htmlspecialchars($eleve['classe_nom'] ?? '—') ?></td>
                <td><?= htmlspecialchars($eleve['parent_nom'] ?? '—') ?></td>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <td><?= htmlspecialchars($eleve['ecole_nom'] ?? '—') ?></td>
                <?php endif; ?>
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