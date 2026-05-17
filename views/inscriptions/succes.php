<?php
// views/inscriptions/succes.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription réussie</title>
    <style>
        .facture {
            border: 2px solid #333;
            padding: 30px;
            max-width: 500px;
            margin: 20px auto;
            font-family: monospace;
        }
        .facture h2 {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .facture table {
            width: 100%;
        }
        .facture td {
            padding: 6px 0;
        }
        .facture td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .facture .total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
        }
        .facture .reste {
            color: red;
        }
        .facture .paye {
            color: green;
        }
        @media print {
            .no-print {
                display: none;
            }
            .facture {
                border: 2px solid #000;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <p style="color:green; font-size:18px">
        ✔ Inscription enregistrée avec succès !
    </p>

    <a href="/gestionecole/inscription/index">← Retour à la liste</a>
    &nbsp;|&nbsp;
    <a href="/gestionecole/inscription/choix">+ Nouvelle inscription</a>
    &nbsp;|&nbsp;
    <button onclick="window.print()">🖨 Imprimer la facture</button>

    <br><br>
</div>

<!-- FACTURE -->
<div class="facture">

    <h2>REÇU DE PAIEMENT</h2>

    <p style="text-align:center">
        <strong>N° <?= htmlspecialchars($succes['numero_facture']) ?></strong>
    </p>

    <br>

    <table>
        <tr>
            <td>Date</td>
            <td><?= date('d/m/Y H:i', strtotime($succes['date_paiement'])) ?></td>
        </tr>
        <tr>
            <td>Année scolaire</td>
            <td><?= htmlspecialchars($succes['annee_libelle']) ?></td>
        </tr>
    </table>

    <br>

    <table>
        <tr>
            <td>Élève</td>
            <td><?= htmlspecialchars($succes['eleve_nom']) ?></td>
        </tr>
        <tr>
            <td>Classe</td>
            <td><?= htmlspecialchars($succes['classe_nom']) ?></td>
        </tr>
    </table>

    <br>

    <table>
        <tr>
            <td>Scolarité annuelle</td>
            <td><?= number_format($succes['montant_scolarite'], 0, ',', ' ') ?> FCFA</td>
        </tr>
        <tr class="paye">
            <td>Montant payé</td>
            <td><?= number_format($succes['montant_paye'], 0, ',', ' ') ?> FCFA</td>
        </tr>
        <tr class="total <?= $succes['montant_reste'] > 0 ? 'reste' : 'paye' ?>">
            <td>Reste à payer</td>
            <td><?= number_format($succes['montant_reste'], 0, ',', ' ') ?> FCFA</td>
        </tr>
    </table>

    <br>

    <table>
        <tr>
            <td>Statut</td>
            <td>
                <?php if ($succes['statut'] === 'paye'): ?>
                    <span class="paye">✔ Payé intégralement</span>
                <?php elseif ($succes['statut'] === 'partiel'): ?>
                    <span style="color:orange">⚡ Paiement partiel</span>
                <?php else: ?>
                    <span class="reste">✖ Impayé</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <br>

    <p style="text-align:center; font-size:12px; border-top:1px solid #333; padding-top:10px">
        Ce reçu est valable comme preuve de paiement.<br>
        Merci de votre confiance.
    </p>

</div>

</body>
</html>