<?php
ob_start();
ini_set("display_errors", "0");
// JSON backend for auth, contact, newsletter, recipe matching, and social actions.
require_once "db.php";
require_once "helpers.php";
require_once "emailService.php";

header("Content-Type: application/json");

function json_out(array $payload): void {
    if (ob_get_length() !== false) {
        ob_clean();
    }
    echo json_encode($payload);
    exit;
}

function require_post(): void {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        json_out(["success" => false, "message" => "Invalid request method."]);
    }
}

function require_ajax_login(): int {
    $id = current_user_id();
    if (!$id) {
        json_out(["success" => false, "message" => "Please log in first.", "login_required" => true]);
    }
    return $id;
}

function ini_size_to_bytes(string $value): int {
    $value = trim($value);
    if ($value === "") {
        return 0;
    }
    $unit = strtolower($value[strlen($value) - 1]);
    $bytes = (float) $value;
    if ($unit === "g") {
        $bytes *= 1024;
    }
    if ($unit === "g" || $unit === "m") {
        $bytes *= 1024;
    }
    if ($unit === "g" || $unit === "m" || $unit === "k") {
        $bytes *= 1024;
    }
    return (int) $bytes;
}

function human_bytes(int $bytes): string {
    if ($bytes >= 1024 * 1024) {
        return round($bytes / 1024 / 1024) . "MB";
    }
    return round($bytes / 1024) . "KB";
}

function ingredient_aliases(): array {
    $aliases = [
        "green onions" => "scallion",
        "green onion" => "scallion",
        "spring onions" => "scallion",
        "spring onion" => "scallion",
        "scallions" => "scallion",
        "prawns" => "shrimp",
        "prawn" => "shrimp",
        "chilli" => "chili",
        "chillies" => "chili",
        "chilies" => "chili",
        "chile" => "chili",
        "capsicums" => "bell pepper",
        "capsicum" => "bell pepper",
        "bell peppers" => "bell pepper",
        "aubergines" => "eggplant",
        "aubergine" => "eggplant",
        "brinjals" => "eggplant",
        "brinjal" => "eggplant",
        "coriander leaves" => "cilantro",
        "coriander" => "cilantro",
        "garbanzo beans" => "chickpea",
        "garbanzo" => "chickpea",
        "chicken breasts" => "chicken",
        "chicken breast" => "chicken",
        "chicken thighs" => "chicken",
        "chicken thigh" => "chicken",
        "ground beef" => "beef",
        "minced beef" => "beef",
        "beef mince" => "beef",
        "ground pork" => "pork",
        "minced pork" => "pork",
        "soy" => "soy sauce",
    ];
    uksort($aliases, fn($a, $b) => strlen($b) <=> strlen($a));
    return $aliases;
}

function ingredient_stop_words(): array {
    return array_fill_keys([
        "a", "an", "and", "or", "of", "with", "to", "for",
        "cup", "cups", "tbsp", "tablespoon", "tablespoons", "tsp", "teaspoon", "teaspoons",
        "gram", "grams", "kg", "g", "ml", "liter", "liters", "litre", "litres",
        "lb", "lbs", "oz", "ounce", "ounces", "pound", "pounds",
        "pinch", "dash", "clove", "cloves", "piece", "pieces", "slice", "slices",
        "can", "cans", "pack", "packs", "package", "packages", "bunch", "stalk", "stalks",
        "small", "medium", "large", "fresh", "dried", "dry", "chopped", "minced",
        "diced", "sliced", "crushed", "grated", "ground", "cooked", "raw", "boneless",
        "skinless", "lean", "unsalted", "salted", "optional", "taste"
    ], true);
}

function ingredient_product_words(): array {
    return array_fill_keys([
        "sauce", "paste", "powder", "milk", "broth", "stock", "paper", "bun",
        "tortilla", "noodle", "pasta", "flour", "oil", "cheese", "butter", "cream", "base"
    ], true);
}

function ingredient_singular_token(string $token): string {
    $irregular = [
        "tomatoes" => "tomato",
        "potatoes" => "potato",
        "leaves" => "leaf",
        "eggs" => "egg",
        "herbs" => "herb",
        "noodles" => "noodle",
        "mushrooms" => "mushroom",
        "carrots" => "carrot",
        "peanuts" => "peanut",
    ];
    if (isset($irregular[$token])) {
        return $irregular[$token];
    }
    $length = strlen($token);
    if ($length > 4 && substr($token, -3) === "ies") {
        return substr($token, 0, -3) . "y";
    }
    if ($length > 4 && in_array(substr($token, -2), ["es"], true) && preg_match("/(ch|sh|x|s|z)es$/", $token)) {
        return substr($token, 0, -2);
    }
    if ($length > 3 && substr($token, -1) === "s" && substr($token, -2) !== "ss") {
        return substr($token, 0, -1);
    }
    return $token;
}

