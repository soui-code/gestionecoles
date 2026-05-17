<?php
// views/scolarites/eleves.php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Élèves — Statut paiement</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        td, th { padding: 8px; border: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .paye { color: green; font-weight: bold; }
        .partiel { color: orange; font-weight: bold; }
        .impaye { color: red; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="no-print">
    <a href="/gestionecole/scolarite/resume">← Retour au résumé</a>
    &nbsp;|&nbsp;
    <button onclick="window.print()">🖨 Imprimer</button>
    <br><br>
</div>

<h1>
    Classe : <?= htmlspecialchars($classe['nom']) ?>
    — <?= htmlspecialchars($anneeActive['libelle']) ?>
</h1>

<?php if (empty($eleves)): ?>
    <p>Aucun élève inscrit dans cette classe.</p>
<?php else: ?>

    <?php
    $totalPaye  = 0;
    $totalReste = 0;
    $nbPaye     = 0;
    $nbPartiel  = 0;
    $nbImpaye   = 0;
    ?>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Matricule</th>
                <th>Nom et prénom</th>
                <th>Téléphone tuteur</th>
                <th>Scolarité</th>
                <th>Total payé</th>
                <th>Reste</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eleves as $i => $eleve): ?>
            <?php
                $totalPaye  += $eleve['total_paye'];
                $totalReste += $eleve['reste'];
                if ($eleve['statut'] === 'paye')    $nbPaye++;
                if ($eleve['statut'] === 'partiel') $nbPartiel++;
                if ($eleve['statut'] === 'impaye')  $nbImpaye++;
            ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                <td><?= htmlspecialchars($eleve['nom_eleve']) ?></td>
                <td><?= htmlspecialchars($eleve['tel_tuteur'] ?? '—') ?></td>
                <td><?= number_format($eleve['montant_annuel'], 0, ',', ' ') ?> FCFA</td>
                <td class="paye"><?= number_format($eleve['total_paye'], 0, ',', ' ') ?> FCFA</td>
                <td class="<?= $eleve['reste'] > 0 ? 'impaye' : 'paye' ?>">
                    <?= number_format($eleve['reste'], 0, ',', ' ') ?> FCFA
                </td>
                <td>
                    <?php if ($eleve['statut'] === 'paye'): ?>
                        <span class="paye">✔ Payé</span>
                    <?php elseif ($eleve['statut'] === 'partiel'): ?>
                        <span class="partiel">⚡ Partiel</span>
                    <?php else: ?>
                        <span class="impaye">✖ Impayé</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/gestionecole/inscription/detail?id=<?= $eleve['inscription_id'] ?>">
                        Voir fiche
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#f9f9f9">
                <td colspan="5">TOTAL (<?= count($eleves) ?> élèves)</td>
                <td class="paye"><?= number_format($totalPaye, 0, ',', ' ') ?> FCFA</td>
                <td class="impaye"><?= number_format($totalReste, 0, ',', ' ') ?> FCFA</td>
                <td colspan="2">
                    <span class="paye">✔ <?= $nbPaye ?></span> |
                    <span class="partiel">⚡ <?= $nbPartiel ?></span> |
                    <span class="impaye">✖ <?= $nbImpaye ?></span>
                </td>
            </tr>
        </tfoot>
    </table>

<?php endif; ?>

</body>
</html>