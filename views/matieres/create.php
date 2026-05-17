<?php
// views/matieres/create.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une matière</title>
</head>
<body>

<h1>Ajouter une matière</h1>

<a href="/gestionecole/matiere/index">← Retour à la liste</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/matiere/create" method="POST">

    <div>
        <label for="classe_id">Classe *</label><br>
        <select id="classe_id" name="classe_id" required>
            <option value="">-- Choisir une classe --</option>
            <?php foreach ($classes as $c): ?>
                <option
                    value="<?= $c['id'] ?>"
                    <?= ($_POST['classe_id'] ?? $classe['id'] ?? '') == $c['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($c['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <div>
        <label for="nom">Nom de la matière *</label><br>
        <input
            type="text"
            id="nom"
            name="nom"
            value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
            placeholder="Ex: Lecture, Calcul, Français..."
            required
        >
    </div>

    <br>

    <div>
        <label for="note_sur">Note sur *</label><br>
        <select id="note_sur" name="note_sur" required>
            <option value="10"  <?= ($_POST['note_sur'] ?? '10') == '10'  ? 'selected' : '' ?>>Sur 10</option>
            <option value="20"  <?= ($_POST['note_sur'] ?? '') == '20'    ? 'selected' : '' ?>>Sur 20</option>
            <option value="50"  <?= ($_POST['note_sur'] ?? '') == '50'    ? 'selected' : '' ?>>Sur 50</option>
            <option value="100" <?= ($_POST['note_sur'] ?? '') == '100'   ? 'selected' : '' ?>>Sur 30</option>
</select>
    </div>

    <br>

    <button type="submit">Enregistrer</button>
    <a href="/gestionecole/matiere/index">Annuler</a>

</form>

</body>
</html>