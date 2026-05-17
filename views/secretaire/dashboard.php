<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Secrétariat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink:       #0f1117;
            --ink-soft:  #4a5060;
            --paper:     #f7f6f2;
            --white:     #ffffff;
            --accent:    #1a6b4a;
            --accent-lt: #e6f3ee;
            --warn:      #c0392b;
            --warn-lt:   #fdecea;
            --amber:     #d4840a;
            --amber-lt:  #fff3dc;
            --border:    #e2e0d8;
            --radius:    12px;
            --shadow:    0 2px 12px rgba(15,17,23,.07);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--ink);
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand .label {
            font-family: 'Syne', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,.4);
            margin-bottom: 4px;
        }

        .sidebar-brand .school {
            font-family: 'Syne', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        .sidebar-user {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 14px; font-weight: 700;
            color: #fff; flex-shrink: 0;
        }

        .sidebar-user-info .name {
            font-size: 13px; font-weight: 500; color: #fff;
        }

        .sidebar-user-info .role {
            font-size: 11px; color: rgba(255,255,255,.4);
            text-transform: uppercase; letter-spacing: .06em;
        }

        .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }

        .nav-section {
            padding: 20px 24px 6px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            transition: all .15s;
            border-left: 3px solid transparent;
        }

        .nav-link:hover { color: #fff; background: rgba(255,255,255,.06); }

        .nav-link.active {
            color: #fff;
            border-left-color: var(--accent);
            background: rgba(26,107,74,.18);
        }

        .nav-link svg { width: 16px; height: 16px; flex-shrink: 0; opacity: .7; }
        .nav-link.active svg, .nav-link:hover svg { opacity: 1; }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-footer a {
            color: rgba(255,255,255,.4);
            text-decoration: none;
            font-size: 13px;
            display: flex; align-items: center; gap: 8px;
            transition: color .15s;
        }

        .sidebar-footer a:hover { color: rgba(255,255,255,.8); }

        /* ── MAIN ── */
        .main {
            margin-left: 240px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 36px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }

        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 700;
        }

        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .15s;
            white-space: nowrap;
        }

        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #155a3d; }
        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--ink-soft);
        }
        .btn-outline:hover { border-color: var(--ink-soft); color: var(--ink); }

        .content { padding: 32px 36px; flex: 1; }

        /* ── ALERTS ── */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: var(--accent-lt); color: #0e4d33; border: 1px solid #b7dece; }
        .alert-error   { background: var(--warn-lt);   color: #8b2017; border: 1px solid #f5bbb7; }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 22px 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }

        .stat-card.green::before  { background: var(--accent); }
        .stat-card.red::before    { background: var(--warn); }
        .stat-card.amber::before  { background: var(--amber); }
        .stat-card.neutral::before { background: var(--ink-soft); }

        .stat-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin-bottom: 8px;
        }

        .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-sub { font-size: 13px; color: var(--ink-soft); }

        /* ── SECTION HEADER ── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: 17px;
            font-weight: 700;
        }

        /* ── TABLE ── */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 28px;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead th {
            background: var(--paper);
            padding: 11px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ink-soft);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #fafaf8; }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-solde   { background: var(--accent-lt); color: #0e4d33; }
        .badge-partiel { background: var(--amber-lt);  color: #7a4a00; }
        .badge-impaye  { background: var(--warn-lt);   color: #8b2017; }
        .badge-paye    { background: var(--accent-lt); color: #0e4d33; }

        /* ── GRID 2 COLS ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }

        /* ── PROGRESS BAR ── */
        .progress-wrap { display: flex; align-items: center; gap: 10px; }

        .progress-bar {
            flex: 1;
            height: 6px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--accent);
            transition: width .4s;
        }

        .progress-pct { font-size: 12px; font-weight: 600; color: var(--ink-soft); min-width: 36px; text-align: right; }

        /* ── EMPTY ── */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--ink-soft);
        }

        .empty-state p { margin-top: 8px; font-size: 14px; }

        .actions { display: flex; gap: 6px; }

        .btn-icon {
            width: 32px; height: 32px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            border: 1.5px solid var(--border);
            background: transparent;
            color: var(--ink-soft);
            text-decoration: none;
            transition: all .15s;
        }

        .btn-icon:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-lt); }
        .btn-icon svg { width: 15px; height: 15px; }

        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="label">Secrétariat</div>
        <div class="school"><?= htmlspecialchars($currentUser['ecole_nom'] ?? 'École') ?></div>
    </div>

    <div class="sidebar-user">
        <div class="avatar"><?= strtoupper(substr($currentUser['nom'] ?? 'S', 0, 1)) ?></div>
        <div class="sidebar-user-info">
            <div class="name"><?= htmlspecialchars($currentUser['nom'] ?? '') ?></div>
            <div class="role">Secrétaire</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Général</div>
        <a href="/gestionecole/secretaire/dashboard" class="nav-link active">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Tableau de bord
        </a>

        <div class="nav-section">Inscriptions</div>
        <a href="/gestionecole/inscription/choix" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Nouvelle inscription
        </a>
        <a href="/gestionecole/inscription/index" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Liste inscriptions
        </a>

        <div class="nav-section">Paiements</div>
        <a href="/gestionecole/secretaire/paiements" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Paiements
        </a>
        <a href="/gestionecole/secretaire/soldes" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Suivi des soldes
        </a>

        <div class="nav-section">Élèves</div>
        <a href="/gestionecole/eleve/index" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
            Élèves
        </a>
        <a href="/gestionecole/classe/index" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Classes
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="/gestionecole/auth/logout">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Déconnexion
        </a>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="main">
    <header class="topbar">
        <h1 class="topbar-title">Tableau de bord</h1>
        <div class="topbar-right">
            <a href="/gestionecole/inscription/choix" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Nouvelle inscription
            </a>
        </div>
    </header>

    <div class="content">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- ── STATS ── -->
        <div class="stats-grid">
            <div class="stat-card neutral">
                <div class="stat-label">Total inscrits</div>
                <div class="stat-value"><?= number_format($stats['total_inscrits'] ?? 0) ?></div>
                <div class="stat-sub">Année <?= htmlspecialchars($anneeActive['libelle']) ?></div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Comptes soldés</div>
                <div class="stat-value" style="color:var(--accent)"><?= number_format($stats['total_soldés'] ?? 0) ?></div>
                <div class="stat-sub">Scolarité intégralement payée</div>
            </div>
            <div class="stat-card amber">
                <div class="stat-label">Paiements partiels</div>
                <div class="stat-value" style="color:var(--amber)"><?= number_format($stats['total_partiels'] ?? 0) ?></div>
                <div class="stat-sub">Solde restant à encaisser</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">Impayés</div>
                <div class="stat-value" style="color:var(--warn)"><?= number_format($stats['total_impayes'] ?? 0) ?></div>
                <div class="stat-sub">Aucun paiement enregistré</div>
            </div>
        </div>

        <!-- ── MONTANTS ── -->
        <div class="grid-2" style="margin-bottom:28px">
            <div class="card" style="padding:22px 24px; border-top: 3px solid var(--accent);">
                <div class="stat-label">Montant encaissé</div>
                <div class="stat-value" style="color:var(--accent); font-size:28px; margin-top:6px">
                    <?= number_format($stats['montant_encaisse'] ?? 0, 0, ',', ' ') ?> <span style="font-size:16px;font-weight:500">FCFA</span>
                </div>
            </div>
            <div class="card" style="padding:22px 24px; border-top: 3px solid var(--warn);">
                <div class="stat-label">Restant à encaisser</div>
                <div class="stat-value" style="color:var(--warn); font-size:28px; margin-top:6px">
                    <?= number_format($stats['montant_restant'] ?? 0, 0, ',', ' ') ?> <span style="font-size:16px;font-weight:500">FCFA</span>
                </div>
            </div>
        </div>

        <div class="grid-2">
            <!-- ── RÉSUMÉ PAR CLASSE ── -->
            <div>
                <div class="section-header">
                    <h2 class="section-title">Suivi par classe</h2>
                    <a href="/gestionecole/secretaire/soldes" class="btn btn-outline" style="font-size:13px;padding:7px 14px">Tout voir</a>
                </div>
                <div class="card">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Élèves</th>
                                    <th>Progression</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($resumeClasses)): ?>
                                    <tr><td colspan="4" class="empty-state">Aucune donnée</td></tr>
                                <?php else: ?>
                                    <?php foreach ($resumeClasses as $r): ?>
                                        <?php
                                            $pct = $r['total_attendu'] > 0
                                                ? min(100, round($r['total_encaisse'] / $r['total_attendu'] * 100))
                                                : 0;
                                        ?>
                                        <tr>
                                            <td style="font-weight:500"><?= htmlspecialchars($r['classe_nom']) ?></td>
                                            <td><?= $r['nb_inscrits'] ?></td>
                                            <td>
                                                <div class="progress-wrap">
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $pct >= 80 ? 'var(--accent)' : ($pct >= 40 ? 'var(--amber)' : 'var(--warn)') ?>"></div>
                                                    </div>
                                                    <span class="progress-pct"><?= $pct ?>%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="/gestionecole/secretaire/soldes?classe_id=<?= $r['classe_id'] ?>" class="btn-icon" title="Voir">
                                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ── DERNIERS PAIEMENTS ── -->
            <div>
                <div class="section-header">
                    <h2 class="section-title">Derniers paiements</h2>
                    <a href="/gestionecole/secretaire/paiements" class="btn btn-outline" style="font-size:13px;padding:7px 14px">Tout voir</a>
                </div>
                <div class="card">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Élève</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($derniersPaiements)): ?>
                                    <tr><td colspan="4" class="empty-state">Aucun paiement</td></tr>
                                <?php else: ?>
                                    <?php foreach ($derniersPaiements as $p): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight:500;font-size:13px"><?= htmlspecialchars($p['nom_eleve']) ?></div>
                                                <div style="font-size:12px;color:var(--ink-soft)"><?= htmlspecialchars($p['classe_nom']) ?></div>
                                            </td>
                                            <td style="font-weight:600"><?= number_format($p['montant'], 0, ',', ' ') ?> F</td>
                                            <td>
                                                <span class="badge badge-<?= $p['statut'] === 'paye' ? 'solde' : $p['statut'] ?>">
                                                    <?= $p['statut'] === 'paye' ? 'Soldé' : ucfirst($p['statut']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="/gestionecole/secretaire/recu?paiement_id=<?= $p['paiement_id'] ?>" class="btn-icon" title="Reçu">
                                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 17H17.01M17 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2zM9 11h6m-6 4h4"/></svg>
                                                </a>
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

    </div><!-- /content -->
</div><!-- /main -->

</body>
</html>