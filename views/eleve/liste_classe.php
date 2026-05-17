<?php
// views/eleves/liste_classe.php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des élèves — <?= htmlspecialchars($classe['nom']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; }

        .entete {
            text-align: center;
            margin-bottom: 20px;
        }

        .entete h2 { margin: 0; }
        .entete p  { margin: 4px 0; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .pied {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 10px; }
        }
    </style>
</head>
<body>

<!-- BOUTONS (cachés à l'impression) -->
<div class="no-print">
    <a href="/gestionecole/classe/index">← Retour aux classes</a>
    &nbsp;|&nbsp;
    <button onclick="window.print()">🖨 Imprimer la liste</button>
    <br><br>
</div>

<!-- EN-TÊTE -->
<div class="entete">
    <h2><?= htmlspecialchars($currentUser['ecole_nom']) ?></h2>
    <p>
        Classe : <strong><?= htmlspecialchars($classe['nom']) ?></strong>
        &nbsp;|&nbsp;
        Année scolaire : <strong><?= htmlspecialchars($anneeActive['libelle']) ?></strong>
    </p>
    <p>
        Enseignant : <strong><?= htmlspecialchars($classe['enseignant_nom'] ?? '— Non affecté') ?></strong>
    </p>
    <h3>LISTE DES ÉLÈVES</h3>
</div>

<!-- TABLEAU -->
<?php if (empty($eleves)): ?>
    <p>Aucun élève inscrit dans cette classe.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <!-- <th style="width:40px">N°</th> -->
                <th style="width:100px">Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                 <th style="width:100px"></th>
                <th style="width:120px">Date naissance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eleves as $i => $eleve): ?>
            <tr>
                <!-- <td style="text-align:center"><?= $i + 1 ?></td> -->
                <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                <td><?= htmlspecialchars($eleve['eleve_nom'] ?? $eleve['nom']) ?></td>
                <td><?= htmlspecialchars($eleve['eleve_prenom'] ?? $eleve['prenom']) ?></td>
                <td></td>
                <td>
                    <?= !empty($eleve['date_naissance'])
                        ? date('d/m/Y', strtotime($eleve['date_naissance']))
                        : '—'
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" style="text-align:right; padding-top:6px">
                    <strong>Total : <?= count($eleves) ?> élève(s)</strong>
                </td>
            </tr>
        </tfoot>
    </table>


<?php endif; ?>

<div class="pied">
    Imprimé le <?= date('d/m/Y à H:i') ?>
</div>

</body>
</html>