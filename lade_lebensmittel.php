<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "smartwellness_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$lebensmittel = [];

if (isset($_GET['gruppe_id']) && $_GET['gruppe_id'] !== "") {
    $gruppe = $_GET['gruppe_id'];
    $stmt = $conn->prepare("SELECT id, produkt, kalorien, fett, eiweiss, kohlenhydrate, glyk_index FROM lebensmittel WHERE gruppe = ?");
    $stmt->bind_param("s", $gruppe);
} else {
    $stmt = $conn->prepare("SELECT id, produkt, kalorien, fett, eiweiss, kohlenhydrate, glyk_index FROM lebensmittel");
}

$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $lebensmittel[] = [
        'id' => $row['id'],
        'produkt' => $row['produkt'],
        'kalorien' => $row['kalorien'],
        'fett' => $row['fett'],
        'eiweiss' => $row['eiweiss'],
        'kohlenhydrate' => $row['kohlenhydrate'],
        'glyk_index' => $row['glyk_index']
    ];
}

header('Content-Type: application/json');
echo json_encode($lebensmittel);
$conn->close();
