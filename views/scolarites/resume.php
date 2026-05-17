<?php
// views/scolarites/resume.php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résumé financier</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        td, th { padding: 8px; border: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .paye { color: green; }
        .reste { color: red; }
        .total { font-weight: bold; background: #f9f9f9; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="no-print">
    <a href="/gestionecole/scolarite/index">← Retour</a>
    &nbsp;|&nbsp;
    <button onclick="window.print()">🖨 Imprimer</button>
    <br><br>
</div>

<h1>Résumé financier — <?= htmlspecialchars($anneeActive['libelle']) ?></h1>

<?php if (empty($resume)): ?>
    <p>Aucune donnée disponible.</p>
<?php else: ?>

    <?php
    $totalEleves   = 0;
    $totalAttendu  = 0;
    $totalPaye     = 0;
    $totalReste    = 0;
    ?>

    <table>
        <thead>
            <tr>
                <th>Classe</th>
                <th>Nb élèves</th>
                <th>Montant annuel</th>
                <th>Total attendu</th>
                <th>Total payé</th>
                <th>Reste à payer</th>
                <th>Détail</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resume as $ligne): ?>
            <?php
                $totalEleves  += $ligne['nb_eleves'];
                $totalAttendu += $ligne['total_attendu'];
                $totalPaye    += $ligne['total_paye'];
                $totalReste   += $ligne['reste_a_payer'];
            ?>
            <tr>
                <td><?= htmlspecialchars($ligne['classe_nom']) ?></td>
                <td><?= $ligne['nb_eleves'] ?></td>
                <td><?= number_format($ligne['montant_annuel'], 0, ',', ' ') ?> FCFA</td>
                <td><?= number_format($ligne['total_attendu'], 0, ',', ' ') ?> FCFA</td>
                <td class="paye"><?= number_format($ligne['total_paye'], 0, ',', ' ') ?> FCFA</td>
                <td class="<?= $ligne['reste_a_payer'] > 0 ? 'reste' : 'paye' ?>">
                    <?= number_format($ligne['reste_a_payer'], 0, ',', ' ') ?> FCFA
                </td>
                <td>
                    <a href="/gestionecole/scolarite/eleves?classe_id=<?= $ligne['classe_id'] ?? '' ?>">
                        Voir élèves
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total">
                <td>TOTAL</td>
                <td><?= $totalEleves ?></td>
                <td>—</td>
                <td><?= number_format($totalAttendu, 0, ',', ' ') ?> FCFA</td>
                <td class="paye"><?= number_format($totalPaye, 0, ',', ' ') ?> FCFA</td>
                <td class="<?= $totalReste > 0 ? 'reste' : 'paye' ?>">
                    <?= number_format($totalReste, 0, ',', ' ') ?> FCFA
                </td>
                <td>—</td>
            </tr>
        </tfoot>
    </table>

<?php endif; ?>

</body>
</html>