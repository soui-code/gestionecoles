<?php
// views/super_admin/dashboard.php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Super Admin</title>
</head>
<body>

<h1>Bonjour, <?= htmlspecialchars($currentUser['nom']) ?> 👋</h1>
<p>Tableau de bord — Super Administrateur</p>

<hr>

<h2>Statistiques globales</h2>

<table border="1" cellpadding="8">
    <tr>
        <td>🏫 Total écoles</td>
        <td><strong><?= $stats['total_ecoles'] ?></strong></td>
    </tr>
    <tr>
        <td>🧑‍💼 Total admins</td>
        <td><strong><?= $stats['total_admins'] ?></strong></td>
    </tr>
    <tr>
        <td>🧑‍🏫 Total enseignants</td>
        <td><strong><?= $stats['total_enseignants'] ?></strong></td>
    </tr>
    <tr>
        <td>📋 Total secrétaires</td>
        <td><strong><?= $stats['total_secretaires'] ?></strong></td>
    </tr>
    <tr>
        <td>👶 Total élèves</td>
        <td><strong><?= $stats['total_eleves'] ?></strong></td>
    </tr>
</table>

<hr>

<h2>Liste des écoles</h2>

<a href="/gestionecole/ecole/create">+ Ajouter une école</a>

<br><br>

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
                <th>Admins</th>
                <th>Enseignants</th>
                <th>Classes</th>
                <th>Élèves</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ecoles as $ecole): ?>
            <tr>
                <td><?= $ecole['id'] ?></td>
                <td><?= htmlspecialchars($ecole['nom']) ?></td>
                <td><?= htmlspecialchars($ecole['adresse'] ?? '—') ?></td>
                <td><?= htmlspecialchars($ecole['telephone'] ?? '—') ?></td>
                <td><?= htmlspecialchars($ecole['email'] ?? '—') ?></td>
                <td><?= $ecole['total_admins'] ?></td>
                <td><?= $ecole['total_enseignants'] ?></td>
                <td><?= $ecole['total_classes'] ?></td>
                <td><?= $ecole['total_eleves'] ?></td>
                <td>
                    <a href="/gestionecole/ecole/edit?id=<?= $ecole['id'] ?>">Modifier</a>
                    |
                    <a href="/gestionecole/user/create">+ Admin</a>
                    |
                    
                        <a href="/gestionecole/ecole/delete?id=<?= $ecole['id'] ?>"
                        onclick="return confirm('Supprimer cette école et toutes ses données ?')"
                    >Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr>

<h2>Accès rapides</h2>
<ul>
    <li><a href="/gestionecole/ecole/index">Gérer les écoles</a></li>
    <li><a href="/gestionecole/user/index">Gérer les admins</a></li>
    <li><a href="/gestionecole/enseignant/index">Gérer les enseignants</a></li>
    <li><a href="/gestionecole/eleve/index">Gérer les élèves</a></li>
    <li><a href="/gestionecole/classe/index">Gérer les classes</a></li>
    <li><a href="/gestionecole/matiere/index">Gérer les matières</a></li>
</ul>

<hr>

<a href="/gestionecole/auth/logout">Se déconnecter</a>

</body>
</html>