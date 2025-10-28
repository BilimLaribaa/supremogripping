<?php
// contact.php — handles form submission and database connection

// Allow CORS and JSON output
header("Content-Type: application/json");

// Database connection
try {
    $db = new PDO('sqlite:contact_database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS contact (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        comment TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['mail'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($name === '' || $email === '' || $comment === '') {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    try {
        $stmt = $db->prepare("INSERT INTO contact (name, email, comment) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $comment]);
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to save data."]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid request."]);
?>
