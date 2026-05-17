<?php
// views/admin/dashboard.php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>

<h1>Bonjour, <?= htmlspecialchars($currentUser['nom']) ?> 👋</h1>
<p>Tableau de bord — <?= htmlspecialchars($ecole['nom']) ?></p>

<hr>

<h2>Statistiques de l'école</h2>

<table border="1" cellpadding="8">
    <tr>
        <td>🏫 Classes</td>
        <td><strong><?= $stats['total_classes'] ?></strong></td>
    </tr>
    <tr>
        <td>👶 Élèves</td>
        <td><strong><?= $stats['total_eleves'] ?></strong></td>
    </tr>
    <tr>
        <td>🧑‍🏫 Enseignants</td>
        <td><strong><?= $stats['total_enseignants'] ?></strong></td>
    </tr>
    <tr>
        <td>📋 Secrétaires</td>
        <td><strong><?= $stats['total_secretaires'] ?></strong></td>
    </tr>
</table>

<hr>

<h2>Informations de l'école</h2>

<table border="1" cellpadding="8">
    <tr>
        <td>Nom</td>
        <td><?= htmlspecialchars($ecole['nom']) ?></td>
    </tr>
    <tr>
        <td>Adresse</td>
        <td><?= htmlspecialchars($ecole['adresse'] ?? '—') ?></td>
    </tr>
    <tr>
        <td>Téléphone</td>
        <td><?= htmlspecialchars($ecole['telephone'] ?? '—') ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><?= htmlspecialchars($ecole['email'] ?? '—') ?></td>
    </tr>
</table>

<hr>

<h2>Classes de l'école</h2>

<a href="/gestionecole/classe/create">+ Ajouter une classe</a>

<br><br>

<?php if (empty($classes)): ?>
    <p>Aucune classe enregistrée.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>#</th>
                <th>Classe</th>
                <th>Enseignant</th>
                <th>Élèves</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $classe): ?>
            <tr>
                <td><?= $classe['id'] ?></td>
                <td><?= htmlspecialchars($classe['nom']) ?></td>
                <td><?= htmlspecialchars($classe['enseignant_nom'] ?? '— Non affecté') ?></td>
                <td>
                    <a href="/gestionecole/eleve/show?classe_id=<?= $classe['id'] ?>">
                        Voir élèves
                    </a>
                </td>
                <td>
                    <a href="/gestionecole/classe/edit?id=<?= $classe['id'] ?>">Modifier</a>
                    |
                    
                        <a href="/gestionecole/classe/delete?id=<?= $classe['id'] ?>"
                        onclick="return confirm('Supprimer cette classe ?')"
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
    <li><a href="/gestionecole/enseignant/index">Gérer les enseignants</a></li>
    <li><a href="/gestionecole/eleve/index">Gérer les élèves</a></li>
    <li><a href="/gestionecole/inscription/index">Gérer les inscriptions</a></li>
    <li><a href="/gestionecole/classe/index">Gérer les classes</a></li>
    <li><a href="/gestionecole/matiere/index">Gérer les matières</a></li>
    <li><a href="/gestionecole/annee/index">Gérer les années scolaires</a></li>
    <li><a href="/gestionecole/note/index">Gérer les notes</a></li>
    <li><a href="/gestionecole/composition/index">Gérer les compositions</a></li>
</ul>

<hr>

<a href="/gestionecole/auth/logout">Se déconnecter</a>

</body>
</html>