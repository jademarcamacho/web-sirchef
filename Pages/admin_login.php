<?php
session_start();
require_once "db.php";
require_once "helpers.php";

$error = "";
$notice = "";
ensure_admin_accounts_table($conn);

if (current_admin_account_id() || (isset($_SESSION["user_id"]) && is_admin($conn))) {
    header("Location: admin.php#user-traffic");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";

    if (!is_gmail_address($email)) {
        $error = "Use the admin Gmail address ending in @gmail.com.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, display_name, password, failed_login_attempts, locked_until FROM admin_accounts WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($adminId, $adminUsername, $adminEmail, $displayName, $hash, $failed, $lockedUntil);

        if (!$stmt->fetch()) {
            $error = "Admin Gmail account not found.";
            $stmt->close();
        } elseif ($lockedUntil && strtotime($lockedUntil) > time()) {
            $error = "This admin account is temporarily locked. Try again later.";
            $stmt->close();
        } elseif (!password_verify($password, $hash)) {
            $error = "Wrong admin password.";
            $stmt->close();
            $failed++;
            $locked = $failed >= 3 ? date("Y-m-d H:i:s", time() + 15 * 60) : null;
            $update = $conn->prepare("UPDATE admin_accounts SET failed_login_attempts = ?, locked_until = ? WHERE id = ?");
            $update->bind_param("isi", $failed, $locked, $adminId);
            $update->execute();
            $update->close();
        } else {
            $stmt->close();

            $update = $conn->prepare("UPDATE admin_accounts SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?");
            $update->bind_param("i", $adminId);
            $update->execute();
            $update->close();

            unset($_SESSION["user_id"], $_SESSION["user_name"]);
            $_SESSION["admin_account_id"] = (int) $adminId;
            $_SESSION["admin_username"] = $adminUsername;
            $_SESSION["admin_email"] = $adminEmail;
            $_SESSION["admin_display_name"] = $displayName;
            log_activity($conn, null, "admin_login", "admin", (int) $adminId, $adminEmail . " signed in.");
            header("Location: admin.php#user-traffic");
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | SirChef</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/admin_login.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<main class="admin-login-page">
  <section class="admin-login-card">
    <a href="index.php" class="brand-mini"><i class="fas fa-mortar-pestle"></i> SirChef</a>
    <div class="admin-icon"><i class="fas fa-user-shield"></i></div>
    <h1>Admin Login</h1>
    <p>
      Sign in with the admin-only Gmail address and password to view user traffic, manage recipes, and review messages.
      <strong>Default local admin: admin@gmail.com with password Admin@12345. Change this password in the database after your first login.</strong>
    </p>

    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <?php if ($notice): ?><div class="alert alert-success"><?= e($notice) ?></div><?php endif; ?>

    <form method="post">
      <label>Admin Gmail</label>
      <div class="admin-input">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="admin@gmail.com" pattern="^[A-Za-z0-9._%+\-]+@gmail\.com$" autocomplete="username" required>
      </div>

      <label>Password</label>
      <div class="admin-input">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Admin password" autocomplete="current-password" required>
      </div>

      <button type="submit" class="admin-login-btn"><i class="fas fa-right-to-bracket"></i> Enter Admin</button>
    </form>

    <div class="admin-login-links">
      <a href="index.php">Back to website</a>
      <a href="admin.php#user-traffic">View analytics</a>
    </div>
  </section>
</main>
</body>
</html>
