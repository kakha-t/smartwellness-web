<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$tag = $_POST['tag'] ?? '';
$produkt = trim($_POST['produkt'] ?? '');
$menge = floatval($_POST['menge'] ?? 0);

if (!$tag || !$produkt || $menge <= 0) {
    echo json_encode(["success" => false, "message" => "Ungültige Eingaben."]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "smartwellness_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Verbindung fehlgeschlagen."]);
    exit;
}

//  Nur Produkte erlauben, die es in der Tabelle 'lebensmittel' gibt
$stmt = $conn->prepare("SELECT glyk_index FROM lebensmittel WHERE produkt = ?");
$stmt->bind_param("s", $produkt);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["success" => false, "message" => "Produkt existiert nicht in der Lebensmitteltabelle."]);
    exit;
}

$gi = $row['glyk_index'];

//  Alten Plan laden
$stmt = $conn->prepare("SELECT daten FROM ernaehrungsplaene WHERE user_id = ? AND tag = ?");
$stmt->bind_param("is", $user_id, $tag);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$daten = $row ? json_decode($row['daten'], true) : [];

//  Neues Produkt hinzufügen
$daten[] = [
    "produkt" => $produkt,
    "menge" => $menge,
    "glyk_index" => $gi
];

$neue_daten = json_encode($daten, JSON_UNESCAPED_UNICODE);

if ($row) {
    $stmt2 = $conn->prepare("UPDATE ernaehrungsplaene SET daten = ?, aktualisiert_am = NOW() WHERE user_id = ? AND tag = ?");
    $stmt2->bind_param("sis", $neue_daten, $user_id, $tag);
} else {
    $stmt2 = $conn->prepare("INSERT INTO ernaehrungsplaene (user_id, tag, daten, aktualisiert_am) VALUES (?, ?, ?, NOW())");
    $stmt2->bind_param("iss", $user_id, $tag, $neue_daten);
}

$stmt2->execute();
echo json_encode(["success" => true]);
