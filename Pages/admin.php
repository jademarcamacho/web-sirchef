<?php
session_start();
require_once "db.php";
require_once "helpers.php";

// If no admin exists yet, the first logged-in user who opens this page becomes admin.
$adminCount = (int) $conn->query("SELECT COUNT(*) total FROM users WHERE role = 'admin'")->fetch_assoc()["total"];
require_admin($conn);
ensure_recipe_views_column($conn);

$message = "";
$messageType = "success";

function admin_recipe_image_upload(?array $file, string &$error): string {
    if (!$file || ($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return "../Assets/chicken adobo.jpg";
    }
    if (($file["error"] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $error = "The recipe image upload failed. Please choose another photo.";
        return "";
    }
    if (($file["size"] ?? 0) > 5 * 1024 * 1024) {
        $error = "Recipe photos must be 5MB or smaller.";
        return "";
    }

    $allowed = [
        "jpg" => ["image/jpeg"],
        "jpeg" => ["image/jpeg"],
        "png" => ["image/png"],
        "webp" => ["image/webp"]
    ];
    $originalExt = strtolower(pathinfo((string) ($file["name"] ?? ""), PATHINFO_EXTENSION));
    if (!isset($allowed[$originalExt])) {
        $error = "Use a JPG, PNG, or WebP image for the recipe photo.";
        return "";
    }

    $type = function_exists("mime_content_type") ? (@mime_content_type($file["tmp_name"]) ?: "") : "";
    if ($type !== "" && !in_array($type, $allowed[$originalExt], true)) {
        $error = "That file does not look like a valid recipe photo.";
        return "";
    }

    $uploadDir = realpath(__DIR__ . "/../Assets");
    if (!$uploadDir) {
        $error = "The Assets folder could not be found.";
        return "";
    }

    $name = "recipe_" . time() . "_" . random_int(1000, 9999) . "." . ($originalExt === "jpeg" ? "jpg" : $originalExt);
    if (!move_uploaded_file($file["tmp_name"], $uploadDir . DIRECTORY_SEPARATOR . $name)) {
        $error = "Could not save the recipe photo. Please try again.";
        return "";
    }
    return public_upload_path($name);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["admin_action"] ?? "";

    if ($action === "add_recipe") {
        $title = clean_text($_POST["title"] ?? "", 180);
        $description = clean_text($_POST["description"] ?? "", 800);
        $duration = max(1, (int) ($_POST["duration_minutes"] ?? 0));
        $difficulty = $_POST["difficulty"] ?? "Easy";
        $cuisine = clean_text($_POST["cuisine"] ?? "", 80);
        $ingredients = clean_text($_POST["ingredients"] ?? "", 4000);
        $instructions = clean_text($_POST["instructions"] ?? "", 8000);
        $youtube = trim($_POST["youtube_url"] ?? "");

        if ($title === "" || $description === "" || $cuisine === "" || $ingredients === "" || $instructions === "" || !in_array($difficulty, ["Easy", "Medium", "Hard"], true)) {
            $message = "Please complete all recipe fields.";
            $messageType = "danger";
        } else {
            $uploadError = "";
            $image = admin_recipe_image_upload($_FILES["recipe_image"] ?? null, $uploadError);
            if ($uploadError !== "") {
                $message = $uploadError;
                $messageType = "danger";
            } else {
                $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, description, duration_minutes, difficulty, cuisine, category, image, youtube_url, instructions, source_type, status) VALUES (?, ?, ?, ?, ?, ?, 'Dishes', ?, ?, ?, 'admin', 'published')");
                $uid = current_user_id();
                $stmt->bind_param("ississsss", $uid, $title, $description, $duration, $difficulty, $cuisine, $image, $youtube, $instructions);
                $stmt->execute();
                $recipeId = $stmt->insert_id;
                $stmt->close();

                foreach (preg_split("/[\r\n,]+/", $ingredients) as $ingredient) {
                    $ingredient = clean_text($ingredient, 120);
                    if ($ingredient !== "") {
                        $stmt = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_name) VALUES (?, ?)");
                        $stmt->bind_param("is", $recipeId, $ingredient);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
                log_activity($conn, current_user_id(), "admin_add_recipe", "recipe", $recipeId, $title);
                $message = "Recipe added successfully.";
            }
        }
    }

    if ($action === "promote_user") {
        $targetId = (int) ($_POST["user_id"] ?? 0);
        if ($targetId > 0) {
            $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $stmt->bind_param("i", $targetId);
            $stmt->execute();
            $stmt->close();
            log_activity($conn, current_user_id(), "promote_user", "user", $targetId, "Promoted to admin.");
            $message = "User promoted to admin.";
        }
    }
}

$counts = [
    "users" => (int) $conn->query("SELECT COUNT(*) total FROM users")->fetch_assoc()["total"],
    "recipes" => (int) $conn->query("SELECT COUNT(*) total FROM recipes")->fetch_assoc()["total"],
    "messages" => (int) $conn->query("SELECT COUNT(*) total FROM contact_messages")->fetch_assoc()["total"],
    "views" => (int) $conn->query("SELECT COALESCE(SUM(views),0) total FROM recipes")->fetch_assoc()["total"]
];
$users = $conn->query("SELECT id, first_name, last_name, email, role, is_verified, created_at FROM users ORDER BY created_at DESC LIMIT 30")->fetch_all(MYSQLI_ASSOC);
$contacts = $conn->query("SELECT first_name, last_name, email, subject, message, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 8")->fetch_all(MYSQLI_ASSOC);
$adminRecipes = $conn->query("SELECT id, title, cuisine, difficulty, image, views, created_at FROM recipes ORDER BY created_at DESC LIMIT 30")->fetch_all(MYSQLI_ASSOC);
$recipesByCuisine = $conn->query("SELECT cuisine label, COUNT(*) total FROM recipes GROUP BY cuisine ORDER BY total DESC, cuisine ASC LIMIT 8")->fetch_all(MYSQLI_ASSOC);
$recipesByDifficulty = $conn->query("SELECT difficulty label, COUNT(*) total FROM recipes GROUP BY difficulty ORDER BY FIELD(difficulty, 'Easy', 'Medium', 'Hard')")->fetch_all(MYSQLI_ASSOC);
$topViewedRecipes = $conn->query("SELECT title label, views total FROM recipes ORDER BY views DESC, created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);
$cuisines = ["Filipino","Italian","Korean","Japanese","Chinese","Thai","Mexican","Indian","American","French","Spanish","Vietnamese","Mediterranean","Middle Eastern","Greek","Indonesian","Malaysian","Turkish","Brazilian","Caribbean"];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | SirChef</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/admin.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<?php include "header.php"; ?>
<main class="admin-page">
  <section class="admin-hero">
    <div>
      <span class="admin-kicker"><i class="fas fa-user-shield"></i> SirChef Control Room</span>
      <h1>Admin Dashboard</h1>
      <p>Manage recipes, users, contact messages, recipe photos, and live viewing activity for SirChef.</p>
    </div>
    <a href="dashboard.php" class="admin-hero-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  </section>

  <?php if ($message): ?><div class="alert alert-<?= e($messageType) ?>"><?= e($message) ?></div><?php endif; ?>

  <section class="admin-stats">
    <div><strong><?= $counts["users"] ?></strong><span>Users</span></div>
    <div><strong><?= $counts["recipes"] ?></strong><span>Recipes</span></div>
    <div><strong><?= $counts["messages"] ?></strong><span>Messages</span></div>
    <div><strong><?= $counts["views"] ?></strong><span>Recipe Views</span></div>
  </section>

  <section class="admin-card admin-analytics">
    <div class="admin-card-head">
      <h2><i class="fas fa-chart-column"></i> Graphs</h2>
      <span class="status-pill ready">Live data</span>
    </div>
    <div class="chart-grid">
      <div class="chart-panel">
        <h3>Recipes by Cuisine</h3>
        <canvas id="cuisineChart" height="220"></canvas>
      </div>
      <div class="chart-panel">
        <h3>Difficulty Mix</h3>
        <canvas id="difficultyChart" height="220"></canvas>
      </div>
      <div class="chart-panel chart-panel-wide">
        <h3>Top Recipe Views</h3>
        <canvas id="viewsChart" height="220"></canvas>
      </div>
    </div>
  </section>

  <section class="admin-grid">
    <article class="admin-card">
      <h2><i class="fas fa-bowl-food"></i> Add Recipe</h2>
      <form method="post" class="recipe-admin-form" enctype="multipart/form-data">
        <input type="hidden" name="admin_action" value="add_recipe">
        <input class="form-control" name="title" placeholder="Recipe title" required>
        <label for="recipeImageInput">Recipe Picture</label>
        <input class="form-control" id="recipeImageInput" type="file" name="recipe_image" accept="image/jpeg,image/png,image/webp">
        <div class="admin-upload-preview" id="recipeImagePreview">
          <i class="fas fa-image"></i>
          <span>No photo selected yet</span>
        </div>
        <textarea class="form-control" name="description" rows="2" placeholder="Short description" required></textarea>
        <div class="admin-two">
          <input class="form-control" type="number" min="1" name="duration_minutes" placeholder="Minutes" required>
          <select class="form-select" name="difficulty" required><option>Easy</option><option>Medium</option><option>Hard</option></select>
        </div>
        <select class="form-select" name="cuisine" required>
          <?php foreach ($cuisines as $cuisine): ?><option><?= e($cuisine) ?></option><?php endforeach; ?>
        </select>
        <input class="form-control" name="youtube_url" placeholder="YouTube tutorial URL">
        <textarea class="form-control" name="ingredients" rows="3" placeholder="Ingredients, separated by comma or new line" required></textarea>
        <textarea class="form-control" name="instructions" rows="4" placeholder="Instructions" required></textarea>
        <button class="btn-admin"><i class="fas fa-plus"></i> Add Recipe</button>
      </form>
    </article>

    <article class="admin-card">
      <h2><i class="fas fa-inbox"></i> Contact Messages</h2>
      <div class="message-list">
        <?php foreach ($contacts as $contact): ?>
          <div class="message-item">
            <strong><?= e($contact["subject"]) ?></strong>
            <span><?= e($contact["first_name"] . " " . $contact["last_name"]) ?> &middot; <?= e($contact["email"]) ?></span>
            <p><?= e($contact["message"]) ?></p>
          </div>
        <?php endforeach; ?>
        <?php if (!$contacts): ?><p class="admin-note">No messages yet.</p><?php endif; ?>
      </div>
    </article>
  </section>

  <section class="admin-grid lower">
    <article class="admin-card">
      <h2><i class="fas fa-users"></i> Users</h2>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Verified</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= e($user["first_name"] . " " . $user["last_name"]) ?></td>
              <td><?= e($user["email"]) ?></td>
              <td><span class="role-chip"><?= e($user["role"]) ?></span></td>
              <td><?= $user["is_verified"] ? "Yes" : "No" ?></td>
              <td>
                <?php if ($user["role"] !== "admin"): ?>
                  <form method="post"><input type="hidden" name="admin_action" value="promote_user"><input type="hidden" name="user_id" value="<?= (int) $user["id"] ?>"><button class="mini-btn">Make Admin</button></form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </article>

    <article class="admin-card">
      <h2><i class="fas fa-book-open"></i> Recipes</h2>
      <div class="recipe-row-list recipe-admin-list">
        <?php foreach ($adminRecipes as $recipe): ?>
          <a class="recipe-row-card" href="recipe_detail.php?id=<?= (int) $recipe["id"] ?>" target="_blank">
            <img class="recipe-admin-thumb" src="<?= e($recipe["image"]) ?>" alt="<?= e($recipe["title"]) ?>">
            <span class="recipe-row-main">
              <strong><?= e($recipe["title"]) ?></strong>
              <span><?= e($recipe["cuisine"]) ?> &middot; <?= e($recipe["difficulty"]) ?> &middot; <?= e(date("M j, Y", strtotime($recipe["created_at"]))) ?></span>
            </span>
            <span class="recipe-view-pill"><i class="fas fa-eye"></i> <?= (int) $recipe["views"] ?></span>
          </a>
        <?php endforeach; ?>
        <?php if (!$adminRecipes): ?><p class="admin-note">No recipes yet.</p><?php endif; ?>
      </div>
    </article>
  </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const cuisineRows = <?= json_encode($recipesByCuisine, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const difficultyRows = <?= json_encode($recipesByDifficulty, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const viewsRows = <?= json_encode($topViewedRecipes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const chartPalette = ['#e76f51', '#ffd166', '#4ecdc4', '#2d3047', '#f4a261', '#87a96b', '#cba409', '#6c63ff'];

function chartLabels(rows, fallback) {
  return rows.length ? rows.map(row => row.label) : [fallback];
}

function chartValues(rows) {
  return rows.length ? rows.map(row => Number(row.total) || 0) : [0];
}

if (window.Chart) {
  Chart.defaults.font.family = 'Poppins, sans-serif';
  Chart.defaults.color = '#555';

  new Chart(document.getElementById('cuisineChart'), {
    type: 'bar',
    data: {
      labels: chartLabels(cuisineRows, 'No recipes'),
      datasets: [{ data: chartValues(cuisineRows), backgroundColor: chartPalette, borderRadius: 8 }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
  });

  new Chart(document.getElementById('difficultyChart'), {
    type: 'doughnut',
    data: {
      labels: chartLabels(difficultyRows, 'No recipes'),
      datasets: [{ data: chartValues(difficultyRows), backgroundColor: chartPalette.slice(0, 3), borderWidth: 0 }]
    },
    options: { cutout: '62%', plugins: { legend: { position: 'bottom' } } }
  });

  new Chart(document.getElementById('viewsChart'), {
    type: 'bar',
    data: {
      labels: chartLabels(viewsRows, 'No views'),
      datasets: [{ data: chartValues(viewsRows), backgroundColor: '#4ecdc4', borderRadius: 8 }]
    },
    options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
  });
}

const recipeImageInput = document.getElementById('recipeImageInput');
const recipeImagePreview = document.getElementById('recipeImagePreview');
if (recipeImageInput && recipeImagePreview) {
  recipeImageInput.addEventListener('change', () => {
    const file = recipeImageInput.files && recipeImageInput.files[0];
    if (!file) {
      recipeImagePreview.innerHTML = '<i class="fas fa-image"></i><span>No photo selected yet</span>';
      return;
    }
    const url = URL.createObjectURL(file);
    recipeImagePreview.innerHTML = `<img src="${url}" alt=""><span>${file.name}</span>`;
  });
}
</script>
</body>
</html>
