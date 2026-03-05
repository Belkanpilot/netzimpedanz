<?php
// WICHTIG: Session starten und CSRF Token generieren
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung | Jahreskonferenz 2026</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
</head>
<body>

<div id="header-placeholder"></div>
<section class="section">
    <div class="container">

        <div class="registration-intro" style="margin-bottom: 20px;">
            <h1 style="color: var(--accent-color)"><strong>Registrierung 23. April 2026 - 8. Konferenz</strong></h1>
            <h2 class="reg-title">Netzausnutzung neu denken:<br>Speicher & Überbauung.</h2>
            <p class="reg-text" style="margin-bottom: 0;">
                Die Energiewende stellt uns vor neue Herausforderungen. Wie integrieren wir volatile Erzeuger stabil in unsere Netze? Wie nutzen wir bestehende Infrastrukturen maximal aus? Am <strong>23. April 2026</strong> widmen wir uns in Hamburg den Schlüsseltechnologien des kommenden Jahrzehnts.
            </p>
        </div>

        <div class="form-container" style="margin-bottom: 60px; border: 1px solid #e5e5e5;">

            <form action="register.php" method="POST">

                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div style="display:none; opacity:0; visibility:hidden;">
                    <label for="website_hp">Website</label>
                    <input type="text" id="website_hp" name="website_hp" value="">
                </div>

                <div class="form-group">
                    <label for="name">Vollständiger Name *</label>
                    <input type="text" id="name" name="name" placeholder="Max Mustermann" required>
                </div>

                <div class="form-group">
                    <label for="email">E-Mail-Adresse *</label>
                    <input type="email" id="email" name="email" placeholder="max@beispiel.de" required>
                </div>

                <div class="form-group">
                    <label for="company">Unternehmen / Institution *</label>
                    <input type="text" id="company" name="company" placeholder="Musterfirma GmbH" required>
                </div>

                <div class="form-group">
                    <label for="topic">Themenvorschlag (Call for Presentations)</label>
                    <p style="font-size: 12px; color: #86868b; margin-bottom: 8px;">
                        Haben Sie einen spannenden Beitrag passend zu unserem Schwerpunktthema? (Optional)
                    </p>
                    <textarea id="topic" name="topic" placeholder="Kurze Beschreibung Ihres Vorschlags..."></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="privacy" name="privacy" required>
                    <label for="privacy" style="font-weight: 400; margin: 0;">
                        Ich stimme zu, dass meine Daten für die Organisation der Veranstaltung gespeichert werden.
                    </label>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn" style="width: 100%; border: none; cursor: pointer;">Jetzt verbindlich anmelden</button>
                </div>

            </form>
        </div>

        <div class="registration-intro">
            <div class="reg-highlights">
                <p class="highlight-intro">Wir laden Sie herzlich ein, Teil dieser Diskussion zu sein. Freuen Sie sich auf:</p>
                <ul>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Praxisnahe Einblicke:</strong> Erfahren Sie aus erster Hand, wie Netzbetreiber Batteriespeicher und Überbauung bereits heute erfolgreich einsetzen.</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Zukunftstechnologien:</strong> Deep-Dive in Themen wie Grid-Forming und intelligente Netzsteuerung.</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Exklusives Netzwerk:</strong> Tauschen Sie sich mit führenden Ingenieuren aus Forschung und Industrie aus.</span>
                    </li>
                </ul>
            </div>

            <p class="reg-cfp">
                <strong>Call for Participation:</strong> Gestalten Sie das Programm mit. Wir suchen Vordenker mit spannenden Praxisbeispielen zu unseren Schwerpunktthemen. Reichen Sie Ihren Vorschlag direkt über das Formular ein.
            </p>
        </div>

    </div>
</section>

<div id="footer-placeholder"></div>

<script src="main.js"></script>
</body>
</html>
