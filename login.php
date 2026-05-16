<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Nur POST erlaubt.']));
}

$host = 'localhost';
$db = 'smartwellness_db';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Verbindung fehlgeschlagen.']));
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

$sql = "SELECT id, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

header('Content-Type: application/json');
if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();
    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Falsches Passwort!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'E-Mail nicht gefunden!']);
}
$stmt->close();
$conn->close();
exit;
