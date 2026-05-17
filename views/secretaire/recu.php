<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu <?= htmlspecialchars($recu['numero_facture'] ?? '') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        :root{
            --ink:#0f1117;--ink-soft:#4a5060;--paper:#f7f6f2;--white:#fff;
            --accent:#1a6b4a;--accent-lt:#e6f3ee;--warn:#c0392b;
            --amber:#d4840a;--amber-lt:#fff3dc;--border:#e2e0d8;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'DM Sans',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;padding:32px}

        .screen-only{display:block}
        .print-only{display:none}

        /* ── ACTIONS BAR ── */
        .action-bar{max-width:780px;margin:0 auto 24px;display:flex;align-items:center;justify-content:space-between;gap:12px}
        .btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:500;cursor:pointer;border:none;text-decoration:none;transition:all .15s;white-space:nowrap}
        .btn-primary{background:var(--accent);color:#fff}
        .btn-primary:hover{background:#155a3d}
        .btn-outline{background:#fff;border:1.5px solid var(--border);color:var(--ink-soft)}
        .btn-outline:hover{border-color:var(--ink-soft);color:var(--ink)}
        .btn-warn{background:var(--warn);color:#fff}

        /* ── RECEIPT CARD ── */
        .receipt{max-width:780px;margin:0 auto;background:var(--white);border-radius:16px;box-shadow:0 4px 32px rgba(15,17,23,.1);overflow:hidden;border:1px solid var(--border)}

        /* header */
        .receipt-header{background:var(--ink);color:#fff;padding:32px 40px;display:flex;align-items:flex-start;justify-content:space-between;gap:24px}
        .school-info .school-name{font-family:'Syne',sans-serif;font-size:22px;font-weight:800;margin-bottom:6px}
        .school-info .school-meta{font-size:13px;opacity:.6;line-height:1.8}
        .receipt-ref{text-align:right}
        .receipt-ref .label{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;opacity:.5;margin-bottom:4px}
        .receipt-ref .numero{font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#fff}
        .receipt-ref .date-tag{display:inline-block;margin-top:8px;background:rgba(255,255,255,.12);padding:4px 10px;border-radius:6px;font-size:12px;opacity:.8}

        /* ribbon statut */
        .statut-ribbon{padding:10px 40px;font-size:13px;font-weight:600;letter-spacing:.04em;display:flex;align-items:center;gap:8px}
        .statut-ribbon.solde   {background:var(--accent-lt);color:#0e4d33;border-bottom:1px solid #b7dece}
        .statut-ribbon.partiel {background:var(--amber-lt); color:#7a4a00;border-bottom:1px solid #fada95}
        .statut-ribbon.impaye  {background:#fdecea;          color:#8b2017;border-bottom:1px solid #f5bbb7}

        /* body */
        .receipt-body{padding:32px 40px}

        .section-title{font-family:'Syne',sans-serif;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border)}

        .two-col{display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-bottom:28px}

        .info-group{margin-bottom:14px}
        .info-label{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:3px}
        .info-value{font-size:14px;font-weight:500;color:var(--ink)}

        /* montants */
        .amounts-box{background:var(--paper);border-radius:12px;padding:20px 24px;margin-bottom:28px;border:1px solid var(--border)}
        .amount-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px dashed var(--border);font-size:14px}
        .amount-row:last-child{border-bottom:none}
        .amount-row.highlight{font-family:'Syne',sans-serif;font-weight:800;font-size:18px;margin-top:4px;padding-top:14px}
        .amount-row.highlight .val-main{color:var(--accent)}
        .amount-label{color:var(--ink-soft)}
        .amount-val{font-weight:600}

        /* progress */
        .progress-section{margin-bottom:28px}
        .progress-meta{display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px}
        .progress-track{height:10px;background:var(--border);border-radius:5px;overflow:hidden}
        .progress-fill{height:100%;border-radius:5px;background:var(--accent);transition:width .5s}

        /* historique */
        .hist-table{width:100%;border-collapse:collapse;font-size:13px}
        .hist-table th{background:var(--paper);padding:9px 12px;text-align:left;font-size:11px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--ink-soft);border-bottom:1px solid var(--border)}
        .hist-table td{padding:10px 12px;border-bottom:1px solid var(--border);vertical-align:middle}
        .hist-table tr:last-child td{border-bottom:none}
        .current-row td{background:#f0faf5;font-weight:600}

        .badge{display:inline-flex;align-items:center;padding:2px 9px;border-radius:999px;font-size:11px;font-weight:600}
        .badge-solde{background:var(--accent-lt);color:#0e4d33}
        .badge-partiel{background:var(--amber-lt);color:#7a4a00}
        .badge-impaye{background:#fdecea;color:#8b2017}
        .badge-paye{background:var(--accent-lt);color:#0e4d33}

        /* footer */
        .receipt-footer{padding:20px 40px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--paper)}
        .receipt-footer .stamp{font-size:12px;color:var(--ink-soft)}
        .receipt-footer .signature-box{width:160px;height:60px;border:1.5px dashed var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--ink-soft)}

        /* ── PRINT STYLES ── */
        @media print {
            body{background:#fff;padding:0}
            .screen-only{display:none!important}
            .print-only{display:block}
            .action-bar{display:none}
            .receipt{box-shadow:none;border-radius:0;border:none;max-width:100%}
            .receipt-header{background:#0f1117!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .statut-ribbon{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .amounts-box{-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .progress-fill{-webkit-print-color-adjust:exact;print-color-adjust:exact}
        }
    </style>
</head>
<body>

<!-- ACTION BAR (screen only) -->
<div class="action-bar screen-only">
    <div style="display:flex;gap:10px">
        <a href="/gestionecole/secretaire/paiements" class="btn btn-outline">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
            Retour
        </a>
        <a href="/gestionecole/secretaire/ficheEleve?inscription_id=<?= $recu['inscription_id'] ?>" class="btn btn-outline">Fiche élève</a>
        <a href="/gestionecole/secretaire/ajouterPaiement?inscription_id=<?= $recu['inscription_id'] ?>" class="btn btn-outline">+ Paiement</a>
    </div>
    <button onclick="window.print()" class="btn btn-primary">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"/></svg>
        Imprimer le reçu
    </button>
</div>

<!-- ══ REÇU ══ -->
<div class="receipt" id="receipt">

    <!-- HEADER -->
    <div class="receipt-header">
        <div class="school-info">
            <div class="school-name"><?= htmlspecialchars($recu['ecole_nom']) ?></div>
            <div class="school-meta">
                <?php if ($recu['ecole_adresse']): ?><?= htmlspecialchars($recu['ecole_adresse']) ?><br><?php endif; ?>
                <?php if ($recu['ecole_tel']): ?>Tél : <?= htmlspecialchars($recu['ecole_tel']) ?><br><?php endif; ?>
                <?php if ($recu['ecole_email']): ?>Email : <?= htmlspecialchars($recu['ecole_email']) ?><?php endif; ?>
            </div>
        </div>
        <div class="receipt-ref">
            <div class="label">Reçu de paiement</div>
            <div class="numero"><?= htmlspecialchars($recu['numero_facture'] ?? 'N/D') ?></div>
            <div class="date-tag">
                <?= date('d/m/Y', strtotime($recu['date_paiement'])) ?>
            </div>
        </div>
    </div>

    <!-- STATUT RIBBON -->
    <?php
        $st = $recu['statut'];
        $stClass = ($st === 'paye') ? 'solde' : $st;
        $stLabel = ['paye' => '✓ Compte soldé — Scolarité intégralement payée', 'partiel' => '⏳ Paiement partiel — Un solde reste à régler', 'impaye' => '⚠ Impayé — Aucun paiement enregistré'];
    ?>
    <div class="statut-ribbon <?= $stClass ?>">
        <?= $stLabel[$st] ?? $st ?>
    </div>

    <div class="receipt-body">

        <!-- INFOS ÉLÈVE + SCOLARITÉ -->
        <div class="two-col">
            <div>
                <div class="section-title">Informations élève</div>
                <div class="info-group">
                    <div class="info-label">Nom complet</div>
                    <div class="info-value" style="font-size:16px;font-family:'Syne',sans-serif;font-weight:700"><?= htmlspecialchars($recu['nom_eleve'] ?? $recu['eleve_nom'] ?? '') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Matricule</div>
                    <div class="info-value"><?= htmlspecialchars($recu['matricule']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Classe</div>
                    <div class="info-value"><?= htmlspecialchars($recu['classe_nom'] ?? '') ?></div>
                </div>
                <?php if ($recu['date_naissance']): ?>
                <div class="info-group">
                    <div class="info-label">Né(e) le</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($recu['date_naissance'])) ?> <?= $recu['lieu_naissance'] ? '— ' . htmlspecialchars($recu['lieu_naissance']) : '' ?></div>
                </div>
                <?php endif; ?>
            </div>
            <div>
                <div class="section-title">Scolarité <?= htmlspecialchars($recu['annee_libelle_raw'] ?? '') ?></div>
                <div class="info-group">
                    <div class="info-label">Année scolaire</div>
                    <div class="info-value"><?= htmlspecialchars($recu['annee_libelle_raw'] ?? '') ?></div>
                </div>
                <?php if ($recu['nom_tuteur'] || $recu['nom_pere']): ?>
                <div class="info-group">
                    <div class="info-label">Parent / Tuteur</div>
                    <div class="info-value"><?= htmlspecialchars($recu['nom_tuteur'] ?: $recu['nom_pere']) ?></div>
                </div>
                <?php endif; ?>
                <?php if ($recu['tel_tuteur']): ?>
                <div class="info-group">
                    <div class="info-label">Téléphone</div>
                    <div class="info-value"><?= htmlspecialchars($recu['tel_tuteur']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- MONTANTS -->
        <div class="section-title">Détail du paiement</div>
        <div class="amounts-box">
            <div class="amount-row">
                <span class="amount-label">Scolarité annuelle totale</span>
                <span class="amount-val"><?= number_format($recu['montant_scolarite'] ?? 0, 0, ',', ' ') ?> FCFA</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">Montant de ce versement</span>
                <span class="amount-val" style="color:var(--accent)"><?= number_format($recu['montant_verse'], 0, ',', ' ') ?> FCFA</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">Total cumulé payé</span>
                <span class="amount-val"><?= number_format($recu['montant_paye'] ?? $recu['montant_verse'], 0, ',', ' ') ?> FCFA</span>
            </div>
            <div class="amount-row highlight">
                <span class="amount-label" style="font-size:16px">Reste à payer</span>
                <span class="val-main"><?= number_format(max(0, $recu['montant_reste'] ?? 0), 0, ',', ' ') ?> FCFA</span>
            </div>
        </div>

        <!-- PROGRESSION -->
        <?php
            $scol = $recu['montant_scolarite'] ?? 0;
            $paye = $recu['montant_paye'] ?? $recu['montant_verse'];
            $pct  = $scol > 0 ? min(100, round($paye / $scol * 100)) : 0;
        ?>
        <div class="progress-section">
            <div class="progress-meta">
                <span style="color:var(--ink-soft)">Avancement de la scolarité</span>
                <strong><?= $pct ?>%</strong>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $pct>=100?'var(--accent)':($pct>=50?'var(--amber)':'var(--warn)') ?>"></div>
            </div>
        </div>

        <!-- HISTORIQUE -->
        <?php if (!empty($historique)): ?>
        <div class="section-title">Historique des versements</div>
        <table class="hist-table">
            <thead>
                <tr>
                    <th>N° Reçu</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historique as $h): ?>
                    <tr class="<?= $h['id'] == $recu['paiement_id'] ? 'current-row' : '' ?>">
                        <td style="font-size:12px;color:var(--ink-soft)"><?= htmlspecialchars($h['numero_facture'] ?? '—') ?></td>
                        <td><?= date('d/m/Y', strtotime($h['date_paiement'])) ?></td>
                        <td style="font-weight:600"><?= number_format($h['montant'], 0, ',', ' ') ?> F</td>
                        <td>
                            <?php $hs = $h['statut']; ?>
                            <span class="badge badge-<?= $hs === 'paye' ? 'solde' : $hs ?>">
                                <?= ['paye'=>'Soldé','partiel'=>'Partiel','impaye'=>'Impayé'][$hs] ?? $hs ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

    </div><!-- /body -->

    <!-- FOOTER -->
    <div class="receipt-footer">
        <div class="stamp">
            Émis le <?= date('d/m/Y à H:i') ?><br>
            <span style="font-size:11px">Ce document tient lieu de reçu de paiement officiel.</span>
        </div>
        <div class="signature-box">Signature &amp; Cachet</div>
    </div>

</div><!-- /receipt -->

<script>
// Auto-print si paramètre print=1 dans l'URL
const params = new URLSearchParams(window.location.search);
if (params.get('print') === '1') {
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
}
</script>
</body>
</html>