<?php
session_start();
require_once "db.php";
require_once "helpers.php";

$cuisines = ["Filipino","Italian","Korean","Japanese","Chinese","Thai","Mexican","Indian","American","French","Spanish","Vietnamese","Mediterranean","Middle Eastern","Greek","Indonesian","Malaysian","Turkish","Brazilian","Caribbean"];
$difficulty = $_GET["difficulty"] ?? "";
$cuisine = $_GET["cuisine"] ?? "";

$sql = "SELECT r.*, COALESCE(ROUND(AVG(rr.rating),1),0) avg_rating FROM recipes r LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.id WHERE r.status = 'published'";
$params = [];
$types = "";
if (in_array($difficulty, ["Easy","Medium","Hard"], true)) { $sql .= " AND r.difficulty = ?"; $params[] = $difficulty; $types .= "s"; }
if (in_array($cuisine, $cuisines, true)) { $sql .= " AND r.cuisine = ?"; $params[] = $cuisine; $types .= "s"; }
$sql .= " GROUP BY r.id ORDER BY r.created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$recipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recipes | SirChef</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/recipe.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include "header.php"; ?>

<section class="page-hero">
  <div class="container">
    <span class="hero-badge"><i class="fas fa-book-open"></i> Timeless Recipes</span>
    <h1>Find Your Next <em>SirChef</em> Dish</h1>
    <p class="hero-sub">Filter recipes by cuisine and difficulty, then open full recipe details in a new tab.</p>
    <div class="hero-stats">
      <div class="hs-item"><span class="hs-num"><?= count($recipes) ?></span><span class="hs-lbl">Showing</span></div>
      <div class="hs-item"><span class="hs-num">20</span><span class="hs-lbl">Cuisines</span></div>
      <div class="hs-item"><span class="hs-num">3</span><span class="hs-lbl">Levels</span></div>
    </div>
  </div>
</section>

<main class="recipes-section">
  <div class="container">
    <section class="recipe-options-panel">
      <div class="options-head">
        <span class="eyebrow">Browse Options</span>
        <h2 class="sec-title">Choose Your <span>Cooking Mood</span></h2>
        <a href="recipe.php" class="clear-options"><i class="fas fa-rotate-left"></i> Clear</a>
      </div>
      <div class="option-group">
        <div class="option-label"><i class="fas fa-signal"></i> Difficulty</div>
        <div class="option-pills">
          <?php foreach (["Easy","Medium","Hard"] as $item): ?>
            <?php $url = "recipe.php?" . http_build_query(array_filter(["cuisine" => $cuisine, "difficulty" => $item])); ?>
            <a class="option-pill <?= $difficulty === $item ? "active" : "" ?>" href="<?= e($url) ?>"><?= e($item) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="option-group">
        <div class="option-label"><i class="fas fa-globe-asia"></i> Cuisine Type</div>
        <div class="option-pills cuisine-pills">
          <?php foreach ($cuisines as $item): ?>
            <?php $url = "recipe.php?" . http_build_query(array_filter(["cuisine" => $item, "difficulty" => $difficulty])); ?>
            <a class="option-pill <?= $cuisine === $item ? "active" : "" ?>" href="<?= e($url) ?>"><?= e($item) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <div class="row g-4" id="recipe-grid">
      <?php foreach ($recipes as $recipe): ?>
        <?php
          $stmt = $conn->prepare("SELECT ingredient_name FROM recipe_ingredients WHERE recipe_id = ? LIMIT 5");
          $rid = (int) $recipe["id"];
          $stmt->bind_param("i", $rid);
          $stmt->execute();
          $ings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
          $stmt->close();
        ?>
        <div class="col-sm-6 col-lg-4 recipe-card-wrap visible">
          <article class="recipe-card-new">
            <div class="recipe-photo">
              <img src="<?= e($recipe["image"]) ?>" alt="<?= e($recipe["title"]) ?>" loading="lazy">
              <span class="diff-badge <?= strtolower(e($recipe["difficulty"])) ?>"><?= e($recipe["difficulty"]) ?></span>
              <span class="time-badge"><i class="fas fa-clock"></i> <?= (int) $recipe["duration_minutes"] ?> min</span>
            </div>
            <div class="recipe-body">
              <h5><?= e($recipe["title"]) ?></h5>
              <p><?= e($recipe["description"]) ?></p>
              <div class="ing-preview">
                <?php foreach ($ings as $ing): ?><span class="ing-chip"><?= e($ing["ingredient_name"]) ?></span><?php endforeach; ?>
              </div>
              <div class="recipe-mini-meta">
                <span><i class="fas fa-globe"></i> <?= e($recipe["cuisine"]) ?></span>
                <span><i class="fas fa-star"></i> <?= e((string) $recipe["avg_rating"]) ?></span>
              </div>
              <a class="btn-view" href="recipe_detail.php?id=<?= (int) $recipe["id"] ?>" target="_blank"><i class="fas fa-utensils"></i> View Recipe</a>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
      <?php if (!$recipes): ?>
        <div class="col-12"><div class="empty-state">No recipes match those filters yet.</div></div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include "footer.php"; include "login_regis.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
