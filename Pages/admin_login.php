<?php
session_start();
require_once "db.php";
require_once "helpers.php";

$error = "";
$notice = "";

if (isset($_SESSION["user_id"]) && is_admin($conn)) {
    header("Location: admin.php");
    exit;
}

$adminCount = (int) $conn->query("SELECT COUNT(*) total FROM users WHERE role = 'admin'")->fetch_assoc()["total"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, first_name, password, role FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId, $firstName, $hash, $role);

    if (!$stmt->fetch()) {
        $error = "Admin account not found.";
    } elseif (!password_verify($password, $hash)) {
        $error = "Wrong admin password.";
    } elseif ($role !== "admin" && $adminCount > 0) {
        $error = "This account is not an admin. Ask an existing admin to promote it.";
    } else {
        $stmt->close();

        // First-admin setup: if no admin exists, promote this valid user.
        if ($adminCount === 0) {
            $promote = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $promote->bind_param("i", $userId);
            $promote->execute();
            $promote->close();
            log_activity($conn, (int) $userId, "first_admin_created", "user", (int) $userId, "Promoted through admin login.");
        }

        $_SESSION["user_id"] = (int) $userId;
        $_SESSION["user_name"] = $firstName;
        log_activity($conn, (int) $userId, "admin_login", "user", (int) $userId, "Admin signed in.");
        header("Location: admin.php");
        exit;
    }

    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
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
      Sign in with an admin account to manage recipes, users, email settings, and messages.
      <?php if ($adminCount === 0): ?>
        <strong>No admin exists yet. The first valid user who logs in here will become admin.</strong>
      <?php endif; ?>
    </p>

    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <?php if ($notice): ?><div class="alert alert-success"><?= e($notice) ?></div><?php endif; ?>

    <form method="post">
      <label>Email Address</label>
      <div class="admin-input">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="admin@example.com" required>
      </div>

      <label>Password</label>
      <div class="admin-input">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Admin password" required>
      </div>

      <button type="submit" class="admin-login-btn"><i class="fas fa-right-to-bracket"></i> Enter Admin</button>
    </form>

    <div class="admin-login-links">
      <a href="index.php">Back to website</a>
      <a href="forgot_password.php">Forgot password?</a>
    </div>
  </section>
</main>
</body>
</html>
