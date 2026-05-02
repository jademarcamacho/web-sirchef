<?php
// Shared helper functions used by pages and backend actions.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function current_admin_account_id(): ?int {
    return isset($_SESSION['admin_account_id']) ? (int) $_SESSION['admin_account_id'] : null;
}

function current_admin_username(): string {
    return $_SESSION['admin_username'] ?? "";
}

function is_gmail_address(string $email): bool {
    return (bool) preg_match('/^[A-Z0-9._%+\-]+@gmail\.com$/i', $email);
}

function require_login(): void {
    if (!current_user_id()) {
        header("Location: index.php");
        exit;
    }
}

function ensure_admin_accounts_table(mysqli $conn): void {
    $conn->query("CREATE TABLE IF NOT EXISTS admin_accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(80) NOT NULL UNIQUE,
        email VARCHAR(160) NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        display_name VARCHAR(120) NOT NULL DEFAULT 'SirChef Admin',
        failed_login_attempts INT NOT NULL DEFAULT 0,
        locked_until DATETIME NULL,
        last_login_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $emailColumn = $conn->query("SHOW COLUMNS FROM admin_accounts LIKE 'email'");
    if (!$emailColumn || $emailColumn->num_rows === 0) {
        $conn->query("ALTER TABLE admin_accounts ADD COLUMN email VARCHAR(160) NULL UNIQUE AFTER username");
    }
    if ($emailColumn) {
        $emailColumn->free();
    }
    $conn->query("UPDATE admin_accounts SET email = CONCAT(username, '@gmail.com') WHERE email IS NULL OR email = ''");

    $result = $conn->query("SELECT COUNT(*) total FROM admin_accounts");
    $total = $result ? (int) $result->fetch_assoc()["total"] : 0;
    if ($result) {
        $result->free();
    }
    if ($total === 0) {
        $username = "admin";
        $email = "admin@gmail.com";
        $displayName = "SirChef Admin";
        $hash = password_hash("Admin@12345", PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_accounts (username, email, password, display_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hash, $displayName);
        $stmt->execute();
        $stmt->close();
    }
}

function is_admin(mysqli $conn, ?int $userId = null): bool {
    if (current_admin_account_id()) {
        return true;
    }
    $userId = $userId ?? current_user_id();
    if (!$userId) {
        return false;
    }
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();
    return $role === "admin";
}

function require_admin(mysqli $conn): void {
    ensure_admin_accounts_table($conn);
    if (current_admin_account_id()) {
        return;
    }
    if (!current_user_id()) {
        header("Location: admin_login.php");
        exit;
    }
    if (!is_admin($conn)) {
        http_response_code(403);
        die("Admin access only.");
    }
}

function e(?string $value): string {
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function clean_text(string $value, int $max = 5000): string {
    $value = trim(strip_tags($value));
    return mb_substr($value, 0, $max);
}

function valid_email(string $email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function code6(): string {
    return (string) random_int(100000, 999999);
}

function public_upload_path(string $filename): string {
    return "../Assets/" . basename($filename);
}

function ensure_recipe_views_column(mysqli $conn): void {
    $result = $conn->query("SHOW COLUMNS FROM recipes LIKE 'views'");
    if ($result && $result->num_rows > 0) {
        $result->free();
        return;
    }
    if ($result) {
        $result->free();
    }
    if (!$conn->query("ALTER TABLE recipes ADD COLUMN views INT NOT NULL DEFAULT 0 AFTER search_count")) {
        $conn->query("ALTER TABLE recipes ADD COLUMN views INT NOT NULL DEFAULT 0");
    }
}

function log_activity(mysqli $conn, ?int $userId, string $action, string $entityType = "", ?int $entityId = null, string $details = ""): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? "";
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? "", 0, 250);
    $stmt = $conn->prepare("INSERT INTO user_activity_logs (user_id, action, entity_type, entity_id, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississs", $userId, $action, $entityType, $entityId, $details, $ip, $ua);
    $stmt->execute();
    $stmt->close();
}

function recipe_average(mysqli $conn, int $recipeId): array {
    $stmt = $conn->prepare("SELECT COALESCE(ROUND(AVG(rating),1),0), COUNT(*) FROM recipe_ratings WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $stmt->bind_result($avg, $count);
    $stmt->fetch();
    $stmt->close();
    return [(float) $avg, (int) $count];
}

function youtube_embed_url(?string $url): string {
    $url = trim((string) $url);
    if ($url === "") {
        return "";
    }
    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
        return "https://www.youtube.com/embed/" . $m[1];
    }
    return "";
}
?>
