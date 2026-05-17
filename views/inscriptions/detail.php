<?php
// views/inscriptions/detail.php
$currentUser = Auth::user();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche élève</title>
    <style>
        table { border-collapse: collapse; width: 100%; max-width: 600px; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        td:first-child { font-weight: bold; width: 40%; color: #555; }
        h3 { background: #f5f5f5; padding: 8px; margin-top: 20px; }
        .paye { color: green; }
        .reste { color: red; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="no-print">
    <a href="/gestionecole/inscription/index">← Retour à la liste</a>
    &nbsp;|&nbsp;
    <button onclick="window.print()">🖨 Imprimer</button>
    <?php if (in_array($currentUser['role'], ['super_admin', 'admin', 'secretaire'])): ?>
    &nbsp;|&nbsp;
    <a href="/gestionecole/eleve/edit?id=<?= $inscription['eleve_id'] ?>">✏ Modifier infos élève</a>
    <?php endif; ?>
    <br><br>
</div>

<!-- FICHE ÉLÈVE -->
<h2>Fiche de l'élève</h2>

<h3>Scolarité</h3>
<table>
    <tr><td>Matricule</td><td><?= htmlspecialchars($inscription['matricule']) ?></td></tr>
    <tr><td>Année scolaire</td><td><?= htmlspecialchars($inscription['annee_libelle']) ?></td></tr>
    <tr><td>Classe</td><td><?= htmlspecialchars($inscription['classe_nom']) ?></td></tr>
    <tr><td>École</td><td><?= htmlspecialchars($inscription['ecole_nom']) ?></td></tr>
    <tr><td>Date inscription</td><td><?= date('d/m/Y', strtotime($inscription['created_at'])) ?></td></tr>
</table>

<h3>Informations personnelles</h3>
<table>
    <tr><td>Nom</td><td><?= htmlspecialchars($inscription['eleve_nom']) ?></td></tr>
    <tr><td>Prénom</td><td><?= htmlspecialchars($inscription['eleve_prenom']) ?></td></tr>
    <tr><td>Date de naissance</td><td><?= $inscription['date_naissance'] ? date('d/m/Y', strtotime($inscription['date_naissance'])) : '—' ?></td></tr>
    <tr><td>Lieu de naissance</td><td><?= htmlspecialchars($inscription['lieu_naissance'] ?? '—') ?></td></tr>
    <tr><td>Domicile</td><td><?= htmlspecialchars($inscription['domicile'] ?? '—') ?></td></tr>
</table>

<h3>Informations des parents</h3>
<table>
    <tr><td>Nom du père</td><td><?= htmlspecialchars($inscription['nom_pere'] ?? '—') ?></td></tr>
    <tr><td>Téléphone père</td><td><?= htmlspecialchars($inscription['tel_pere'] ?? '—') ?></td></tr>
    <tr><td>Profession père</td><td><?= htmlspecialchars($inscription['profession_pere'] ?? '—') ?></td></tr>
    <tr><td>Nom de la mère</td><td><?= htmlspecialchars($inscription['nom_mere'] ?? '—') ?></td></tr>
    <tr><td>Téléphone mère</td><td><?= htmlspecialchars($inscription['tel_mere'] ?? '—') ?></td></tr>
    <tr><td>Nom du tuteur</td><td><?= htmlspecialchars($inscription['nom_tuteur'] ?? '—') ?></td></tr>
    <tr><td>Téléphone tuteur</td><td><?= htmlspecialchars($inscription['tel_tuteur'] ?? '—') ?></td></tr>
</table>

<h3>Paiements</h3>
<table>
    <tr>
        <td>Scolarité annuelle</td>
        <td><?= number_format($montantScolarite, 0, ',', ' ') ?> FCFA</td>
    </tr>
    <tr class="paye">
        <td>Total payé</td>
        <td><?= number_format($totalPaye, 0, ',', ' ') ?> FCFA</td>
    </tr>
    <tr class="<?= $resteAPayer > 0 ? 'reste' : 'paye' ?>">
        <td>Reste à payer</td>
        <td><?= number_format($resteAPayer, 0, ',', ' ') ?> FCFA</td>
    </tr>
</table>

<?php if (!empty($paiements)): ?>
<h3>Historique des paiements</h3>
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Date</th>
            <th>Montant</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($paiements as $paiement): ?>
        <tr>
            <td><?= date('d/m/Y H:i', strtotime($paiement['date_paiement'])) ?></td>
            <td><?= number_format($paiement['montant'], 0, ',', ' ') ?> FCFA</td>
            <td>
                <?php if ($paiement['statut'] === 'paye'): ?>
                    <span class="paye">✔ Payé</span>
                <?php elseif ($paiement['statut'] === 'partiel'): ?>
                    <span style="color:orange">⚡ Partiel</span>
                <?php else: ?>
                    <span class="reste">✖ Impayé</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>