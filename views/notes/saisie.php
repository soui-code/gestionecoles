<?php
// views/notes/saisie.php
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Saisie des notes</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background: #f5f5f5; }
        td:nth-child(2) { text-align: left; }
        input[type="number"] { width: 60px; text-align: center; }
        .absent { background: #fff3cd; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="no-print">
    <a href="/gestionecole/note/index">← Retour</a>
    <br><br>
</div>

<h1>Saisie des notes</h1>

<p>
    Classe : <strong><?= htmlspecialchars($classe['nom']) ?></strong>
    &nbsp;|&nbsp;
    Composition : <strong><?= htmlspecialchars($composition['libelle']) ?></strong>
    &nbsp;|&nbsp;
    Du : <strong><?= date('d/m/Y', strtotime($composition['date_debut'])) ?></strong>
    au : <strong><?= date('d/m/Y', strtotime($composition['date_fin'])) ?></strong>
</p>

<?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red"><?= nl2br(htmlspecialchars($error)) ?></p>
<?php endif; ?>

<?php if (empty($inscriptions)): ?>
    <p style="color:orange">⚠ Aucun élève inscrit dans cette classe.</p>
<?php elseif (empty($matieres)): ?>
    <p style="color:orange">⚠ Aucune matière définie pour cette école.</p>
<?php else: ?>

<form action="/gestionecole/note/saisie?classe_id=<?= $classe['id'] ?>&composition_id=<?= $composition['id'] ?>" method="POST">

    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>matricule</th>
                <th>Élève</th>
                <?php foreach ($matieres as $matiere): ?>
                    <th>
                        <?= htmlspecialchars($matiere['nom']) ?><br>
                       
                    </th>
                <?php endforeach; ?>
                <th class="no-print">Moyenne</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscriptions as $i => $inscription): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($inscription['matricule']) ?></td>
                <td style="text-align:left">
                    <?= htmlspecialchars($inscription['eleve_nom']) ?>
                    <?= htmlspecialchars($inscription['eleve_prenom']) ?>
                    <br>
                </td>
                <?php foreach ($matieres as $matiere): ?>
                    <td>
                        <input
                            type="number"
                            name="notes[<?= $inscription['id'] ?>][<?= $matiere['id'] ?>]"
                            value="<?= $notesExistantes[$inscription['id']][$matiere['id']] ?? '' ?>"
                            min="0"
                            max="<?= $matiere['note_sur'] ?>"
                            step="0.25"
                            placeholder="—"
                        >
                    </td>
                <?php endforeach; ?>
                <td class="no-print" id="moy_<?= $inscription['id'] ?>">—</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>

    <div class="no-print">
        <button type="submit">💾 Enregistrer les notes</button>
        &nbsp;
        <a href="/gestionecole/note/resultats?classe_id=<?= $classe['id'] ?>&composition_id=<?= $composition['id'] ?>">
            📊 Voir les résultats
        </a>
    </div>

</form>

<!-- CALCUL MOYENNE EN TEMPS RÉEL -->
<script>
    const matieres = <?= json_encode(array_map(function($m) {
        return [
            'id'          => $m['id'],
            
            'note_sur'    => (float)$m['note_sur'],
        ];
    }, $matieres)) ?>;

    function calculerMoyenne(inscriptionId) {
        let sommePonderee = 0;
        let sommeCoef     = 0;

        matieres.forEach(m => {
            const input = document.querySelector(
                `input[name="notes[${inscriptionId}][${m.id}]"]`
            );
            if (input && input.value !== '') {
                const note = parseFloat(input.value);
                sommePonderee += note * (20 / m.note_sur);
                
            }
        });

        const moyEl = document.getElementById(`moy_${inscriptionId}`);
        if (moyEl) {
            moyEl.textContent = sommeCoef > 0
                ? (sommePonderee / sommeCoef).toFixed(2) + '/20'
                : '—';
        }
    }

    // Écouter les changements sur tous les inputs
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            const match = this.name.match(/notes\[(\d+)\]/);
            if (match) calculerMoyenne(match[1]);
        });
    });

    // Calculer les moyennes au chargement
    <?php foreach ($inscriptions as $inscription): ?>
        calculerMoyenne(<?= $inscription['id'] ?>);
    <?php endforeach; ?>
</script>

<?php endif; ?>

</body>
</html>