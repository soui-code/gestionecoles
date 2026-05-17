<?php
// views/notes/resultats.php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats — <?= htmlspecialchars($classe['nom']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .entete { text-align: center; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: center; }
        th { background: #f0f0f0; }
        td:nth-child(2), td:nth-child(3) { text-align: left; }
        .rang-1 { background: #ffd700; font-weight: bold; }
        .rang-2 { background: #c0c0c0; font-weight: bold; }
        .rang-3 { background: #cd7f32; font-weight: bold; }
        .moyenne-bien { color: green; font-weight: bold; }
        .moyenne-passable { color: orange; font-weight: bold; }
        .moyenne-faible { color: red; font-weight: bold; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 10px; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <a href="/gestionecole/note/index">← Retour</a>
    &nbsp;|&nbsp;
    <button onclick="window.print()">🖨 Imprimer</button>
    <br><br>
</div>

<!-- EN-TÊTE -->
<div class="entete">
    <h2><?= htmlspecialchars($currentUser['ecole_nom']) ?></h2>
    <p>
        Classe : <strong><?= htmlspecialchars($classe['nom']) ?></strong>
        &nbsp;|&nbsp;
        <?= htmlspecialchars($composition['libelle']) ?>
        &nbsp;|&nbsp;
        Année : <strong><?= htmlspecialchars($anneeActive['libelle']) ?></strong>
    </p>
    <h3>TABLEAU DES RÉSULTATS</h3>
</div>

<?php if (empty($rangs)): ?>
    <p>Aucun résultat disponible.</p>
<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Rang</th>
                <th>Matricule</th>
                <th>Nom et prénom</th>
                <?php foreach ($matieres as $matiere): ?>
                    <th>
                        <?= htmlspecialchars($matiere['nom']) ?><br>
                    </th>
                <?php endforeach; ?>
                <th>Moyenne/20</th>
                <th class="no-print">Bulletin</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rangs as $r): ?>
            <?php
                $rangClass = '';
                if ($r['rang'] == 1) $rangClass = 'rang-1';
                if ($r['rang'] == 2) $rangClass = 'rang-2';
                if ($r['rang'] == 3) $rangClass = 'rang-3';

                $moyClass = '';
                if ($r['moyenne'] !== null) {
                    if ($r['moyenne'] >= 14)     $moyClass = 'moyenne-bien';
                    elseif ($r['moyenne'] >= 10) $moyClass = 'moyenne-passable';
                    else                         $moyClass = 'moyenne-faible';
                }
            ?>
            <tr class="<?= $rangClass ?>">
                <td><?= $r['rang'] ?></td>
                <td><?= htmlspecialchars($r['matricule']) ?></td>
                <td><?= htmlspecialchars($r['nom_eleve']) ?></td>
                <?php foreach ($matieres as $matiere): ?>
                    <td>
                        <?= isset($notesParEleve[$r['inscription_id']][$matiere['id']])
                            ? $notesParEleve[$r['inscription_id']][$matiere['id']]['note']
                            : '—'
                        ?>
                    </td>
                <?php endforeach; ?>
                <td class="<?= $moyClass ?>">
                    <?= $r['moyenne'] !== null ? $r['moyenne'] . '/20' : '—' ?>
                </td>
                <td class="no-print">
                    <a href="/gestionecole/note/bulletin?inscription_id=<?= $r['inscription_id'] ?>&composition_id=<?= $composition['id'] ?>">
                        📄 Bulletin
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <?php
                $moyClasse = array_filter(array_column($rangs, 'moyenne'));
                $moyClasse = !empty($moyClasse)
                    ? round(array_sum($moyClasse) / count($moyClasse), 2)
                    : '—';
                $colspan = count($matieres) + 1;
            ?>
            <tr>
                <td colspan="3" style="text-align:right">
                    <strong>Moyenne de la classe :</strong>
                </td>
                <td colspan="<?= $colspan ?>">
                    <strong><?= $moyClasse ?>/20</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:right">
                    <strong>Nombre d'élèves :</strong>
                </td>
                <td colspan="<?= $colspan ?>">
                    <strong><?= count($rangs) ?></strong>
                </td>
            </tr>
        </tfoot>
    </table>

<?php endif; ?>

</body>
</html>