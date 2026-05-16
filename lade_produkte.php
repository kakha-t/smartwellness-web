<?php
$conn = new mysqli("localhost", "root", "", "smartwellness_db");
$conn->set_charset("utf8");
$gruppe = $_GET['gruppe'] ?? '';
$stmt = $conn->prepare("SELECT * FROM lebensmittel WHERE gruppe = ?");
$stmt->bind_param("s", $gruppe);
$stmt->execute();
$res = $stmt->get_result();
$produkte = [];
while ($row = $res->fetch_assoc()) {
    $produkte[] = $row;
}
echo json_encode($produkte);
