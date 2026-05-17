<?php
// views/inscriptions/choix.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription / Réinscription</title>
</head>
<body>

<h1>Inscription des élèves</h1>

<a href="/gestionecole/inscription/index">← Retour à la liste</a>

<br><br>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<h2>Que souhaitez-vous faire ?</h2>

<table cellpadding="20">
    <tr>
        <td style="border:1px solid #ccc; text-align:center; width:250px">
            <h3>📝 Nouvelle inscription</h3>
            <p>Pour un élève qui s'inscrit pour la première fois dans cette école.</p>
            <br>
            <a href="/gestionecole/inscription/create">
                <button>Nouvelle inscription</button>
            </a>
        </td>
        <td width="50"></td>
        <td style="border:1px solid #ccc; text-align:center; width:250px">
            <h3>🔄 Réinscription</h3>
            <p>Pour un élève déjà enregistré qui passe dans une nouvelle année scolaire.</p>
            <br>
            <a href="/gestionecole/inscription/reinscription">
                <button>Réinscription</button>
            </a>
        </td>
    </tr>
</table>

</body>
</html>