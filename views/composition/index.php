<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="<?= BASE_URL ?>/composition/create">Créer une composition</a>
    <h1>liste des Compositions</h1>
    <?php if (empty($compositions)): ?>
        <p>Aucune composition trouvée.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Annee</th>
                    <th>libelle</th>
                    <th>date_debut</th>
                    <th>date_fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compositions as $composition): ?>
                    <tr>
                        <td><?= htmlspecialchars($composition['annee_libelle']) ?></td>
                        <td><?= htmlspecialchars($composition['libelle']) ?></td>
                        <td><?= htmlspecialchars($composition['date_debut']) ?></td>
                        <td><?= htmlspecialchars($composition['date_fin']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/composition/edit?id=<?= $composition['id'] ?>">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>