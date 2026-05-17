<?php
// views/auth/login.php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — GestionEcole</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 420px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1a73e8;
        }

        .logo p {
            font-size: 13px;
            color: #888;
            margin-top: 4px;
        }

        .error {
            background: #fdecea;
            color: #c0392b;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 4px solid #c0392b;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            transition: border 0.2s;
            outline: none;
        }

        input:focus {
            border-color: #1a73e8;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1a73e8;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.2s;
        }

        button:hover {
            background: #1558b0;
        }

        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="card">

    <div class="logo">
        <h1>🏫 GestionEcole</h1>
        <p>Connectez-vous à votre espace</p>
    </div>

    <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="/gestionecole/auth/login" method="POST">

        <div class="form-group">
            <label for="email">Adresse email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="exemple@ecole.com"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="••••••••"
                required
            >
        </div>

        <button type="submit">Se connecter</button>

    </form>

    <div class="footer">
        &copy; <?= date('Y') ?> GestionEcole — Tous droits réservés
    </div>

</div>

</body>
</html>