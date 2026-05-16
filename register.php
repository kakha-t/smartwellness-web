<?php
// Fehleranzeigen aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Nur POST-Anfragen verarbeiten
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "⚠️ Diese Seite erwartet ein Formular (POST). Bitte <a href='register.html'>Registrierungsformular</a> verwenden.";
    exit();
}

// Verbindung zur Datenbank
$host = 'localhost';
$db = 'smartwellness_db';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

// Verbindungsfehler prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Formulardaten
$vorname = trim($_POST['vorname']);
$nachname = trim($_POST['nachname']);
$email = trim($_POST['email']);
$telefon = trim($_POST['phone']);
$geburtstag = trim($_POST['geburtstag']);
$password = trim($_POST['password']);
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// E-Mail prüfen
$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
if (!$check_stmt) {
    die("Fehler bei der Vorbereitung der Abfrage: " . $conn->error);
}
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "<script>
        localStorage.setItem('registrationError', 'true');
        window.location.href = 'register.html';
    </script>";
    $check_stmt->close();
    $conn->close();
    exit();
}
$check_stmt->close();

// Benutzer einfügen
$insert_sql = "INSERT INTO users (vorname, nachname, email, telefon, geburtstag, password) VALUES (?, ?, ?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
if (!$insert_stmt) {
    die("Fehler bei der Vorbereitung des Inserts: " . $conn->error);
}
$insert_stmt->bind_param("ssssss", $vorname, $nachname, $email, $telefon, $geburtstag, $hashed_password);

if ($insert_stmt->execute()) {
    echo "<script>
        localStorage.setItem('registrationSuccess', 'true');
        window.location.href = 'register.html';
    </script>";
} else {
    echo "<script>
        localStorage.setItem('registrationError', 'true');
        window.location.href = 'register.html';
    </script>";
}

$insert_stmt->close();
$conn->close();
