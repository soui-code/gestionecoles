<?php
// views/matieres/edit.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une matière</title>
</head>
<body>

<h1>Modifier une matière</h1>

<a href="/gestionecole/matiere/classe?classe_id=<?= $matiere['classe_id'] ?>">← Retour</a>

<br><br>

<p>Classe : <strong><?= htmlspecialchars($matiere['classe_nom']) ?></strong></p>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/matiere/edit?id=<?= $matiere['id'] ?>" method="POST">

    <div>
        <label for="nom">Nom de la matière *</label><br>
        <input
            type="text"
            id="nom"
            name="nom"
            value="<?= htmlspecialchars($_POST['nom'] ?? $matiere['nom']) ?>"
            required
        >
    </div>

    <br>

    <div>
        <label for="note_sur">Note sur *</label><br>
        <select id="note_sur" name="note_sur" required>
            <option value="10"  <?= ($_POST['note_sur'] ?? $matiere['note_sur']) == '10'  ? 'selected' : '' ?>>Sur 10</option>
            <option value="20"  <?= ($_POST['note_sur'] ?? $matiere['note_sur']) == '20'  ? 'selected' : '' ?>>Sur 20</option>
            <option value="50"  <?= ($_POST['note_sur'] ?? $matiere['note_sur']) == '50'  ? 'selected' : '' ?>>Sur 50</option>
            <option value="5"   <?= ($_POST['note_sur'] ?? $matiere['note_sur']) == '5'   ? 'selected' : '' ?>>Sur 5</option>
            <option value="100" <?= ($_POST['note_sur'] ?? $matiere['note_sur']) == '100' ? 'selected' : '' ?>>Sur 100</option>
        </select>
    </div>

    <br>

    <button type="submit">Enregistrer les modifications</button>
    <a href="/gestionecole/matiere/classe?classe_id=<?= $matiere['classe_id'] ?>">Annuler</a>

</form>>

</body>
</html>