<?php
session_start();
require_once "db.php";
require_once "helpers.php";

ensure_recipe_views_column($conn);
$recipeId = max(1, (int) ($_GET["id"] ?? 0));
$stmt = $conn->prepare("SELECT r.*, u.first_name, u.last_name FROM recipes r LEFT JOIN users u ON u.id = r.user_id WHERE r.id = ? AND r.status = 'published'");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$recipe) {
    http_response_code(404);
    die("Recipe not found.");
}

$stmt = $conn->prepare("UPDATE recipes SET views = views + 1 WHERE id = ?");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$stmt->close();
$recipe["views"] = (int) ($recipe["views"] ?? 0) + 1;

$stmt = $conn->prepare("SELECT ingredient_name, quantity FROM recipe_ingredients WHERE recipe_id = ? ORDER BY id");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

[$avgRating, $ratingCount] = recipe_average($conn, $recipeId);

$stmt = $conn->prepare("SELECT rr.rating, rr.feedback, rr.created_at, u.first_name, u.last_name FROM recipe_ratings rr JOIN users u ON u.id = rr.user_id WHERE rr.recipe_id = ? AND rr.feedback IS NOT NULL AND rr.feedback <> '' ORDER BY rr.created_at DESC LIMIT 10");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$feedbacks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT rc.comment, rc.created_at, u.first_name, u.last_name FROM recipe_comments rc JOIN users u ON u.id = rc.user_id WHERE rc.recipe_id = ? ORDER BY rc.created_at DESC LIMIT 20");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$embed = youtube_embed_url($recipe["youtube_url"]);
$steps = array_filter(array_map("trim", preg_split("/\r\n|\n|\.\s+/", $recipe["instructions"])));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($recipe["title"]) ?> | SirChef</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/recipe_detail.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include "header.php"; ?>

<main class="detail-shell">
  <section class="detail-hero">
    <div class="container">
      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <span class="hero-badge"><i class="fas fa-utensils"></i> <?= e($recipe["cuisine"]) ?> Cuisine</span>
          <h1><?= e($recipe["title"]) ?></h1>
          <p><?= e($recipe["description"]) ?></p>
          <div class="detail-meta">
            <span><i class="fas fa-clock"></i> <?= (int) $recipe["duration_minutes"] ?> min</span>
            <span><i class="fas fa-signal"></i> <?= e($recipe["difficulty"]) ?></span>
            <span><i class="fas fa-star"></i> <?= $avgRating ?> (<?= $ratingCount ?>)</span>
            <span><i class="fas fa-eye"></i> <?= (int) $recipe["views"] ?> views</span>
            <span><i class="fas fa-user"></i> <?= $recipe["source_type"] === "user" ? e(trim(($recipe["first_name"] ?? "") . " " . ($recipe["last_name"] ?? ""))) : "SirChef" ?></span>
          </div>
          <div class="detail-actions">
            <button class="action-btn" data-action="like" data-id="<?= $recipeId ?>"><i class="far fa-heart"></i> Like</button>
            <button class="action-btn" data-action="favorite" data-id="<?= $recipeId ?>"><i class="far fa-star"></i> Favorite</button>
          </div>
        </div>
        <div class="col-lg-6">
          <img class="detail-image" src="<?= e($recipe["image"]) ?>" alt="<?= e($recipe["title"]) ?>">
        </div>
      </div>
    </div>
  </section>

  <section class="container detail-content">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="detail-card">
          <h2>Ingredients</h2>
          <ul class="ingredient-list">
            <?php foreach ($ingredients as $ing): ?>
              <li><i class="fas fa-check"></i> <?= e(trim(($ing["quantity"] ? $ing["quantity"] . " " : "") . $ing["ingredient_name"])) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="detail-card">
          <h2>Instructions</h2>
          <ol class="steps-list">
            <?php foreach ($steps as $step): ?>
              <li><?= e($step) ?>.</li>
            <?php endforeach; ?>
          </ol>
        </div>
        <?php if ($embed): ?>
          <div class="detail-card mt-4">
            <h2>YouTube Tutorial</h2>
            <div class="ratio ratio-16x9">
              <iframe src="<?= e($embed) ?>" title="YouTube tutorial" allowfullscreen></iframe>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-lg-5">
        <div class="detail-card">
          <h2>Rate and Give Feedback</h2>
          <?php if (current_user_id()): ?>
          <form id="ratingForm">
            <input type="hidden" name="action" value="rate_recipe">
            <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
            <select name="rating" class="form-select mb-3" required>
              <option value="5">5 stars</option><option value="4">4 stars</option><option value="3">3 stars</option><option value="2">2 stars</option><option value="1">1 star</option>
            </select>
            <textarea name="feedback" class="form-control mb-3" rows="3" placeholder="What did you think?"></textarea>
            <button class="btn btn-bohemian" type="submit">Submit Rating</button>
          </form>
          <hr>
          <form id="commentForm">
            <input type="hidden" name="action" value="comment_recipe">
            <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
            <textarea name="comment" class="form-control mb-3" rows="3" placeholder="Add a public comment"></textarea>
            <button class="btn btn-outline-dark" type="submit">Post Comment</button>
          </form>
          <?php else: ?>
            <p>Please log in to rate, comment, favorite, or like this recipe.</p>
          <?php endif; ?>
          <div id="detailMsg" class="small mt-3"></div>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="detail-card">
          <h2>Community Feedback</h2>
          <?php foreach ($feedbacks as $fb): ?>
            <div class="feedback-item"><strong><?= e($fb["first_name"] . " " . $fb["last_name"]) ?></strong> <span><?= (int) $fb["rating"] ?> stars</span><p><?= e($fb["feedback"]) ?></p></div>
          <?php endforeach; ?>
          <?php foreach ($comments as $comment): ?>
            <div class="feedback-item"><strong><?= e($comment["first_name"] . " " . $comment["last_name"]) ?></strong><p><?= e($comment["comment"]) ?></p></div>
          <?php endforeach; ?>
          <?php if (!$feedbacks && !$comments): ?><p>No feedback yet. Be the first cook to respond.</p><?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include "footer.php"; include "login_regis.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── Original functionality ── */
