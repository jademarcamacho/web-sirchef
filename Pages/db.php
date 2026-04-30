<?php
// Central database connection for SirChef.
// Import database.sql first, then update these credentials if your XAMPP setup differs.
$host = "localhost";
$username = "root";
$password = "";
$dbname = "sirchef_db";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed. Please import database.sql and check Pages/db.php.");
}

$conn->set_charset("utf8mb4");

// Older files used $recipe_conn. Keep it as an alias so existing includes do not break.
$recipe_conn = $conn;
?>
