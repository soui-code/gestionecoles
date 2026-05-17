<?php
// views/inscriptions/create.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle inscription</title>
</head>
<body>

<h1>Nouvelle inscription</h1>

<a href="/gestionecole/inscription/choix">← Retour au choix</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p>Année scolaire : <strong><?= htmlspecialchars($anneeActive['libelle']) ?></strong></p>

<form action="/gestionecole/inscription/create" method="POST">

    <h3>Informations de l'élève</h3>

    <div>
        <label for="matricule">Matricule *</label><br>
        <input
            type="text"
            id="matricule"
            name="matricule"
            value="<?= htmlspecialchars($_POST['matricule'] ?? '') ?>"
            required
        >
    </div>

    <br>

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
        <label for="prenom">Prénom *</label><br>
        <input
            type="text"
            id="prenom"
            name="prenom"
            value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
            required
        >
    </div>

    <br>

    <div>
        <label for="date_naissance">Date de naissance</label><br>
        <input
            type="date"
            id="date_naissance"
            name="date_naissance"
            value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="lieu_naissance">Lieu de naissance</label><br>
        <input
            type="text"
            id="lieu_naissance"
            name="lieu_naissance"
            value="<?= htmlspecialchars($_POST['lieu_naissance'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="domicile">Domicile</label><br>
        <input
            type="text"
            id="domicile"
            name="domicile"
            value="<?= htmlspecialchars($_POST['domicile'] ?? '') ?>"
        >
    </div>

    <br>

    <h3>Informations du tuteur</h3>

    <div>
        <label for="nom_tuteur">Nom du tuteur</label><br>
        <input
            type="text"
            id="nom_tuteur"
            name="nom_tuteur"
            value="<?= htmlspecialchars($_POST['nom_tuteur'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="tel_tuteur">Téléphone tuteur</label><br>
        <input
            type="text"
            id="tel_tuteur"
            name="tel_tuteur"
            value="<?= htmlspecialchars($_POST['tel_tuteur'] ?? '') ?>"
        >
    </div>

    <br>

    <h3>Informations des parents</h3>

    <div>
        <label for="nom_pere">Nom du père</label><br>
        <input
            type="text"
            id="nom_pere"
            name="nom_pere"
            value="<?= htmlspecialchars($_POST['nom_pere'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="tel_pere">Téléphone père</label><br>
        <input
            type="text"
            id="tel_pere"
            name="tel_pere"
            value="<?= htmlspecialchars($_POST['tel_pere'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="profession_pere">Profession du père</label><br>
        <input
            type="text"
            id="profession_pere"
            name="profession_pere"
            value="<?= htmlspecialchars($_POST['profession_pere'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="nom_mere">Nom de la mère</label><br>
        <input
            type="text"
            id="nom_mere"
            name="nom_mere"
            value="<?= htmlspecialchars($_POST['nom_mere'] ?? '') ?>"
        >
    </div>

    <br>

    <div>
        <label for="tel_mere">Téléphone mère</label><br>
        <input
            type="text"
            id="tel_mere"
            name="tel_mere"
            value="<?= htmlspecialchars($_POST['tel_mere'] ?? '') ?>"
        >
    </div>

    <br>

    <h3>Affectation</h3>

    <div>
        <label for="classe_id">Classe *</label><br>
        <select id="classe_id" name="classe_id" required>
            <option value="">-- Choisir une classe --</option>
            <?php foreach ($classes as $classe): ?>
                <option
                    value="<?= $classe['id'] ?>"
                    <?= ($_POST['classe_id'] ?? '') == $classe['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($classe['nom']) ?>
                    <?= $classe['enseignant_nom'] ? '— ' . htmlspecialchars($classe['enseignant_nom']) : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br><br>

    <button type="submit">Suivant → Paiement</button>
    <a href="/gestionecole/inscription/choix">Annuler</a>

</form>

</body>
</html>