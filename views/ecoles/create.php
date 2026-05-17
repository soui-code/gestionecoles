<?php
// views/ecoles/create.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une école</title>
</head>
<body>

<h1>Ajouter une école</h1>

<a href="/gestionecole/ecole/index">← Retour à la liste</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="/gestionecole/ecole/create" method="POST">

    <div>
        <label for="nom">Nom de l'école *</label><br>
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
        <label for="adresse">Adresse</label><br>
        <input 
            type="text" 
            id="adresse" 
            name="adresse" 
            value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="telephone">Téléphone</label><br>
        <input 
            type="text" 
            id="telephone" 
            name="telephone" 
            value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="email">Email</label><br>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        >
    </div>

    <br>

    <button type="submit">Enregistrer</button>
    <a href="/gestionecole/ecole/index">Annuler</a>

</form>

</body>
</html>