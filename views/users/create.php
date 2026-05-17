<?php
// views/users/create.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un utilisateur</title>
</head>
<body>

<h1>Ajouter un utilisateur</h1>

<a href="/gestionecole/user/index">← Retour à la liste</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/user/create" method="POST">

    <div>
        <label for="nom">Nom complet *</label><br>
        <input
            type="text"
            id="nom"
            name="nom"
            value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
            required
        >
    </div>

    <br>

    <div>
        <label for="email">Email *</label><br>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            required
        >
    </div>

    <br>

    <div>
        <label for="password">Mot de passe * (min. 6 caractères)</label><br>
        <input
            type="password"
            id="password"
            name="password"
            required
        >
    </div>

    <br>

    <div>
        <label for="role">Rôle *</label><br>
        <select id="role" name="role" required>
            <option value="">-- Choisir un rôle --</option>
            <?php if ($currentUser['role'] === 'super_admin'): ?>
                <option value="super_admin" <?= ($_POST['role'] ?? '') === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                <option value="admin"       <?= ($_POST['role'] ?? '') === 'admin'       ? 'selected' : '' ?>>Admin</option>
            <?php endif; ?>
            <option value="enseignant" <?= ($_POST['role'] ?? '') === 'enseignant' ? 'selected' : '' ?>>Enseignant</option>
            <option value="parent"     <?= ($_POST['role'] ?? '') === 'parent'     ? 'selected' : '' ?>>Parent</option>
        </select>
    </div>

    <br>

    <?php if ($currentUser['role'] === 'super_admin'): ?>
    <div>
        <label for="ecole_id">École</label><br>
        <select id="ecole_id" name="ecole_id">
            <option value="">-- Aucune école --</option>
            <?php foreach ($ecoles as $ecole): ?>
                <option
                    value="<?= $ecole['id'] ?>"
                    <?= ($_POST['ecole_id'] ?? '') == $ecole['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($ecole['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <br>
    <?php endif; ?>

    <button type="submit">Enregistrer</button>
    <a href="/gestionecole/user/index">Annuler</a>

</form>

</body>
</html>