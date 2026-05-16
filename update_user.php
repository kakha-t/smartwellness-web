<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt."]);
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "smartwellness_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Verbindung fehlgeschlagen."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_POST['email'] ?? '';
$telefon = $_POST['telefon'] ?? '';
$geburtstag = $_POST['geburtstag'] ?? '';

$stmt = $conn->prepare("UPDATE users SET email = ?, telefon = ?, geburtstag = ? WHERE id = ?");
$stmt->bind_param("sssi", $email, $telefon, $geburtstag, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "✅ Daten erfolgreich gespeichert."]);
} else {
    echo json_encode(["success" => false, "message" => "⚠️ Keine Änderungen vorgenommen."]);
}

$stmt->close();
$conn->close();