function postForm(form) {
  return fetch('backend.php', {method:'POST', body:new FormData(form)}).then(r => r.json());
}
document.querySelectorAll('.action-btn').forEach(btn => btn.addEventListener('click', function () {
  const fd = new FormData();
  fd.append('action', this.dataset.action);
  fd.append('target_type', 'recipe');
  fd.append('target_id', this.dataset.id);
  fetch('backend.php', {method:'POST', body:fd}).then(r => r.json()).then(data => {
    if (data.login_required) { bootstrap.Modal.getOrCreateInstance(document.getElementById('loginModal')).show(); return; }
    this.classList.toggle('active', data.active);
    this.querySelector('i').className = data.active ? this.querySelector('i').className.replace('far','fas') : this.querySelector('i').className.replace('fas','far');
  });
}));
['ratingForm','commentForm'].forEach(id => {
  const form = document.getElementById(id);
  if (form) form.addEventListener('submit', e => {
    e.preventDefault();
    postForm(form).then(data => {
      document.getElementById('detailMsg').textContent = data.message || 'Updated.';
      if (data.success) setTimeout(() => location.reload(), 700);
    });
  });
});

/* ── Visual enhancements ── */
(function () {
  /* 1. Scroll-reveal for cards */
  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.detail-card').forEach(function (card, i) {
    card.classList.add('will-reveal');
    card.style.transitionDelay = (i * 0.07) + 's';
    observer.observe(card);
  });

  /* 2. Magnetic glow on action buttons */
  document.querySelectorAll('.action-btn').forEach(function (btn) {
    btn.addEventListener('mousemove', function (e) {
      const r = btn.getBoundingClientRect();
      btn.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
      btn.style.setProperty('--my', ((e.clientY - r.top)  / r.height * 100) + '%');
    });
  });

  /* 3. Stagger ingredient list items in */
  const ingList = document.querySelector('.ingredient-list');
  if (ingList) {
    const ingItems = ingList.querySelectorAll('li');
    ingItems.forEach(function (li, i) {
      li.style.opacity = '0';
      li.style.transform = 'translateX(-14px)';
      li.style.transition = 'opacity .5s ease ' + (0.05 + i * 0.06) + 's, transform .5s ease ' + (0.05 + i * 0.06) + 's';
    });
    const ingObs = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting) {
        ingItems.forEach(function (li) { li.style.opacity = '1'; li.style.transform = 'translateX(0)'; });
        ingObs.disconnect();
      }
    }, { threshold: 0.15 });
    ingObs.observe(ingList);
  }

  /* 4. Stagger steps in */
  const stepsList = document.querySelector('.steps-list');
  if (stepsList) {
    const stepItems = stepsList.querySelectorAll('li');
    stepItems.forEach(function (li, i) {
      li.style.opacity = '0';
      li.style.transform = 'translateY(16px)';
      li.style.transition = 'opacity .55s ease ' + (0.08 + i * 0.07) + 's, transform .55s ease ' + (0.08 + i * 0.07) + 's';
    });
    const stepsObs = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting) {
        stepItems.forEach(function (li) { li.style.opacity = '1'; li.style.transform = 'translateY(0)'; });
        stepsObs.disconnect();
      }
    }, { threshold: 0.1 });
    stepsObs.observe(stepsList);
  }

  /* 5. Feedback items fade in */
  const fbObs = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.style.opacity = '1'; e.target.style.transform = 'translateY(0)'; }
    });
  }, { threshold: 0.15 });
  document.querySelectorAll('.feedback-item').forEach(function (item, i) {
    item.style.opacity = '0';
    item.style.transform = 'translateY(12px)';
    item.style.transition = 'opacity .5s ease ' + (i * 0.08) + 's, transform .5s ease ' + (i * 0.08) + 's';
    fbObs.observe(item);
  });
})();
</script>
</body>
</html>
