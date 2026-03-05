<?php
// register.php - SECURE VERSION
session_start();

// Konfiguration
$csvFile = 'teilnehmer_liste_intern_2026.csv';

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