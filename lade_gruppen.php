<?php
$conn = new mysqli("localhost", "root", "", "smartwellness_db");
$conn->set_charset("utf8");
$res = $conn->query("SELECT DISTINCT gruppe FROM lebensmittel ORDER BY gruppe");
$gruppen = [];
while ($row = $res->fetch_assoc()) {
    $gruppen[] = $row;
}
echo json_encode($gruppen);
