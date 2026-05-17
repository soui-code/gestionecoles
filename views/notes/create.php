<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>creer un note</h1>
    <form action="" method="post">
         <label for="">matier</label>
         <select name="matiereId" id="">
                <?php foreach ($matieres as $matiere): ?>
                    <option value="<?= $matiere['id'] ?>"><?= htmlspecialchars($matiere['nom']) ?></option>
                <?php endforeach; ?>
         </select>
            <label for="">composition</label>
            <select name="compositionId" id="">
                <?php foreach ($composition as $comp): ?>
                    <option value="<?= $comp['id'] ?>"><?= htmlspecialchars($comp['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
            <table>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Note </th>
                </tr>
                <?php foreach ($eleves as $eleve): ?>
                <tr>
                    <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                    <td><?= htmlspecialchars($eleve['nom']) ?></td>
                    <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                    <td><input type="text" name="note[<?= $eleve['inscription_id'] ?>]" /></td>
            </table>
            <?php endforeach; ?>
            <button type="submit">Enregistrer les notes</button>
    </form>
</body>
</html>