function normalize_ingredient_name(string $value): string {
    $value = strtolower(trim($value));
    $value = preg_replace("/\([^)]*\)/", " ", $value);
    $value = preg_replace("/\b\d+([\/.]\d+)?\b/", " ", $value);
    $value = str_replace(["&", "+", "-"], " ", $value);
    $value = preg_replace("/[^a-z\s]/", " ", $value);
    $value = preg_replace("/\s+/", " ", trim($value));

    foreach (ingredient_aliases() as $alias => $canonical) {
        $value = preg_replace("/\b" . preg_quote($alias, "/") . "\b/", $canonical, $value);
    }

    $stopWords = ingredient_stop_words();
    $tokens = [];
    foreach (preg_split("/\s+/", trim($value)) as $token) {
        $token = ingredient_singular_token($token);
        if ($token === "" || strlen($token) < 2 || isset($stopWords[$token])) {
            continue;
        }
        $tokens[] = $token;
    }
    return implode(" ", array_values(array_unique($tokens)));
}

function ingredient_tokens(string $normalized): array {
    if ($normalized === "") {
        return [];
    }
    return preg_split("/\s+/", $normalized);
}

function ingredient_product_conflict(array $leftTokens, array $rightTokens): bool {
    $products = ingredient_product_words();
    $leftProducts = array_values(array_intersect($leftTokens, array_keys($products)));
    $rightProducts = array_values(array_intersect($rightTokens, array_keys($products)));
    if (!$leftProducts && !$rightProducts) {
        return false;
    }
    if ($leftProducts && $rightProducts && array_intersect($leftProducts, $rightProducts)) {
        return false;
    }
    return true;
}

function ingredient_similarity_score(string $wanted, string $recipeIngredient): float {
    if ($wanted === "" || $recipeIngredient === "") {
        return 0.0;
    }
    if ($wanted === $recipeIngredient) {
        return 1.0;
    }

    $wantedTokens = ingredient_tokens($wanted);
    $recipeTokens = ingredient_tokens($recipeIngredient);
    if (!$wantedTokens || !$recipeTokens || ingredient_product_conflict($wantedTokens, $recipeTokens)) {
        return 0.0;
    }

    $common = array_values(array_intersect($wantedTokens, $recipeTokens));
    $commonCount = count($common);
    if ($commonCount === 0) {
        similar_text($wanted, $recipeIngredient, $percent);
        return $percent >= 82 ? min(0.78, $percent / 120) : 0.0;
    }

    $smaller = max(1, min(count($wantedTokens), count($recipeTokens)));
    $union = max(1, count(array_unique(array_merge($wantedTokens, $recipeTokens))));
    $overlap = $commonCount / $smaller;
    $jaccard = $commonCount / $union;

    if ($overlap >= 1.0) {
        return count($wantedTokens) === count($recipeTokens) ? 1.0 : 0.88;
    }
    if ($overlap >= 0.67 || $jaccard >= 0.5) {
        return 0.72 + (0.16 * $jaccard);
    }

    return 0.0;
}

