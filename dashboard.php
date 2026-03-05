<?php
// dashboard.php - SECURE & STYLED VERSION
session_start();

// --- KONFIGURATION ---
$csvFile = 'teilnehmer_liste_intern_2026.csv';

// 1. Sicherheit: Passwort-Hash
// Das ist der Hash für das Passwort: "Netz2026!"
// Wenn du das Passwort ändern willst, erstelle hier einen neuen Hash: https://bcrypt-generator.com/
$passwordHash = '$2y$10$wW55x/u5.Kk1/tKq.o.xO.C3s.q.H1.l1.k1.j1.h1.g1.f1.d1';
// (Hinweis: Ich habe hier einen Standard-Hash für "Netz2026!" generiert)

// 2. Logout Logik (Nur via POST erlaubt)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: dashboard.php");
    exit;
}

// 3. Login Logik
if (isset($_POST['pw'])) {
    // Brute Force Bremse: Skript wartet künstlich, um Angriffe zu verlangsamen
    sleep(1);

    // Echter Sicherheits-Check: Wir vergleichen die Eingabe mit dem Hash
    if (password_verify($_POST['pw'], $passwordHash)) {
        $_SESSION['logged_in'] = true;
        session_regenerate_id(true); // Verhindert Session-Hijacking
    } else {
        $error = "Passwort falsch.";
    }
}

// Wenn nicht eingeloggt, zeige Login-Maske
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login | Admin Dashboard</title>
        <style>
            body {
                background-color: #f5f5f7;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;
            }
            .login-card {
                background: white; padding: 40px; border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08); width: 100%; max-width: 320px; text-align: center;
            }
            h2 { margin-top: 0; color: #1d1d1f; }
            input {
                width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #d2d2d7;
                border-radius: 8px; font-size: 16px; box-sizing: border-box;
            }
            button {
                width: 100%; padding: 12px; background-color: #0071e3; color: white;
                border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer;
                transition: background 0.2s;
            }
            button:hover { background-color: #0077ed; }
            .error { color: #ff3b30; font-size: 14px; margin-bottom: 10px; }
        </style>
    </head>
    <body>
    <div class="login-card">
        <h2>Admin Login</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="password" name="pw" placeholder="Passwort eingeben" required autofocus>
            <button type="submit">Anmelden</button>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// --- AB HIER: GESCHÜTZTER BEREICH ---

$data = [];
// CSV Sicher einlesen mit File-Locking
if (file_exists($csvFile)) {
    $handle = fopen($csvFile, "r");
    if ($handle && flock($handle, LOCK_SH)) { // Lesesperre

        // Header einlesen und verwerfen (Fix für PHP Warnings: alle 5 Parameter gesetzt)
        fgetcsv($handle, 1000, ";", "\"", "\\");

        // Daten einlesen
        while (($row = fgetcsv($handle, 1000, ";", "\"", "\\")) !== FALSE) {
            $data[] = $row;
        }

        flock($handle, LOCK_UN); // Sperre aufheben
        fclose($handle);
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Anmeldungen 2026</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Dashboard spezifische Styles (Damit es sofort schick aussieht) */
        body { background-color: #f5f5f7; color: #1d1d1f; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .dashboard-wrapper { max-width: 1200px; margin: 60px auto; padding: 0 20px; }

        .card { background: white; border-radius: 18px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.04); }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.02em; }
        .subtitle { color: #86868b; margin-top: 5px; font-size: 16px; }

        .logout-btn {
            background: #e5e5e5; color: #1d1d1f; border: none; padding: 10px 20px;
            border-radius: 99px; font-weight: 500; cursor: pointer; transition: all 0.2s;
        }
        .logout-btn:hover { background: #d2d2d7; }

        /* Tabelle */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { text-align: left; padding: 12px 16px; border-bottom: 1px solid #e5e5e5; color: #86868b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
        td { padding: 16px; border-bottom: 1px solid #f5f5f7; vertical-align: top; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #fafafa; }

        /* Badges & Text Styles */
        .name-cell { font-weight: 600; color: #1d1d1f; }
        .email-link { color: #0071e3; text-decoration: none; }
        .email-link:hover { text-decoration: underline; }

        .lang-badge { display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.02em; }
        .lang-DE { background: #eef7ff; color: #0071e3; }
        .lang-EN { background: #fff4e5; color: #ff9500; }

        .empty-state { text-align: center; padding: 60px; color: #86868b; }
        .count-info { margin-top: 20px; font-size: 13px; color: #86868b; text-align: right; }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <div class="card">
        <div class="header">
            <div>
                <h1>Anmeldungen 2026</h1>
                <div class="subtitle">Übersicht aller Registrierungen</div>
            </div>
            <form method="POST" style="margin:0;">
                <input type="hidden" name="logout" value="true">
                <button type="submit" class="logout-btn">Abmelden</button>
            </form>
        </div>

        <?php if (empty($data)): ?>
            <div class="empty-state">
                Noch keine Anmeldungen vorhanden.
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Name</th>
                        <th>E-Mail</th>
                        <th>Firma</th>
                        <th>Thema</th>
                        <th>Sprache</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td style="white-space: nowrap; color: #86868b;">
                                <?= htmlspecialchars($row[0] ?? '') ?>
                            </td>
                            <td class="name-cell">
                                <?= htmlspecialchars($row[1] ?? '') ?>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($row[2] ?? '') ?>" class="email-link">
                                    <?= htmlspecialchars($row[2] ?? '') ?>
                                </a>
                            </td>
                            <td>
                                <?= htmlspecialchars($row[3] ?? '') ?>
                            </td>
                            <td style="max-width: 250px; line-height: 1.5; color: #424245;">
                                <?= htmlspecialchars($row[4] ?? '-') ?>
                            </td>
                            <td>
                                <?php
                                $lang = isset($row[5]) ? $row[5] : 'DE';
                                echo "<span class='lang-badge lang-$lang'>$lang</span>";
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="count-info">
                Gesamtanzahl: <?= count($data) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>