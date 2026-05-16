<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['tag']) || !isset($data['produkte'])) {
    echo json_encode(["success" => false, "message" => "Ungültige Daten"]);
    exit;
}

$tag = $data['tag'];
$produkte = json_encode($data['produkte'], JSON_UNESCAPED_UNICODE);
$timestamp = date("Y-m-d H:i:s");

$conn = new mysqli("localhost", "root", "", "smartwellness_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Datenbankfehler"]);
    exit;
}

// Prüfen, ob Plan für diesen Tag existiert
$sql_check = "SELECT id FROM ernaehrungsplaene WHERE user_id = ? AND tag = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("is", $user_id, $tag);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update
    $sql_update = "UPDATE ernaehrungsplaene SET daten = ?, aktualisiert_am = ? WHERE user_id = ? AND tag = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssis", $produkte, $timestamp, $user_id, $tag);
} else {
    // Insert
    $sql_insert = "INSERT INTO ernaehrungsplaene (user_id, tag, daten, aktualisiert_am) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("isss", $user_id, $tag, $produkte, $timestamp);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "✅ Dein Plan wurde erfolgreich gespeichert."]);
} else {
    echo json_encode(["success" => false, "message" => "Fehler beim Speichern in der Datenbank."]);
}
