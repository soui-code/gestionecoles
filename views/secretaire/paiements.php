<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiements — Secrétariat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        <?php include __DIR__ . '/../_partials/secretaire_base.css.php'; ?>
    </style>
</head>
<body>

<?php include __DIR__ . '/../_partials/secretaire_sidebar.php'; ?>

<div class="main">
    <header class="topbar">
        <h1 class="topbar-title">Paiements</h1>
        <div class="topbar-right">
            <span style="font-size:13px;color:var(--ink-soft)"><?= htmlspecialchars($anneeActive['libelle']) ?></span>
        </div>
    </header>

    <div class="content">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($_SESSION['success']) ?> <?php unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($_SESSION['error']) ?> <?php unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- ── RECHERCHE ── -->
        <div class="card" style="padding:16px 20px; margin-bottom:20px;">
            <form method="GET" action="/gestionecole/secretaire/paiements" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                <div style="flex:1;min-width:220px;position:relative;">
                    <svg style="position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--ink-soft)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                        placeholder="Rechercher par nom, matricule, N° facture…"
                        style="width:100%;padding:9px 12px 9px 34px;border:1.5px solid var(--border);border-radius:8px;font-family:inherit;font-size:14px;outline:none;transition:border .15s"
                        onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
                </div>
                <button type="submit" class="btn btn-primary" style="padding:9px 20px">Rechercher</button>
                <?php if ($search): ?>
                    <a href="/gestionecole/secretaire/paiements" class="btn btn-outline">Effacer</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- ── TABLE ── -->
        <div class="card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
                <span style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700">
                    <?= count($paiements) ?> paiement<?= count($paiements) > 1 ? 's' : '' ?>
                    <?= $search ? ' pour "<em>' . htmlspecialchars($search) . '</em>"' : '' ?>
                </span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Élève</th>
                            <th>Classe</th>
                            <th>Date</th>
                            <th>Montant versé</th>
                            <th>Scolarité</th>
                            <th>Reste</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($paiements)): ?>
                            <tr>
                                <td colspan="9" class="empty-state">
                                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--border);margin:0 auto 8px;display:block"><path d="M9 12h6m-6 4h4m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p>Aucun paiement enregistré</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($paiements as $p): ?>
                                <tr>
                                    <td style="font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:var(--ink-soft)">
                                        <?= htmlspecialchars($p['numero_facture'] ?? '—') ?>
                                    </td>
                                    <td>
                                        <div style="font-weight:500"><?= htmlspecialchars($p['nom_eleve']) ?></div>
                                        <div style="font-size:12px;color:var(--ink-soft)"><?= htmlspecialchars($p['matricule']) ?></div>
                                    </td>
                                    <td style="font-size:13px"><?= htmlspecialchars($p['classe_nom']) ?></td>
                                    <td style="font-size:13px;color:var(--ink-soft)">
                                        <?= date('d/m/Y', strtotime($p['date_paiement'])) ?>
                                    </td>
                                    <td style="font-weight:600;color:var(--accent)">
                                        <?= number_format($p['montant'], 0, ',', ' ') ?> F
                                    </td>
                                    <td style="font-size:13px">
                                        <?= number_format($p['montant_scolarite'] ?? 0, 0, ',', ' ') ?> F
                                    </td>
                                    <td style="font-size:13px;color:<?= ($p['montant_reste'] ?? 0) > 0 ? 'var(--warn)' : 'var(--accent)' ?>; font-weight:500">
                                        <?= number_format(max(0, $p['montant_reste'] ?? 0), 0, ',', ' ') ?> F
                                    </td>
                                    <td>
                                        <?php
                                            $st = $p['statut'];
                                            $labels = ['paye' => 'Soldé', 'partiel' => 'Partiel', 'impaye' => 'Impayé'];
                                        ?>
                                        <span class="badge badge-<?= $st === 'paye' ? 'solde' : $st ?>">
                                            <?= $labels[$st] ?? $st ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="/gestionecole/secretaire/recu?paiement_id=<?= $p['paiement_id'] ?>" class="btn-icon" title="Voir / Imprimer le reçu">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 17H17.01M17 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2zM9 11h6m-6 4h4"/></svg>
                                            </a>
                                            <a href="/gestionecole/secretaire/ficheEleve?inscription_id=<?= $p['inscription_id'] ?>" class="btn-icon" title="Fiche élève">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </a>
                                            <a href="/gestionecole/secretaire/ajouterPaiement?inscription_id=<?= $p['inscription_id'] ?>" class="btn-icon" title="Ajouter un paiement" style="<?= ($p['montant_reste'] ?? 0) <= 0 ? 'opacity:.35;pointer-events:none' : '' ?>">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
</body>
</html>