<?php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Enseignant</title>
</head>
<body>

<h1>Bonjour, <?= htmlspecialchars($currentUser['nom']) ?> 👋</h1>
<p>Tableau de bord — Enseignant</p>
<p>École : <strong><?= htmlspecialchars($currentUser['ecole_nom']) ?></strong></p>

<hr>

<h2>Mes classes (<?= count($classesAvecEleves) ?>)</h2>

<?php if (empty($classesAvecEleves)): ?>
    <p>Aucune classe affectée pour le moment.</p>
<?php else: ?>
    <?php foreach ($classesAvecEleves as $item): ?>

        <h3>
            Classe <?= htmlspecialchars($item['classe']['nom']) ?>
            — <?= $item['total'] ?> élève(s)
        </h3>

        <?php if (empty($item['eleves'])): ?>
            <p>Aucun élève dans cette classe.</p>
        <?php else: ?>
            <table border="1" cellpadding="8">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date naissance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($item['eleves'] as $eleve): ?>
                    <tr>
                        <td><?= $eleve['id'] ?></td>
                        <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                        <td><?= htmlspecialchars($eleve['nom']) ?></td>
                        <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                        <td><?= $eleve['date_naissance'] ?? '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <br>

    <?php endforeach; ?>
<?php endif; ?>

<hr>

<h2>Accès rapides</h2>
<ul>
    <li><a href="/gestionecole/classe/index">Mes classes</a></li>
    <li><a href="/gestionecole/eleve/index">Mes élèves</a></li>
    <li><a href="/gestionecole/note/index">Saisie des notes de mes élèves</a></li>
</ul>

<hr>

<a href="/gestionecole/auth/logout">Se déconnecter</a>

</body>
</html>