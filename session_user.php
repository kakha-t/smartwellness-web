<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false]);
    exit;
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'smartwellness_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Datenbankverbindung fehlgeschlagen"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT vorname, nachname, email, telefon, geburtstag FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode([
        "success" => true,
        "vorname" => $user['vorname'],
        "nachname" => $user['nachname'],
        "email" => $user['email'],
        "telefon" => $user['telefon'],
        "geburtstag" => $user['geburtstag']
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Benutzer nicht gefunden"]);
}

$stmt->close();
$conn->close();
