<?php
// views/notes/bulletin.php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin — <?= htmlspecialchars($inscription['eleve_nom']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 20px;
        }

        .bulletin {
            border: 2px solid #333;
            padding: 20px;
            max-width: 700px;
            margin: 0 auto;
        }

        .entete {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .entete h2 { margin: 0; font-size: 18px; }
        .entete h3 { margin: 5px 0; font-size: 14px; }
        .entete p  { margin: 3px 0; font-size: 12px; }

        .infos-eleve {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .infos-eleve div { width: 48%; }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: center;
        }

        th { background: #f0f0f0; font-size: 12px; }

        td:first-child { text-align: left; }

        .moyenne-bien     { color: green;  font-weight: bold; }
        .moyenne-passable { color: orange; font-weight: bold; }
        .moyenne-faible   { color: red;    font-weight: bold; }

        .total-row {
            background: #f9f9f9;
            font-weight: bold;
        }

        .resultats {
            display: flex;
            justify-content: space-between;
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 15px;
        }

        .resultats div { text-align: center; }
        .resultats .label { font-size: 11px; color: #666; }
        .resultats .valeur { font-size: 18px; font-weight: bold; }

        .appreciation {
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 15px;
            min-height: 60px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signatures div { text-align: center; width: 30%; }

        .pied {
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            margin-top: 15px;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 5px; }
            .bulletin { border: 2px solid #000; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <a href="/gestionecole/note/resultats?classe_id=<?= $inscription['classe_id'] ?>&composition_id=<?= $composition['id'] ?>">
        ← Retour aux résultats
    </a>
    &nbsp;|&nbsp;
    <button onclick="window.print()">🖨 Imprimer le bulletin</button>
    <br><br>
</div>

<div class="bulletin">

    <!-- EN-TÊTE -->
    <div class="entete">
        <h2><?= htmlspecialchars($currentUser['ecole_nom']) ?></h2>
        <h3>BULLETIN DE NOTES</h3>
        <p>
            <?= htmlspecialchars($composition['libelle']) ?>
            &nbsp;|&nbsp;
            Année scolaire : <?= htmlspecialchars($inscription['annee_libelle']) ?>
        </p>
        <p>
            Du <?= date('d/m/Y', strtotime($composition['date_debut'])) ?>
            au <?= date('d/m/Y', strtotime($composition['date_fin'])) ?>
        </p>
    </div>

    <!-- INFOS ÉLÈVE -->
    <div class="infos-eleve">
        <div>
            <p><strong>Nom :</strong> <?= htmlspecialchars($inscription['eleve_nom']) ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($inscription['eleve_prenom']) ?></p>
            <p><strong>Matricule :</strong> <?= htmlspecialchars($inscription['matricule']) ?></p>
        </div>
        <div>
            <p><strong>Classe :</strong> <?= htmlspecialchars($inscription['classe_nom']) ?></p>
            <p><strong>Date naissance :</strong>
                <?= $inscription['date_naissance']
                    ? date('d/m/Y', strtotime($inscription['date_naissance']))
                    : '—'
                ?>
            </p>
            <p><strong>Effectif :</strong> <?= $totalEleves ?> élève(s)</p>
        </div>
    </div>

    
<!-- TABLEAU DES NOTES -->
<table>
    <thead>
        <tr>
            <th>Matière</th>
            <th>Note obtenue</th>
            <th>Sur</th>
            <th>Note /20</th>
            <th>Moy. classe</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sommeNotes = 0;
        $nbMatieres = 0;
        foreach ($matieres as $matiere):
            $noteData = null;
            foreach ($bulletin as $b) {
                if ($b['matiere_nom'] === $matiere['nom']) {
                    $noteData = $b;
                    break;
                }
            }

            $note      = $noteData['note'] ?? null;
            $noteSur20 = $note !== null
                ? round($note * (20 / $matiere['note_sur']), 2)
                : null;

            if ($noteSur20 !== null) {
                $sommeNotes += $noteSur20;
                $nbMatieres++;
            }
        ?>
        <tr>
            <td><?= htmlspecialchars($matiere['nom']) ?></td>
            <td>
                <?= $note !== null
                    ? $note
                    : '<span style="color:gray">—x</span>'
                ?>
            </td>
            <td><?= $matiere['note_sur'] ?></td>
            <td>
                <?= $noteSur20 !== null
                    ? $noteSur20
                    : '<span style="color:gray">—v</span>'
                ?>
            </td>
            <td>
                <?= isset($noteData['moyenne_classe'])
                    ? $noteData['moyenne_classe']
                    : '—'
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" style="text-align:right">Moyenne :</td>
            <td colspan="2">
                <?= $nbMatieres > 0
                    ? round($sommeNotes / $nbMatieres, 2) . '/20'
                    : '—'
                ?>
            </td>
        </tr>
    </tfoot>
</table>

    <!-- RÉSULTATS -->
    <div class="resultats">
        <div>
            <div class="label">Moyenne obtenue</div>
            <div class="valeur <?=
                $moyenne >= 14 ? 'moyenne-bien' :
                ($moyenne >= 10 ? 'moyenne-passable' : 'moyenne-faible')
            ?>">
                <?= $moyenne !== null ? $moyenne . '/20' : '—' ?>
            </div>
        </div>
        <div>
            <div class="label">Rang</div>
            <div class="valeur"><?= $rang ?> / <?= $totalEleves ?></div>
        </div>
        <div>
            <div class="label">Mention</div>
            <div class="valeur">
                <?php
                if ($moyenne === null)    echo '—';
                elseif ($moyenne >= 16)   echo 'Très bien';
                elseif ($moyenne >= 14)   echo 'Bien';
                elseif ($moyenne >= 12)   echo 'Assez bien';
                elseif ($moyenne >= 10)   echo 'Passable';
                else                      echo 'Insuffisant';
                ?>
            </div>
        </div>
        <div>
            <div class="label">Décision</div>
            <div class="valeur">
                <?= $moyenne !== null && $moyenne >= 10
                    ? '<span style="color:green">Admis</span>'
                    : '<span style="color:red">Ajourné</span>'
                ?>
            </div>
        </div>
    </div>

    <!-- APPRÉCIATION -->
    <div class="appreciation">
        <strong>Appréciation du directeur :</strong>
        <br><br>
        &nbsp;
    </div>

    <!-- SIGNATURES -->
    <div class="signatures">
        <div>
            <p>Le Directeur</p>
            <br><br><br>
            <p>________________________</p>
        </div>
        <div>
            <p>L'Enseignant</p>
            <br><br><br>
            <p>________________________</p>
        </div>
        <div>
            <p>Parent / Tuteur</p>
            <br><br><br>
            <p>________________________</p>
        </div>
    </div>

    <!-- PIED DE PAGE -->
    <div class="pied">
        Bulletin édité le <?= date('d/m/Y à H:i') ?>
    </div>

</div>

</body>
</html>