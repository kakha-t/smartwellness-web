<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$tag = $_POST['tag'] ?? '';
$produkt = $_POST['produkt'] ?? '';

if (!$tag || !$produkt) {
    echo json_encode(["success" => false, "message" => "Fehlende Angaben."]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "smartwellness_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Datenbankfehler."]);
    exit;
}

$stmt = $conn->prepare("SELECT daten FROM ernaehrungsplaene WHERE user_id = ? AND tag = ?");
$stmt->bind_param("is", $user_id, $tag);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["success" => false, "message" => "Kein Plan gefunden."]);
    exit;
}

$daten = json_decode($row['daten'], true);
$daten = array_filter($daten, fn($item) => $item['produkt'] !== $produkt);
$neue_daten = json_encode(array_values($daten), JSON_UNESCAPED_UNICODE);

$stmt2 = $conn->prepare("UPDATE ernaehrungsplaene SET daten = ?, aktualisiert_am = NOW() WHERE user_id = ? AND tag = ?");
$stmt2->bind_param("sis", $neue_daten, $user_id, $tag);
$stmt2->execute();

echo json_encode(["success" => true]);