function ingredient_search_parts(string $raw, int $limit): array {
    $parts = [];
    $seen = [];
    foreach (preg_split("/[,;\r\n]+/", $raw) as $part) {
        $label = strtolower(clean_text($part, 80));
        $key = normalize_ingredient_name($label);
        if ($key === "" || isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $parts[] = ["label" => $label, "key" => $key];
        if ($limit > 0 && count($parts) >= $limit) {
            break;
        }
    }
    return $parts;
}

function score_recipe_for_ingredients(array $wantedParts, array $recipeIngredients): array {
    $normalizedRecipeIngredients = [];
    foreach ($recipeIngredients as $ingredient) {
        $key = normalize_ingredient_name($ingredient);
        if ($key !== "" && !in_array($key, $normalizedRecipeIngredients, true)) {
            $normalizedRecipeIngredients[] = $key;
        }
    }

    $matchedCount = 0;
    $qualityTotal = 0.0;
    foreach ($wantedParts as $wanted) {
        $bestScore = 0.0;
        foreach ($normalizedRecipeIngredients as $recipeIngredient) {
            $bestScore = max($bestScore, ingredient_similarity_score($wanted["key"], $recipeIngredient));
        }
        if ($bestScore >= 0.72) {
            $matchedCount++;
            $qualityTotal += $bestScore;
        }
    }

    $searchedCount = max(1, count($wantedParts));
    $totalCount = max(1, count($normalizedRecipeIngredients));
    return [
        "matched_count" => $matchedCount,
        "total_count" => $totalCount,
        "match_score" => $matchedCount / $searchedCount,
        "quality_score" => $qualityTotal / $searchedCount,
        "coverage_score" => $matchedCount / $searchedCount,
        "recipe_coverage" => $matchedCount / $totalCount,
    ];
}

function ensure_user_posted_recipes_table(mysqli $conn): void {
    $sql = "CREATE TABLE IF NOT EXISTS user_posted_recipes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipe_id INT NOT NULL,
        user_id INT NOT NULL,
        title VARCHAR(180) NOT NULL,
        description TEXT NOT NULL,
        duration_minutes INT NOT NULL,
        difficulty ENUM('Easy','Medium','Hard') NOT NULL,
        cuisine VARCHAR(80) NOT NULL,
        category VARCHAR(80) NOT NULL DEFAULT 'Dishes',
        image VARCHAR(255) NOT NULL,
        youtube_url VARCHAR(255) NULL,
        ingredients TEXT NOT NULL,
        instructions TEXT NOT NULL,
        status ENUM('draft','published') NOT NULL DEFAULT 'published',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_user_posted_recipe (recipe_id),
        INDEX idx_user_posted_user (user_id),
        INDEX idx_user_posted_status (status),
        FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    if (!$conn->query($sql)) {
        json_out(["success" => false, "message" => "Could not prepare the user recipe database."]);
    }
}

require_post();
$serverPostLimit = ini_size_to_bytes((string) ini_get("post_max_size"));
if (empty($_POST) && empty($_FILES) && (int) ($_SERVER["CONTENT_LENGTH"] ?? 0) > 0 && $serverPostLimit > 0) {
    json_out(["success" => false, "message" => "That upload is larger than the server limit of " . human_bytes($serverPostLimit) . "."]);
}
$action = $_POST["action"] ?? "";

if ($action === "register") {
    $first = clean_text($_POST["firstName"] ?? "", 60);
    $last = clean_text($_POST["lastName"] ?? "", 60);
    $email = strtolower(trim($_POST["email"] ?? ""));
    $plain = $_POST["password"] ?? "";

    if ($first === "" || $last === "" || !valid_email($email) || strlen($plain) < 8) {
        json_out(["success" => false, "message" => "Please provide a valid name, email, and strong password."]);
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        json_out(["success" => false, "field" => "email", "message" => "This email is already registered."]);
    }
    $stmt->close();

    $hash = password_hash($plain, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, is_verified) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $first, $last, $email, $hash);
    $stmt->execute();
    $userId = $stmt->insert_id;
    $stmt->close();

    $code = code6();
    $stmt = $conn->prepare("INSERT INTO email_verifications (user_id, email, code, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
    $stmt->bind_param("iss", $userId, $email, $code);
    $stmt->execute();
    $stmt->close();

    $emailSent = sendVerificationEmail($email, $first, $code);
    log_activity($conn, $userId, "register", "user", $userId, "Registration created; verification required.");
    json_out([
        "success" => true,
        "message" => $emailSent
            ? "Registration successful. Check your email for the verification code."
            : "Registration successful, but the verification email could not be sent. Please ask an admin to check SMTP settings.",
        "redirect" => "verify_email.php?email=" . urlencode($email)
    ]);
}

if ($action === "verify_email") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $code = preg_replace("/\D+/", "", $_POST["code"] ?? "");

    $stmt = $conn->prepare("SELECT ev.id, ev.user_id, u.first_name FROM email_verifications ev JOIN users u ON u.id = ev.user_id WHERE ev.email = ? AND ev.code = ? AND ev.used_at IS NULL AND ev.expires_at > NOW() ORDER BY ev.id DESC LIMIT 1");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->bind_result($verificationId, $userId, $firstName);
    if (!$stmt->fetch()) {
        json_out(["success" => false, "message" => "Invalid or expired verification code."]);
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verified_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE email_verifications SET used_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $verificationId);
    $stmt->execute();
    $stmt->close();

    $_SESSION["user_id"] = (int) $userId;
    $_SESSION["user_name"] = $firstName;
    sendEmail($email, "Welcome to SirChef", welcomeEmail($firstName));
    log_activity($conn, (int) $userId, "verify_email", "user", (int) $userId, "Email verified.");
    json_out(["success" => true, "message" => "Email verified. Welcome to SirChef!", "redirect" => "dashboard.php"]);
}

