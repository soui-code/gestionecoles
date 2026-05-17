<?php
// views/notes/index.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notes</title>
</head>
<body>

<h1>Gestion des notes</h1>

<p>Année scolaire : <strong><?= htmlspecialchars($anneeActive['libelle']) ?></strong></p>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (empty($compositions)): ?>
    <p style="color:orange">
        ⚠ Aucune composition définie pour cette année.
        <a href="/gestionecole/composition/create">Ajouter une composition</a>
    </p>
<?php elseif (empty($classes)): ?>
    <p style="color:orange">
        ⚠ Aucune classe disponible.
    </p>
<?php else: ?>

    <form action="/gestionecole/note/saisie" method="GET">

        <div>
            <label for="classe_id">Classe *</label><br>
            <select id="classe_id" name="classe_id" required>
                <option value="">-- Choisir une classe --</option>
                <?php foreach ($classes as $classe): ?>
                    <option value="<?= $classe['id'] ?>">
                        <?= htmlspecialchars($classe['nom']) ?>
                        <?= $classe['enseignant_nom']
                            ? '— ' . htmlspecialchars($classe['enseignant_nom'])
                            : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label for="composition_id">Composition *</label><br>
            <select id="composition_id" name="composition_id" required>
                <option value="">-- Choisir une composition --</option>
                <?php foreach ($compositions as $composition): ?>
                    <option value="<?= $composition['id'] ?>">
                        <?= htmlspecialchars($composition['libelle']) ?>
                        (<?= date('d/m/Y', strtotime($composition['date_debut'])) ?>
                        →
                        <?= date('d/m/Y', strtotime($composition['date_fin'])) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <button type="submit">✏ Saisir les notes</button>

    </form>

    <br><hr><br>

    <h3>Voir les résultats</h3>

    <form action="/gestionecole/note/resultats" method="GET">

        <div>
            <label for="classe_id_res">Classe *</label><br>
            <select id="classe_id_res" name="classe_id" required>
                <option value="">-- Choisir une classe --</option>
                <?php foreach ($classes as $classe): ?>
                    <option value="<?= $classe['id'] ?>">
                        <?= htmlspecialchars($classe['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label for="composition_id_res">Composition *</label><br>
            <select id="composition_id_res" name="composition_id" required>
                <option value="">-- Choisir une composition --</option>
                <?php foreach ($compositions as $composition): ?>
                    <option value="<?= $composition['id'] ?>">
                        <?= htmlspecialchars($composition['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <button type="submit">📊 Voir les résultats</button>

    </form>

<?php endif; ?>

</body>
</html>