<?php
// register.php - SECURE VERSION
session_start();

// Konfiguration
$csvFile = 'teilnehmer_liste_intern_2026.csv';
$internalNotificationEmail = 'info@netzimpedanz.com';
$mailFromAddress = 'noreply@netzimpedanz.com';
$mailReplyTo = 'info@netzimpedanz.com';

/**
 * Sends internal notification and participant confirmation email.
 * Mail errors are logged and must not block registration flow.
 */
function sendRegistrationEmails(
    string $name,
    string $email,
    string $company,
    string $topic,
    string $language,
    string $date,
    string $internalNotificationEmail,
    string $mailFromAddress,
    string $mailReplyTo
): void {
    $topicText = $topic !== '' ? $topic : '—';
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=Georg-Wilhelm-Stra%C3%9Fe+187,+21107+Hamburg';
    $isEnglish = strtoupper($language) === 'EN';

    // Internal notification
    $internalSubject = 'Neue Anmeldung: ' . $name . ' - 8. Konferenz (23.04.2026)';
    $internalBody = "Neue Anmeldung eingegangen.\n\n"
        . "Name: " . $name . "\n"
        . "E-Mail: " . $email . "\n"
        . "Unternehmen/Institution: " . $company . "\n"
        . "Themenvorschlag: " . $topicText . "\n"
        . "Sprache: " . $language . "\n"
        . "Zeitpunkt: " . $date . "\n\n"
        . "Ort:\n"
        . "Ingenieurwerk Hamburg\n"
        . "Georg-Wilhelm-Straße 187, 21107 Hamburg\n"
        . "Google Maps: " . $mapsUrl . "\n";
    $internalHeaders = "MIME-Version: 1.0\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n"
        . "From: Netzimpedanz <" . $mailFromAddress . ">\r\n"
        . "Reply-To: " . $email . "\r\n";
    @mail($internalNotificationEmail, $internalSubject, $internalBody, $internalHeaders);

    // Participant confirmation
    if ($isEnglish) {
        $participantSubject = 'Your registration for the 8th Conference on April 23, 2026';
        $participantBody = "Hello " . $name . ",\n\n"
            . "thank you for registering for the 8th Conference of the Grid Impedance Interest Group.\n\n"
            . "Date: April 23, 2026\n"
            . "Venue: Ingenieurwerk Hamburg, Georg-Wilhelm-Straße 187, 21107 Hamburg, Germany\n"
            . "Google Maps: " . $mapsUrl . "\n\n"
            . "We look forward to welcoming you.\n"
            . "If you have any questions, just reply to this email.\n\n"
            . "Best regards,\n"
            . "Grid Impedance Interest Group Team";
    } else {
        $participantSubject = 'Ihre Anmeldung zur 8. Konferenz am 23.04.2026';
        $participantBody = "Hallo " . $name . ",\n\n"
            . "vielen Dank für Ihre Anmeldung zur 8. Konferenz der Interessengemeinschaft Netzimpedanz.\n\n"
            . "Termin: 23. April 2026\n"
            . "Ort: Ingenieurwerk Hamburg, Georg-Wilhelm-Straße 187, 21107 Hamburg\n"
            . "Google Maps: " . $mapsUrl . "\n\n"
            . "Wir freuen uns auf Ihre Teilnahme.\n"
            . "Bei Rückfragen antworten Sie einfach auf diese E-Mail.\n\n"
            . "Viele Grüße\n"
            . "Ihr Team der Interessengemeinschaft Netzimpedanz";
    }

    $participantHeaders = "MIME-Version: 1.0\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n"
        . "From: Netzimpedanz <" . $mailFromAddress . ">\r\n"
        . "Reply-To: " . $mailReplyTo . "\r\n";
    @mail($email, $participantSubject, $participantBody, $participantHeaders);
}

// 1. CSRF Schutz & Spam Schutz (Honeypot)
// Wenn das Feld "website" (das wir per CSS ausblenden) ausgefüllt ist -> SPAM.
if (!empty($_POST['website_hp'])) {
    die("Spam detected.");
}

// 2. Nur POST erlauben
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. CSRF Token prüfen
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ungültige Sitzung (CSRF Fehler). Bitte laden Sie die Seite neu.");
    }

    // Daten bereinigen
    $name = strip_tags(trim($_POST['name'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $company = strip_tags(trim($_POST['company'] ?? ''));
    $topic = strip_tags(trim($_POST['topic'] ?? ''));
    // Hier holen wir die Sprache aus dem versteckten Feld (EN oder DE)
    $language = strip_tags(trim($_POST['_language'] ?? 'DE'));
    $date = date('d.m.Y H:i');

    // Validierung
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($company)) {
        die("Ungültige Eingaben.");
    }

    $dataRow = [$date, $name, $email, $company, $topic, $language];

    // 4. File Locking (Verhindert Crash bei gleichzeitigem Schreiben)
    $fp = fopen($csvFile, 'a');
    if (flock($fp, LOCK_EX)) { // Exklusiven Schreibzugriff anfordern

        // Wenn Datei leer, Header schreiben
        if (filesize($csvFile) == 0) {
            fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
            fputcsv($fp, ['Datum', 'Name', 'E-Mail', 'Firma', 'Themenvorschlag', 'Sprache'], ';');
        }

        fputcsv($fp, $dataRow, ';');
        flock($fp, LOCK_UN); // Sperre aufheben
    } else {
        die("Fehler beim Speichern (Datei gesperrt). Bitte versuchen Sie es erneut.");
    }
    fclose($fp);

    // 5. E-Mails versenden (fehlertolerant)
    try {
        sendRegistrationEmails(
            $name,
            $email,
            $company,
            $topic,
            $language,
            $date,
            $internalNotificationEmail,
            $mailFromAddress,
            $mailReplyTo
        );
    } catch (Throwable $e) {
        error_log('Registration mail error: ' . $e->getMessage());
    }

    // Session Token erneuern für nächsten Request
    unset($_SESSION['csrf_token']);

    // --- HIER IST DIE ÄNDERUNG (Zeile 59-64) ---
    // Wir prüfen, welche Sprache im Formular stand ($language)
    if ($language === 'EN') {
        header("Location: danke-en.html"); // <--- Englische Seite
    } else {
        header("Location: danke.html");    // <--- Deutsche Seite (Standard)
    }
    exit;
    // -------------------------------------------

} else {
    header("Location: index.html");
    exit;
}
?>