if ($action === "login") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $plain = $_POST["password"] ?? "";

    if (is_gmail_address($email)) {
        ensure_admin_accounts_table($conn);
        $stmt = $conn->prepare("SELECT id, username, email, display_name, password, failed_login_attempts, locked_until FROM admin_accounts WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($adminId, $adminUsername, $adminEmail, $displayName, $adminHash, $adminFailed, $adminLockedUntil);
        if ($stmt->fetch()) {
            $stmt->close();
            if ($adminLockedUntil && strtotime($adminLockedUntil) > time()) {
                json_out(["success" => false, "message" => "This admin account is temporarily locked. Try again later."]);
            }
            if (!password_verify($plain, $adminHash)) {
                $adminFailed++;
                $locked = $adminFailed >= 3 ? date("Y-m-d H:i:s", time() + 15 * 60) : null;
                $stmt = $conn->prepare("UPDATE admin_accounts SET failed_login_attempts = ?, locked_until = ? WHERE id = ?");
                $stmt->bind_param("isi", $adminFailed, $locked, $adminId);
                $stmt->execute();
                $stmt->close();
                json_out(["success" => false, "field" => "password", "message" => "Wrong admin password."]);
            }

            $stmt = $conn->prepare("UPDATE admin_accounts SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $stmt->close();

            unset($_SESSION["user_id"], $_SESSION["user_name"]);
            $_SESSION["admin_account_id"] = (int) $adminId;
            $_SESSION["admin_username"] = $adminUsername;
            $_SESSION["admin_email"] = $adminEmail;
            $_SESSION["admin_display_name"] = $displayName;
            log_activity($conn, null, "admin_login", "admin", (int) $adminId, $adminEmail . " signed in.");
            json_out(["success" => true, "name" => $displayName, "redirect" => "admin.php#user-traffic"]);
        }
        $stmt->close();
    }

    $stmt = $conn->prepare("SELECT id, first_name, password, failed_login_attempts, locked_until FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $firstName, $hash, $failed, $lockedUntil);
    if (!$stmt->fetch()) {
        json_out(["success" => false, "field" => "password", "message" => "Wrong password or email not found."]);
    }
    $stmt->close();

    if ($lockedUntil && strtotime($lockedUntil) > time()) {
        json_out(["success" => false, "message" => "Account locked after 3 wrong attempts. Please reset your password.", "redirect" => "forgot_password.php?email=" . urlencode($email)]);
    }
    if (!password_verify($plain, $hash)) {
        $failed++;
        $locked = $failed >= 3 ? date("Y-m-d H:i:s", time() + 15 * 60) : null;
        $stmt = $conn->prepare("UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?");
        $stmt->bind_param("isi", $failed, $locked, $id);
        $stmt->execute();
        $stmt->close();
        json_out(["success" => false, "field" => "password", "message" => "Wrong password.", "redirect" => $failed >= 3 ? "forgot_password.php?email=" . urlencode($email) : null]);
    }

    $stmt = $conn->prepare("UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION["user_id"] = (int) $id;
    $_SESSION["user_name"] = $firstName;
    sendEmail($email, "SirChef login notice", loginAlertEmail($firstName));
    log_activity($conn, (int) $id, "login", "user", (int) $id, "Successful login.");
    json_out(["success" => true, "name" => $firstName, "redirect" => "dashboard.php"]);
}

if ($action === "forgot_password") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $stmt = $conn->prepare("SELECT id, first_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId, $firstName);
    if ($stmt->fetch()) {
        $stmt->close();
        $code = code6();
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, email, code, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
        $stmt->bind_param("iss", $userId, $email, $code);
        $stmt->execute();
        $stmt->close();
        $emailSent = sendPasswordResetEmail($email, $firstName, $code);
        log_activity($conn, (int) $userId, "request_password_reset", "user", (int) $userId, "Password reset code sent.");
    }
    json_out([
        "success" => true,
        "message" => isset($code) && isset($emailSent) && !$emailSent
            ? "The reset email could not be sent. Please ask an admin to check SMTP settings."
            : "If that email exists, a reset code was sent.",
        "redirect" => "reset_password.php?email=" . urlencode($email)
    ]);
}

if ($action === "reset_password") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $code = preg_replace("/\D+/", "", $_POST["code"] ?? "");
    $plain = $_POST["password"] ?? "";
    if (strlen($plain) < 8) {
        json_out(["success" => false, "message" => "Password must be at least 8 characters."]);
    }

    $stmt = $conn->prepare("SELECT pr.id, pr.user_id FROM password_resets pr WHERE pr.email = ? AND pr.code = ? AND pr.used_at IS NULL AND pr.expires_at > NOW() ORDER BY pr.id DESC LIMIT 1");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->bind_result($resetId, $userId);
    if (!$stmt->fetch()) {
        json_out(["success" => false, "message" => "Invalid or expired reset code."]);
    }
    $stmt->close();

    $hash = password_hash($plain, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, failed_login_attempts = 0, locked_until = NULL WHERE id = ?");
    $stmt->bind_param("si", $hash, $userId);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $resetId);
    $stmt->execute();
    $stmt->close();

    log_activity($conn, (int) $userId, "reset_password", "user", (int) $userId, "Password changed.");
    json_out(["success" => true, "message" => "Password updated. You can now log in.", "redirect" => "index.php"]);
}

if ($action === "newsletter") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    if (!valid_email($email)) {
        json_out(["success" => false, "message" => "Enter a valid email address."]);
    }
    $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE is_active = 1, updated_at = NOW()");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
    sendEmail($email, "Welcome to SirChef updates", "<p>Thanks for subscribing to SirChef. We will send recipe ideas and community updates.</p>");
    json_out(["success" => true, "message" => "Subscribed successfully."]);
}

if ($action === "contact") {
    $first = clean_text($_POST["first_name"] ?? "", 80);
    $last = clean_text($_POST["last_name"] ?? "", 80);
    $email = strtolower(trim($_POST["email"] ?? ""));
    $subject = clean_text($_POST["subject"] ?? "", 150);
    $message = clean_text($_POST["message"] ?? "", 3000);
    if ($first === "" || !valid_email($email) || $subject === "" || $message === "") {
        json_out(["success" => false, "message" => "Please complete all required fields."]);
    }
    $stmt = $conn->prepare("INSERT INTO contact_messages (user_id, first_name, last_name, email, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
    $uid = current_user_id();
    $stmt->bind_param("isssss", $uid, $first, $last, $email, $subject, $message);
    $stmt->execute();
    $stmt->close();
    sendEmail(MAIL_FROM, "SirChef contact: " . $subject, "<p><strong>From:</strong> " . e($first . " " . $last) . " (" . e($email) . ")</p><p>" . nl2br(e($message)) . "</p>", $email);
    json_out(["success" => true, "message" => "Message sent."]);
}

if ($action === "ingredient_search") {
    $raw = $_POST["ingredients"] ?? "";
    $unlimited = current_user_id() && ($_POST["search_scope"] ?? "") === "dashboard";
    $limit = $unlimited ? 0 : (current_user_id() ? 15 : 5);
    $allParts = ingredient_search_parts($raw, 0);
    if (!$allParts) {
        json_out(["success" => false, "message" => "Add at least one ingredient."]);
    }
    if ($limit > 0 && count($allParts) > $limit) {
        json_out(["success" => false, "message" => "Ingredient limit reached: $limit maximum on this page.", "ingredient_limit" => $limit]);
    }
    $parts = $limit > 0 ? array_slice($allParts, 0, $limit) : $allParts;
    $searchedCount = count($parts);

    $sql = "SELECT r.*,
                GROUP_CONCAT(ri.ingredient_name ORDER BY ri.id SEPARATOR '||') AS ingredient_names
            FROM recipes r
            LEFT JOIN recipe_ingredients ri ON ri.recipe_id = r.id
            WHERE r.status = 'published'
            GROUP BY r.id";
    $result = $conn->query($sql);
    if (!$result) {
        json_out(["success" => false, "message" => "Recipe search is temporarily unavailable."]);
    }
    $recipes = [];
    while ($row = $result->fetch_assoc()) {
        $recipeIngredients = array_filter(explode("||", (string) ($row["ingredient_names"] ?? "")));
        $score = score_recipe_for_ingredients($parts, $recipeIngredients);
        if ($score["matched_count"] <= 0) {
            continue;
        }
        unset($row["ingredient_names"]);
        $row["matched_count"] = $score["matched_count"];
        $row["total_count"] = $score["total_count"];
        $row["searched_count"] = $searchedCount;
        $row["match_score"] = $score["match_score"];
        $row["quality_score"] = $score["quality_score"];
        $row["coverage_score"] = $score["coverage_score"];
        $row["recipe_coverage"] = $score["recipe_coverage"];
        $recipes[] = $row;
    }

    usort($recipes, function ($a, $b) {
        return [$b["matched_count"], $b["quality_score"], $b["recipe_coverage"], -$b["total_count"], $b["search_count"], strtotime($b["created_at"])]
            <=> [$a["matched_count"], $a["quality_score"], $a["recipe_coverage"], -$a["total_count"], $a["search_count"], strtotime($a["created_at"])];
    });
    $recipes = array_slice($recipes, 0, 12);
    foreach ($recipes as $row) {
        $rid = (int) $row["id"];
        $conn->query("UPDATE recipes SET search_count = search_count + 1 WHERE id = $rid");
    }

    log_activity($conn, current_user_id(), "ingredient_search", "recipe", null, implode(", ", array_column($parts, "label")));
    json_out(["success" => true, "recipes" => $recipes, "guest_limit" => $limit, "ingredient_limit" => $limit]);
}

if (in_array($action, ["like", "favorite"], true)) {
    $uid = require_ajax_login();
    $targetType = $_POST["target_type"] ?? "recipe";
    $targetId = (int) ($_POST["target_id"] ?? 0);
    if ($targetId <= 0) {
        json_out(["success" => false, "message" => "Invalid target."]);
    }
    $table = $action === "like" ? "likes" : "favorites";
    $idColumn = $targetType === "post" ? "post_id" : "recipe_id";
    $stmt = $conn->prepare("SELECT id FROM $table WHERE user_id = ? AND $idColumn = ?");
    $stmt->bind_param("ii", $uid, $targetId);
    $stmt->execute();
    $stmt->bind_result($existingId);
    $exists = $stmt->fetch();
    $stmt->close();
    if ($exists) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->bind_param("i", $existingId);
        $stmt->execute();
        $stmt->close();
        $active = false;
    } else {
        $stmt = $conn->prepare("INSERT INTO $table (user_id, $idColumn) VALUES (?, ?)");
        $stmt->bind_param("ii", $uid, $targetId);
        $stmt->execute();
        $stmt->close();
        $active = true;
    }
    log_activity($conn, $uid, $action, $targetType, $targetId, $active ? "added" : "removed");
    json_out(["success" => true, "active" => $active]);
}

if ($action === "rate_recipe") {
    $uid = require_ajax_login();
    $recipeId = (int) ($_POST["recipe_id"] ?? 0);
    $rating = max(1, min(5, (int) ($_POST["rating"] ?? 0)));
    $feedback = clean_text($_POST["feedback"] ?? "", 1200);
    $stmt = $conn->prepare("INSERT INTO recipe_ratings (user_id, recipe_id, rating, feedback) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), feedback = VALUES(feedback), updated_at = NOW()");
    $stmt->bind_param("iiis", $uid, $recipeId, $rating, $feedback);
    $stmt->execute();
    $stmt->close();
    log_activity($conn, $uid, "rate_recipe", "recipe", $recipeId, (string) $rating);
    json_out(["success" => true, "message" => "Rating saved."]);
}

if ($action === "comment_recipe") {
    $uid = require_ajax_login();
    $recipeId = (int) ($_POST["recipe_id"] ?? 0);
    $comment = clean_text($_POST["comment"] ?? "", 1200);
    if ($recipeId <= 0 || $comment === "") {
        json_out(["success" => false, "message" => "Write a comment first."]);
    }
    $stmt = $conn->prepare("INSERT INTO recipe_comments (user_id, recipe_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $uid, $recipeId, $comment);
    $stmt->execute();
    $stmt->close();
    log_activity($conn, $uid, "comment_recipe", "recipe", $recipeId, $comment);
    json_out(["success" => true, "message" => "Comment posted."]);
}

if ($action === "follow") {
    $uid = require_ajax_login();
    $followingId = (int) ($_POST["following_id"] ?? 0);
    if ($followingId <= 0 || $followingId === $uid) {
        json_out(["success" => false, "message" => "Invalid user."]);
    }
    $stmt = $conn->prepare("INSERT IGNORE INTO follows (follower_id, following_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $uid, $followingId);
    $stmt->execute();
    $active = $stmt->affected_rows > 0;
    $stmt->close();
    if (!$active) {
        $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
        $stmt->bind_param("ii", $uid, $followingId);
        $stmt->execute();
        $stmt->close();
    }
    $stmt = $conn->prepare("SELECT COUNT(*) FROM follows WHERE following_id = ?");
    $stmt->bind_param("i", $followingId);
    $stmt->execute();
    $stmt->bind_result($followersCount);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
    $stmt->bind_param("i", $followingId);
    $stmt->execute();
    $stmt->bind_result($followingCount);
    $stmt->fetch();
    $stmt->close();

    log_activity($conn, $uid, "follow", "user", $followingId, $active ? "followed" : "unfollowed");
    json_out([
        "success" => true,
        "active" => $active,
        "followers_count" => (int) $followersCount,
        "following_count" => (int) $followingCount
    ]);
}

if ($action === "share_post" || $action === "share_recipe") {
    $uid = require_ajax_login();
    $uploadPath = null;
    $uploadMediaType = null;
    if (isset($_FILES["media"]) && $_FILES["media"]["error"] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES["media"]["error"] !== UPLOAD_ERR_OK) {
            $uploadLimit = ini_size_to_bytes((string) ini_get("upload_max_filesize"));
            json_out(["success" => false, "message" => "The upload failed. Try a file under " . human_bytes($uploadLimit ?: 15 * 1024 * 1024) . "."]);
        }
        $appUploadLimit = 50 * 1024 * 1024;
        if ($_FILES["media"]["size"] > $appUploadLimit) {
            json_out(["success" => false, "message" => "Videos and photos must be " . human_bytes($appUploadLimit) . " or smaller."]);
        }

        $allowed = [
            "jpg" => ["kind" => "image", "mimes" => ["image/jpeg"]],
            "jpeg" => ["kind" => "image", "mimes" => ["image/jpeg"]],
            "png" => ["kind" => "image", "mimes" => ["image/png"]],
            "webp" => ["kind" => "image", "mimes" => ["image/webp"]],
            "mp4" => ["kind" => "video", "mimes" => ["video/mp4", "application/mp4", "application/octet-stream"]],
            "m4v" => ["kind" => "video", "mimes" => ["video/mp4", "video/x-m4v", "application/octet-stream"]],
            "webm" => ["kind" => "video", "mimes" => ["video/webm", "application/octet-stream"]],
            "mov" => ["kind" => "video", "mimes" => ["video/quicktime", "video/mp4", "application/octet-stream"]]
        ];
        $originalExt = strtolower(pathinfo($_FILES["media"]["name"], PATHINFO_EXTENSION));
        if (!isset($allowed[$originalExt])) {
            json_out(["success" => false, "message" => "Unsupported upload. Use JPG, PNG, WebP, MP4, M4V, MOV, or WebM."]);
        }
        $type = function_exists("mime_content_type") ? (@mime_content_type($_FILES["media"]["tmp_name"]) ?: "") : "";
        $isExpectedMime = $type === "" || in_array($type, $allowed[$originalExt]["mimes"], true) || ($allowed[$originalExt]["kind"] === "video" && strncmp($type, "video/", 6) === 0);
        if (!$isExpectedMime && $allowed[$originalExt]["kind"] === "image") {
            json_out(["success" => false, "message" => "That file does not look like a supported photo or video."]);
        }

        $name = "upload_" . time() . "_" . random_int(1000, 9999) . "." . ($originalExt === "jpeg" ? "jpg" : $originalExt);
        if (!move_uploaded_file($_FILES["media"]["tmp_name"], __DIR__ . "/../Assets/" . $name)) {
            json_out(["success" => false, "message" => "Could not save the upload. Please try again."]);
        }
        $uploadPath = public_upload_path($name);
        $uploadMediaType = $allowed[$originalExt]["kind"];
    }

    if ($action === "share_post") {
        $content = clean_text($_POST["content"] ?? "", 3000);
        if ($content === "" && !$uploadPath) {
            json_out(["success" => false, "message" => "Write something or attach a photo/video to share."]);
        }
        $stmt = $conn->prepare("INSERT INTO user_posts (user_id, content, post_type) VALUES (?, ?, 'thought')");
        $stmt->bind_param("is", $uid, $content);
        $stmt->execute();
        $postId = $stmt->insert_id;
        $stmt->close();
        if ($uploadPath) {
            $stmt = $conn->prepare("INSERT INTO post_media (post_id, media_type, media_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $postId, $uploadMediaType, $uploadPath);
            $stmt->execute();
            $stmt->close();
        }
        log_activity($conn, $uid, "share_post", "post", $postId, "Post shared.");
        json_out(["success" => true, "message" => "Post shared."]);
    }

    $title = clean_text($_POST["title"] ?? "", 160);
    $duration = max(1, (int) ($_POST["duration"] ?? 0));
    $difficulty = ucfirst(strtolower(clean_text($_POST["difficulty"] ?? "", 20)));
    $cuisine = clean_text($_POST["cuisine"] ?? "", 80);
    $ingredients = clean_text($_POST["ingredients"] ?? "", 4000);
    $instructions = clean_text($_POST["instructions"] ?? "", 8000);
    $youtube = trim($_POST["youtube_url"] ?? "");
    if ($title === "" || $duration <= 0 || !in_array($difficulty, ["Easy", "Medium", "Hard"], true) || $ingredients === "" || $instructions === "") {
        json_out(["success" => false, "message" => "Complete all required recipe fields."]);
    }
    ensure_user_posted_recipes_table($conn);
    $image = $uploadPath ?: "../Assets/chicken adobo.jpg";
    $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, description, duration_minutes, difficulty, cuisine, category, image, youtube_url, instructions, source_type, status) VALUES (?, ?, ?, ?, ?, ?, 'Dishes', ?, ?, ?, 'user', 'published')");
    $description = "Shared by the SirChef community.";
    $stmt->bind_param("ississsss", $uid, $title, $description, $duration, $difficulty, $cuisine, $image, $youtube, $instructions);
    $stmt->execute();
    $recipeId = $stmt->insert_id;
    $stmt->close();
    foreach (preg_split("/[\r\n,]+/", $ingredients) as $ing) {
        $ing = clean_text($ing, 120);
        if ($ing !== "") {
            $stmt = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_name) VALUES (?, ?)");
            $stmt->bind_param("is", $recipeId, $ing);
            $stmt->execute();
            $stmt->close();
        }
    }
    $stmt = $conn->prepare("INSERT INTO user_posted_recipes (recipe_id, user_id, title, description, duration_minutes, difficulty, cuisine, category, image, youtube_url, ingredients, instructions, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Dishes', ?, ?, ?, ?, 'published')");
    $stmt->bind_param("iississssss", $recipeId, $uid, $title, $description, $duration, $difficulty, $cuisine, $image, $youtube, $ingredients, $instructions);
    $stmt->execute();
    $stmt->close();
    log_activity($conn, $uid, "share_recipe", "recipe", $recipeId, "Recipe shared.");
    json_out(["success" => true, "message" => "Recipe shared.", "recipe_id" => $recipeId]);
}

if ($action === "send_private_message") {
    $uid = require_ajax_login();
    $receiverId = (int) ($_POST["receiver_id"] ?? 0);
    $message = clean_text($_POST["message"] ?? "", 2000);
    if ($receiverId <= 0 || $receiverId === $uid) {
        json_out(["success" => false, "message" => "Choose a person to message."]);
    }
    if ($message === "") {
        json_out(["success" => false, "message" => "Write a message first."]);
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $receiverId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();
        json_out(["success" => false, "message" => "That user was not found."]);
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO messages (group_id, sender_id, receiver_id, message) VALUES (NULL, ?, ?, ?)");
    $stmt->bind_param("iis", $uid, $receiverId, $message);
    $stmt->execute();
    $messageId = $stmt->insert_id;
    $stmt->close();

    log_activity($conn, $uid, "send_private_message", "user", $receiverId, "Private message sent.");
    json_out([
        "success" => true,
        "message" => "Message sent.",
        "id" => $messageId,
        "message_text" => $message,
        "created_at_label" => date("M j, g:i A")
    ]);
}

json_out(["success" => false, "message" => "Unknown action."]);
?>
