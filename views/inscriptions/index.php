<?php
// views/inscriptions/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des inscriptions</title>
</head>
<body>

<h1>Inscriptions — <?= htmlspecialchars($anneeActive['libelle']) ?></h1>

<a href="/gestionecole/inscription/choix">+ Nouvelle inscription</a>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($inscriptions)): ?>
    <p>Aucune inscription enregistrée.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Matricule</th>
                <th>Nom et prénom</th>
                <th>Classe</th>
                <th>Année</th>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <th>École</th>
                <?php endif; ?>
                <th>Date inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscriptions as $inscription): ?>
            <tr>
                <td><?= $inscription['id'] ?></td>
                <td><?= htmlspecialchars($inscription['matricule'] ?? '—') ?></td>
                <td>
                    <?= htmlspecialchars($inscription['eleve_nom'] ?? '—') ?>
                    <?= htmlspecialchars($inscription['eleve_prenom'] ?? '') ?>
                </td>
                <td><?= htmlspecialchars($inscription['classe_nom'] ?? '—') ?></td>
                <td><?= htmlspecialchars($inscription['annee_libelle'] ?? '—') ?></td>
                <?php if ($currentUser['role'] === 'super_admin'): ?>
                <td><?= htmlspecialchars($inscription['ecole_nom'] ?? '—') ?></td>
                <?php endif; ?>
                <td><?= date('d/m/Y', strtotime($inscription['created_at'])) ?></td>
                <td>
                <a href="/gestionecole/inscription/detail?id=<?= $inscription['id'] ?>">Voir</a>
                <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
    |
    
                <a href="/gestionecole/inscription/delete?id=<?= $inscription['id'] ?>"
                onclick="return confirm('Supprimer cette inscription ?')"
                >Supprimer</a>
                <?php endif; ?>
                </td>
                <td>
                    <?php if (in_array($currentUser['role'], ['super_admin', 'admin'])): ?>
                        
                            <a href="/gestionecole/inscription/delete?id=<?= $inscription['id'] ?>"
                            onclick="return confirm('Supprimer cette inscription ?')"
                        >Supprimer</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>