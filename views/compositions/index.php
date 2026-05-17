<?php
// views/compositions/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compositions</title>
</head>
<body>

<h1>Compositions — <?= htmlspecialchars($anneeActive['libelle']) ?></h1>

<a href="/gestionecole/composition/create">+ Ajouter une composition</a>

<br><br>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- ALERTE MINIMUM 3 -->
<?php if ($totalCompositions < 3): ?>
    <p style="color:orange">
        ⚠ Vous avez <?= $totalCompositions ?> composition(s).
        Le minimum requis est <strong>3</strong>.
        <?= 3 - $totalCompositions ?> composition(s) manquante(s).
    </p>
<?php else: ?>
    <p style="color:green">
        ✔ <?= $totalCompositions ?> composition(s) définie(s).
    </p>
<?php endif; ?>

<?php if (empty($compositions)): ?>
    <p>Aucune composition enregistrée pour cette année.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Ordre</th>
                <th>Libellé</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Durée</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($compositions as $composition): ?>
            <?php
                $debut  = new DateTime($composition['date_debut']);
                $fin    = new DateTime($composition['date_fin']);
                $duree  = $debut->diff($fin)->days + 1;
            ?>
            <tr>
                <td style="text-align:center">
                    <strong><?= $composition['ordre'] ?></strong>
                </td>
                <td><?= htmlspecialchars($composition['libelle']) ?></td>
                <td><?= date('d/m/Y', strtotime($composition['date_debut'])) ?></td>
                <td><?= date('d/m/Y', strtotime($composition['date_fin'])) ?></td>
                <td><?= $duree ?> jour(s)</td>
                <td>
                    <a href="/gestionecole/composition/edit?id=<?= $composition['id'] ?>">Modifier</a>
                    |
                    <?php if ($totalCompositions > 3): ?>
                        
                            href="/gestionecole/composition/delete?id=<?= $composition['id'] ?>"
                            onclick="return confirm('Supprimer cette composition ?')"
                        >Supprimer</a>
                    <?php else: ?>
                        <span style="color:gray">Supprimer</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>