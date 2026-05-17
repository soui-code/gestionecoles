<?php
// views/ecoles/edit.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une école</title>
</head>
<body>

<h1>Modifier une école</h1>

<a href="/gestionecole/ecole/index">← Retour à la liste</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/ecole/edit?id=<?= $ecole['id'] ?>" method="POST">

    <div>
        <label for="nom">Nom de l'école *</label><br>
        <input 
            type="text" 
            id="nom" 
            name="nom" 
            value="<?= htmlspecialchars($_POST['nom'] ?? $ecole['nom']) ?>"
            required
        >
    </div>

    <br>

    <div>
        <label for="adresse">Adresse</label><br>
        <input 
            type="text" 
            id="adresse" 
            name="adresse" 
            value="<?= htmlspecialchars($_POST['adresse'] ?? $ecole['adresse']) ?>"
        >
    </div>

    <br>

    <div>
        <label for="telephone">Téléphone</label><br>
        <input 
            type="text" 
            id="telephone" 
            name="telephone" 
            value="<?= htmlspecialchars($_POST['telephone'] ?? $ecole['telephone']) ?>"
        >
    </div>

    <br>

    <div>
        <label for="email">Email</label><br>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="<?= htmlspecialchars($_POST['email'] ?? $ecole['email']) ?>"
        >
    </div>

    <br>

    <button type="submit">Enregistrer les modifications</button>
    <a href="/gestionecole/ecole/index">Annuler</a>

</form>

</body>
</html>