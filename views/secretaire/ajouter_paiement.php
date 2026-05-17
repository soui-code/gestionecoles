<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un paiement — Secrétariat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink:#0f1117;--ink-soft:#4a5060;--paper:#f7f6f2;--white:#fff;
            --accent:#1a6b4a;--accent-lt:#e6f3ee;--warn:#c0392b;--warn-lt:#fdecea;
            --amber:#d4840a;--amber-lt:#fff3dc;--border:#e2e0d8;--radius:12px;
            --shadow:0 2px 12px rgba(15,17,23,.07);
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'DM Sans',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;display:flex}
        <?php /* sidebar & layout identiques au dashboard — inclure le même CSS base */ ?>

        /* ── SIDEBAR compact ── */
        .sidebar{width:240px;min-height:100vh;background:var(--ink);color:#fff;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100}
        .sidebar-brand{padding:28px 24px 20px;border-bottom:1px solid rgba(255,255,255,.08)}
        .sidebar-brand .label{font-family:'Syne',sans-serif;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:4px}
        .sidebar-brand .school{font-family:'Syne',sans-serif;font-size:17px;font-weight:700}
        .sidebar-nav{flex:1;padding:12px 0;overflow-y:auto}
        .nav-section{padding:20px 24px 6px;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.3)}
        .nav-link{display:flex;align-items:center;gap:10px;padding:10px 24px;color:rgba(255,255,255,.65);text-decoration:none;font-size:14px;transition:all .15s;border-left:3px solid transparent}
        .nav-link:hover{color:#fff;background:rgba(255,255,255,.06)}
        .nav-link.active{color:#fff;border-left-color:var(--accent);background:rgba(26,107,74,.18)}
        .nav-link svg{width:16px;height:16px;flex-shrink:0;opacity:.7}
        .sidebar-footer{padding:16px 24px;border-top:1px solid rgba(255,255,255,.08)}
        .sidebar-footer a{color:rgba(255,255,255,.4);text-decoration:none;font-size:13px;display:flex;align-items:center;gap:8px;transition:color .15s}
        .sidebar-footer a:hover{color:rgba(255,255,255,.8)}

        /* ── MAIN ── */
        .main{margin-left:240px;flex:1;min-height:100vh;display:flex;flex-direction:column}
        .topbar{background:var(--white);border-bottom:1px solid var(--border);padding:0 36px;height:64px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}
        .topbar-title{font-family:'Syne',sans-serif;font-size:20px;font-weight:700}
        .content{padding:32px 36px;flex:1;max-width:960px}
        .btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:500;cursor:pointer;border:none;text-decoration:none;transition:all .15s;white-space:nowrap}
        .btn-primary{background:var(--accent);color:#fff}
        .btn-primary:hover{background:#155a3d}
        .btn-outline{background:transparent;border:1.5px solid var(--border);color:var(--ink-soft)}
        .btn-outline:hover{border-color:var(--ink-soft);color:var(--ink)}
        .alert{padding:12px 16px;border-radius:var(--radius);margin-bottom:24px;font-size:14px;display:flex;align-items:center;gap:10px}
        .alert-success{background:var(--accent-lt);color:#0e4d33;border:1px solid #b7dece}
        .alert-error{background:var(--warn-lt);color:#8b2017;border:1px solid #f5bbb7}

        /* ── LAYOUT 2 COL ── */
        .grid-form{display:grid;grid-template-columns:1fr 340px;gap:24px}

        /* ── CARD ── */
        .card{background:var(--white);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow)}
        .card-header{padding:18px 24px;border-bottom:1px solid var(--border);font-family:'Syne',sans-serif;font-size:16px;font-weight:700}
        .card-body{padding:24px}

        /* ── FORM ── */
        .form-group{margin-bottom:20px}
        label{display:block;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:6px}
        input,select{width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font-family:'DM Sans',sans-serif;font-size:14px;color:var(--ink);background:var(--white);outline:none;transition:border .15s}
        input:focus,select:focus{border-color:var(--accent)}
        .field-hint{font-size:12px;color:var(--ink-soft);margin-top:5px}

        /* ── SOLDE VISUAL ── */
        .solde-box{background:var(--paper);border-radius:10px;padding:16px;margin-bottom:16px}
        .solde-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px dashed var(--border);font-size:14px}
        .solde-row:last-child{border-bottom:none}
        .solde-row.total{font-weight:700;font-size:16px;color:var(--warn)}
        .solde-label{color:var(--ink-soft)}
        .solde-val{font-weight:600}

        /* ── HISTORIQUE ── */
        .hist-item{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px}
        .hist-item:last-child{border-bottom:none}
        .badge{display:inline-flex;align-items:center;padding:2px 9px;border-radius:999px;font-size:11px;font-weight:600}
        .badge-solde{background:var(--accent-lt);color:#0e4d33}
        .badge-partiel{background:var(--amber-lt);color:#7a4a00}
        .badge-impaye{background:var(--warn-lt);color:#8b2017}

        .eleve-card{display:flex;align-items:center;gap:14px;padding:16px 20px;background:var(--ink);border-radius:var(--radius);color:#fff;margin-bottom:24px}
        .eleve-avatar{width:44px;height:44px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:18px;font-weight:700;flex-shrink:0}
        .eleve-nom{font-family:'Syne',sans-serif;font-size:17px;font-weight:700}
        .eleve-meta{font-size:13px;opacity:.6;margin-top:2px}
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="label">Secrétariat</div>
        <div class="school"><?= htmlspecialchars($currentUser['ecole_nom'] ?? 'École') ?></div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Général</div>
        <a href="/gestionecole/secretaire/dashboard" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Tableau de bord
        </a>
        <div class="nav-section">Paiements</div>
        <a href="/gestionecole/secretaire/paiements" class="nav-link active">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Paiements
        </a>
        <a href="/gestionecole/secretaire/soldes" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Suivi des soldes
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="/gestionecole/auth/logout">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Déconnexion
        </a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <h1 class="topbar-title">Enregistrer un paiement</h1>
        <div style="display:flex;gap:10px">
            <a href="/gestionecole/secretaire/ficheEleve?inscription_id=<?= $fiche['inscription_id'] ?>" class="btn btn-outline">← Fiche élève</a>
        </div>
    </header>

    <div class="content">

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($_SESSION['error']) ?> <?php unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Entête élève -->
        <div class="eleve-card">
            <div class="eleve-avatar"><?= strtoupper(substr($fiche['nom_eleve'], 0, 1)) ?></div>
            <div>
                <div class="eleve-nom"><?= htmlspecialchars($fiche['nom_eleve']) ?></div>
                <div class="eleve-meta"><?= htmlspecialchars($fiche['matricule']) ?> · <?= htmlspecialchars($fiche['classe_nom']) ?> · <?= htmlspecialchars($fiche['annee_libelle']) ?></div>
            </div>
        </div>

        <div class="grid-form">

            <!-- ── FORMULAIRE ── -->
            <div>
                <div class="card">
                    <div class="card-header">Nouveau versement</div>
                    <div class="card-body">
                        <form method="POST" action="/gestionecole/secretaire/ajouterPaiement">
                            <input type="hidden" name="inscription_id" value="<?= htmlspecialchars($fiche['inscription_id']) ?>">

                            <?php
                                $reste = $fiche['reste_a_payer'];
                                $montantAnnuel = $fiche['montant_annuel'];
                            ?>

                            <div class="form-group">
                                <label for="montant">Montant versé (FCFA) *</label>
                                <input type="number"
                                    id="montant" name="montant"
                                    min="1"
                                    max="<?= $reste > 0 ? $reste : '' ?>"
                                    step="100"
                                    value="<?= $reste > 0 ? $reste : '' ?>"
                                    placeholder="Ex: 25000"
                                    required
                                    oninput="updateStatut(this.value)">
                                <?php if ($reste > 0): ?>
                                    <div class="field-hint">Reste à payer : <strong><?= number_format($reste, 0, ',', ' ') ?> FCFA</strong></div>
                                <?php else: ?>
                                    <div class="field-hint" style="color:var(--accent)">✓ Ce compte est déjà soldé</div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="date_paiement">Date du paiement *</label>
                                <input type="date" id="date_paiement" name="date_paiement"
                                    value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="statut">Statut *</label>
                                <select id="statut" name="statut" required>
                                    <option value="partiel">Partiel</option>
                                    <option value="paye">Soldé (paiement complet)</option>
                                    <option value="impaye">Impayé</option>
                                </select>
                                <div class="field-hint" id="statut_hint">Le statut sera mis à jour automatiquement selon le montant saisi.</div>
                            </div>

                            <div style="display:flex;gap:10px;margin-top:8px">
                                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center" <?= $reste <= 0 ? 'onclick="return confirm(\'Ce compte est déjà soldé. Confirmer quand même ?\')"' : '' ?>>
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer le paiement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ── SOLDE + HISTORIQUE ── -->
            <div>
                <div class="card" style="margin-bottom:20px">
                    <div class="card-header">Situation financière</div>
                    <div class="card-body">
                        <div class="solde-box">
                            <div class="solde-row">
                                <span class="solde-label">Scolarité annuelle</span>
                                <span class="solde-val"><?= number_format($fiche['montant_annuel'], 0, ',', ' ') ?> F</span>
                            </div>
                            <div class="solde-row">
                                <span class="solde-label">Total payé</span>
                                <span class="solde-val" style="color:var(--accent)"><?= number_format($fiche['total_paye'], 0, ',', ' ') ?> F</span>
                            </div>
                            <div class="solde-row total">
                                <span class="solde-label" style="font-weight:700">Reste à payer</span>
                                <span><?= number_format(max(0, $fiche['reste_a_payer']), 0, ',', ' ') ?> F</span>
                            </div>
                        </div>

                        <?php
                            $statutActuel = $fiche['statut_paiement'];
                            $pct = $fiche['montant_annuel'] > 0
                                ? min(100, round($fiche['total_paye'] / $fiche['montant_annuel'] * 100))
                                : 0;
                        ?>
                        <div style="margin-bottom:6px;display:flex;justify-content:space-between;font-size:13px">
                            <span style="color:var(--ink-soft)">Avancement</span>
                            <span style="font-weight:600"><?= $pct ?>%</span>
                        </div>
                        <div style="height:8px;background:var(--border);border-radius:4px;overflow:hidden">
                            <div style="height:100%;width:<?= $pct ?>%;background:<?= $pct>=100?'var(--accent)':($pct>=50?'var(--amber)':'var(--warn)') ?>;border-radius:4px;transition:width .4s"></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Historique des versements</div>
                    <div class="card-body" style="padding:16px 20px">
                        <?php if (empty($historique)): ?>
                            <p style="color:var(--ink-soft);font-size:13px;text-align:center;padding:16px 0">Aucun paiement enregistré</p>
                        <?php else: ?>
                            <?php foreach ($historique as $h): ?>
                                <div class="hist-item">
                                    <div>
                                        <div style="font-weight:500;font-size:13px"><?= date('d/m/Y', strtotime($h['date_paiement'])) ?></div>
                                        <div style="font-size:11px;color:var(--ink-soft)"><?= htmlspecialchars($h['numero_facture'] ?? '—') ?></div>
                                    </div>
                                    <div style="text-align:right">
                                        <div style="font-weight:600;font-size:13px;color:var(--accent)"><?= number_format($h['montant'], 0, ',', ' ') ?> F</div>
                                        <a href="/gestionecole/secretaire/recu?paiement_id=<?= $h['id'] ?>"
                                           style="font-size:11px;color:var(--ink-soft);text-decoration:none">🖨 Reçu</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const reste   = <?= (float)$fiche['reste_a_payer'] ?>;
const annuel  = <?= (float)$fiche['montant_annuel'] ?>;
const select  = document.getElementById('statut');
const hint    = document.getElementById('statut_hint');

function updateStatut(val) {
    const v = parseFloat(val) || 0;
    if (annuel <= 0) return;
    const totalApres = <?= (float)$fiche['total_paye'] ?> + v;
    if (totalApres >= annuel) {
        select.value = 'paye';
        hint.textContent = '✓ Ce versement soldera le compte.';
        hint.style.color = 'var(--accent)';
    } else if (v > 0) {
        select.value = 'partiel';
        hint.textContent = 'Reste après ce versement : ' + new Intl.NumberFormat('fr-FR').format(Math.max(0, annuel - totalApres)) + ' FCFA';
        hint.style.color = 'var(--amber)';
    } else {
        select.value = 'impaye';
        hint.textContent = '';
    }
}

// Init
updateStatut(document.getElementById('montant').value);
</script>
</body>
</html>