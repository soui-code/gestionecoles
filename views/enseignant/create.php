<?php
// views/enseignants/create.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un enseignant</title>
</head>
<body>

<h1>Ajouter un enseignant / secrétaire</h1>

<a href="/gestionecole/enseignant/index">← Retour à la liste</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/enseignant/create" method="POST">

    <div>
        <label for="nom">Nom *</label><br>
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
        <label for="prenom">Prénom</label><br>
        <input
            type="text"
            id="prenom"
            name="prenom"
            value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
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
        <label for="tel">Téléphone</label><br>
        <input
            type="text"
            id="tel"
            name="tel"
            value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>"
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
            <option value="enseignant" <?= ($_POST['role'] ?? '') === 'enseignant' ? 'selected' : '' ?>>
                Enseignant
            </option>
            <option value="secretaire" <?= ($_POST['role'] ?? '') === 'secretaire' ? 'selected' : '' ?>>
                Secrétaire
            </option>
        </select>
    </div>

    <br>

    <button type="submit">Enregistrer</button>
    <a href="/gestionecole/enseignant/index">Annuler</a>

</form>

</body>
</html>