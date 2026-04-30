<?php
session_start();
require_once "db.php";
require_once "helpers.php";

$featured = $conn->query("SELECT r.*, COALESCE(ROUND(AVG(rr.rating),1),0) avg_rating FROM recipes r LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.id WHERE r.status='published' AND r.source_type='admin' GROUP BY r.id ORDER BY avg_rating DESC, r.created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);
$userRecipes = $conn->query("SELECT r.*, u.first_name, u.last_name FROM recipes r JOIN users u ON u.id = r.user_id WHERE r.source_type='user' AND r.status='published' ORDER BY r.created_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SirChef | Cook With What You Have</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/index.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include "header.php"; ?>

<section class="hero-section" id="home">
  <div class="hero-particle"></div><div class="hero-particle"></div><div class="hero-particle"></div><div class="hero-particle"></div><div class="hero-particle"></div>
  <div class="container">
    <div class="row align-items-center g-5">

      <!-- Left: Hero copy -->
      <div class="col-lg-6">
        <span class="hero-badge"><i class="fas fa-seedling"></i> Less Waste, Better Meals</span>
        <h1 class="hero-title">Cook From <span class="shimmer-word">Your Kitchen</span></h1>
        <p class="hero-subtitle">SirChef matches the ingredients you already have with real recipes, then lets you favorite, rate, share, and connect with other home cooks.</p>
        <div class="mt-4 d-flex flex-wrap gap-3">
          <a href="#search" class="btn btn-bohemian btn-lg"><i class="fas fa-carrot me-2"></i> Find Recipes</a>
          <a href="#about" class="btn-hero-secondary"><i class="fas fa-arrow-down"></i><span>Learn More</span></a>
          <?php if (!current_user_id()): ?><a href="#" class="btn-hero-primary" data-bs-toggle="modal" data-bs-target="#registerModal"><i class="fas fa-user-plus"></i><span>Join Free</span></a><?php endif; ?>
        </div>
        <div class="hero-stats mt-5 d-flex gap-4 flex-wrap">
          <div class="hero-stat-item"><span class="hero-stat-number"><?= count($featured) ?>+</span><span class="hero-stat-label">Featured</span></div>
          <div class="hero-stat-divider"></div>
          <div class="hero-stat-item"><span class="hero-stat-number">20</span><span class="hero-stat-label">Cuisines</span></div>
          <div class="hero-stat-divider"></div>
          <div class="hero-stat-item"><span class="hero-stat-number"><?= current_user_id() ? "15" : "5" ?></span><span class="hero-stat-label">Search Limit</span></div>
        </div>
      </div>

      <!-- Right: Redesigned Kitchen Search -->
      <div class="col-lg-6" id="search">
        <div class="kitchen-search-card">

          <!-- Animated top bar -->
          <div class="ksc-top-bar"></div>

          <!-- Floating food icons -->
          <span class="ksc-float ksc-f1"><i class="fas fa-carrot"></i></span>
          <span class="ksc-float ksc-f2"><i class="fas fa-pepper-hot"></i></span>
          <span class="ksc-float ksc-f3"><i class="fas fa-egg"></i></span>
          <span class="ksc-float ksc-f4"><i class="fas fa-lemon"></i></span>

          <!-- Header -->
          <div class="ksc-header">
            <div class="ksc-icon-ring">
              <i class="fas fa-basket-shopping"></i>
            </div>
            <h3 class="ksc-title">What's in Your Kitchen?</h3>
            <p class="ksc-subtitle">Add your ingredients and we'll find matching recipes instantly.</p>
          </div>

          <!-- Input row -->
          <div class="ksc-input-row">
            <div class="ksc-input-wrap">
              <i class="fas fa-search ksc-search-icon"></i>
              <input type="text" class="ksc-input" id="ingredientInput" placeholder="e.g. chicken, garlic, tomato…">
            </div>
            <button class="ksc-add-btn" type="button" id="addIngredientBtn">
              <i class="fas fa-plus"></i> Add
            </button>
          </div>

          <!-- Tags area -->
          <div class="ksc-tags-area" id="ingredientTags">
            <span class="ksc-tags-empty">Your ingredients will appear here&hellip;</span>
          </div>

          <!-- Find button -->
          <button class="ksc-find-btn" id="findRecipesBtn">
            <i class="fas fa-utensils"></i>
            <span>Find Recipes</span>
            <i class="fas fa-arrow-right ksc-arrow"></i>
          </button>

          <!-- Results -->
          <div id="matchResults" class="match-results"></div>

          <!-- Footer note -->
          <p class="ksc-note">
            <i class="fas fa-circle-info"></i>
            <?= current_user_id()
              ? "Logged-in users can search up to <strong>15 ingredients</strong>."
              : "Guests preview up to <strong>5 ingredients</strong>. <a href='#' data-bs-toggle='modal' data-bs-target='#registerModal'>Join free</a> for more." ?>
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Featured Recipes -->
<section class="section-padding featured-recipes featured-recipes-home" id="recipes">
  <div class="container">
    <div class="featured-section-head">
      <span class="section-kicker"><i class="fas fa-fire"></i> Popular Picks</span>
      <h2 class="section-title">Featured Recipes</h2>
      <p>Chef-loved dishes from the SirChef recipe collection, ready to open, cook, and save.</p>
    </div>
    <div class="row g-4 featured-recipe-grid">
      <?php foreach ($featured as $recipe): ?>
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
              <div class="recipe-mini-meta">
                <span><i class="fas fa-globe"></i> <?= e($recipe["cuisine"]) ?></span>
                <span><i class="fas fa-star"></i> <?= number_format((float) $recipe["avg_rating"], 1) ?></span>
              </div>
              <a href="recipe_detail.php?id=<?= (int) $recipe["id"] ?>" target="_blank" class="btn-view"><i class="fas fa-utensils"></i> View Recipe</a>
            </div>
          </article>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- How It Works -->
<section class="section-padding how-it-works">
  <div class="container">
    <div class="text-center mb-5"><h2 class="section-title">Why SirChef Helps</h2></div>
    <div class="row g-4">
      <div class="col-md-3"><div class="step-card"><div class="step-number">1</div><h4>Less Food Waste</h4><p>Use ingredients before they expire by turning them into matched recipes.</p></div></div>
      <div class="col-md-3"><div class="step-card"><div class="step-number">2</div><h4>More Discovery</h4><p>Explore 20 cuisines from Filipino comfort food to Mediterranean favorites.</p></div></div>
      <div class="col-md-3"><div class="step-card"><div class="step-number">3</div><h4>Community</h4><p>Logged-in cooks can share posts, recipes, ratings, favorites, and follows.</p></div></div>
      <div class="col-md-3"><div class="step-card"><div class="step-number">4</div><h4>Favorite Cookbook</h4><p>Keep favorite recipes close so planning stays simple and organized.</p></div></div>
    </div>
  </div>
</section>

<!-- About -->
<section class="section-padding about-section" id="about">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="about-card">
          <div class="about-icon"><i class="fas fa-leaf"></i></div>
          <h3>For Guests and Members</h3>
          <p>Guest users can browse recipes and run limited ingredient searches. Logged-in users get more ingredient search, favorites, ratings, recipe sharing, follows, private messages, and activity history.</p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="about-card">
          <div class="about-icon"><i class="fas fa-users"></i></div>
          <h3>About the Developers</h3>
          <p>SirChef is built by seven Computer Science students who each own a clear part of the system: analysis, backend, frontend, admin logic, business process, content flow, and community support.</p>
          <div class="developer-grid">
            <button class="developer-link" data-bs-toggle="modal" data-bs-target="#devKeannu">Jarylle Keannu</button>
            <button class="developer-link" data-bs-toggle="modal" data-bs-target="#devAkeziah">Akeziah Limbo</button>
            <button class="developer-link" data-bs-toggle="modal" data-bs-target="#devKristine">Kristine Lopez</button>
            <button class="developer-link" data-bs-toggle="modal" data-bs-target="#devJade">Jade Mar Camacho</button>
            <button class="developer-link" data-bs-toggle="modal" data-bs-target="#devJohn">John Lenard Robosa</button>
            <button class="developer-link" data-bs-toggle="modal" data-bs-target="#devRei">Rei Johnric Portugaliza</button>
            <button class="developer-link" data-bs-toggle="modal" data-bs-target="#devPaolo">Paolo Ballesteros</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- User Recipes -->
<section class="section-padding featured-recipes">
  <div class="container">
    <div class="text-center"><h2 class="section-title">Recipes Created by Users</h2><p class="text-muted mb-5">Community recipes appear here after users share them.</p></div>
    <div class="row">
      <?php foreach ($userRecipes as $recipe): ?>
        <div class="col-md-4 mb-4"><div class="recipe-card"><div class="recipe-photo"><img src="<?= e($recipe["image"]) ?>" alt="<?= e($recipe["title"]) ?>"></div><div class="recipe-card-body"><h5 class="recipe-title"><?= e($recipe["title"]) ?></h5><p>By <?= e($recipe["first_name"] . " " . $recipe["last_name"]) ?></p><a class="btn-view" target="_blank" href="recipe_detail.php?id=<?= (int) $recipe["id"] ?>">View Recipe</a></div></div></div>
      <?php endforeach; ?>
      <?php if (!$userRecipes): ?><div class="col-12"><div class="about-card text-center">No user-created recipes yet. Register, verify your email, and share the first one from the dashboard.</div></div><?php endif; ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════
     JOIN FREE CTA SECTION
══════════════════════════════════════════ -->
<?php if (!current_user_id()): ?>
<section class="join-cta-section">

  <!-- Decorative food icons -->
  <span class="jc-deco jc-d1"><i class="fas fa-utensils"></i></span>
  <span class="jc-deco jc-d2"><i class="fas fa-bowl-rice"></i></span>
  <span class="jc-deco jc-d3"><i class="fas fa-carrot"></i></span>
  <span class="jc-deco jc-d4"><i class="fas fa-fire-flame-curved"></i></span>
  <span class="jc-deco jc-d5"><i class="fas fa-star"></i></span>

  <div class="container">
    <div class="join-cta-inner">

      <!-- Pill badge -->
      <span class="jc-badge"><i class="fas fa-bolt"></i> Free Forever</span>

      <!-- Heading -->
      <h2 class="jc-heading">
        Ready to Cook Smarter?<br>
        <em>Join SirChef Today</em>
      </h2>
      <p class="jc-sub">Unlock full ingredient search, favorite recipes, rate dishes, share your own creations, and connect with a community of home cooks — all for free.</p>

      <!-- Feature pills row -->
      <div class="jc-features">
        <span class="jc-feat"><i class="fas fa-magnifying-glass"></i> 15 Ingredient Search</span>
        <span class="jc-feat"><i class="fas fa-heart"></i> Save Favorites</span>
        <span class="jc-feat"><i class="fas fa-star"></i> Rate Recipes</span>
        <span class="jc-feat"><i class="fas fa-share-nodes"></i> Share Creations</span>
        <span class="jc-feat"><i class="fas fa-users"></i> Join Community</span>
      </div>

      <!-- CTA Buttons -->
      <div class="jc-buttons">
        <a href="#" class="jc-btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
          <i class="fas fa-user-plus"></i>
          <span>Create Free Account</span>
          <i class="fas fa-arrow-right jc-btn-arrow"></i>
        </a>
        <a href="#" class="jc-btn-secondary" data-bs-toggle="modal" data-bs-target="#loginModal">
          <i class="fas fa-right-to-bracket"></i>
          <span>Sign In</span>
        </a>
      </div>

      <!-- Trust note -->
      <p class="jc-trust"><i class="fas fa-shield-halved"></i> No credit card needed &middot; No spam &middot; Cancel anytime</p>

    </div>
  </div>
</section>
<?php endif; ?>

<?php
$developers = [
  ["devKeannu", "Jarylle Keannu Evangelio", "System Analyst", "../Assets/keannu.jpg", "Maps SirChef's user flows, database needs, and secure backend requirements."],
  ["devAkeziah", "Akeziah Limbo", "Back-End Developer", "../Assets/Frame 3 (37).png", "Builds recipe matching logic, backend endpoints, and MySQL feature support."],
  ["devKristine", "Kristine Lopez", "Back-End Developer", "../Assets/Frame 2 (43).png", "Handles data flow, contact features, and social interaction records."],
  ["devJade", "Jade Mar Camacho", "Admin Programmer", "../Assets/Frame 2 (41).png", "Focuses on admin logic, interface consistency, and usable Bootstrap screens."],
  ["devJohn", "John Lenard Robosa", "Business Process Manager", "../Assets/Frame 1 (60).png", "Organizes SirChef's process flow from ingredient search to community sharing."],
  ["devRei", "Rei Johnric Portugaliza", "Front-End Developer", "../Assets/Frame 2 (42).png", "Improves responsive layouts, recipe presentation, and page interactions."],
  ["devPaolo", "Paolo Ballesteros", "Front-End Developer", "../Assets/pao.jpg", "Supports user-facing screens and keeps the community experience friendly."]
];
foreach ($developers as $dev): ?>
<div class="modal fade" id="<?= e($dev[0]) ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content dev-modal">
      <div class="modal-header"><h5><?= e($dev[1]) ?></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body text-center">
        <img src="<?= e($dev[3]) ?>" alt="<?= e($dev[1]) ?>" class="dev-photo">
        <div class="dev-role"><?= e($dev[2]) ?></div>
        <p><?= e($dev[4]) ?></p>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<?php include "footer.php"; include "login_regis.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let ingredients = [];
const ingredientLimit = <?= current_user_id() ? 15 : 5 ?>;
const input = document.getElementById('ingredientInput');
const tagsContainer = document.getElementById('ingredientTags');
const matchResults = document.getElementById('matchResults');

function escHtml(value) {
  return String(value).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
}

function renderTags() {
  if (!ingredients.length) {
    tagsContainer.innerHTML = '<span class="ksc-tags-empty">Your ingredients will appear here&hellip;</span>';
    return;
  }
  tagsContainer.innerHTML = ingredients.map((i, index) =>
    `<span class="ingredient-tag">${escHtml(i)}<button type="button" data-remove-index="${index}">&times;</button></span>`
  ).join('') + `<button type="button" class="ksc-clear-btn" id="clearIngredientsBtn"><i class="fas fa-basket-shopping"></i><span>Clear all</span></button>`;
  tagsContainer.querySelectorAll('[data-remove-index]').forEach(btn => {
    btn.addEventListener('click', () => removeIngredientAt(Number(btn.dataset.removeIndex)));
  });
  document.getElementById('clearIngredientsBtn').addEventListener('click', clearIngredients);
}

function addIngredient() {
  const value = input.value.trim().toLowerCase();
  if (ingredients.length >= ingredientLimit && value && !ingredients.includes(value)) {
    matchResults.innerHTML = `<div class="match-empty">Ingredient limit reached: ${ingredientLimit} maximum on this page.</div>`;
    input.value = '';
    return false;
  }
  if (value && !ingredients.includes(value)) {
    ingredients.push(value);
    input.value = '';
    renderTags();
    input.focus();
  }
  return true;
}

function removeIngredientAt(index) {
  ingredients = ingredients.filter((_, i) => i !== index);
  renderTags();
}

function clearIngredients() {
  ingredients = [];
  input.value = '';
  matchResults.innerHTML = '';
  renderTags();
  input.focus();
}

document.getElementById('addIngredientBtn').addEventListener('click', addIngredient);
input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addIngredient(); } });

document.getElementById('findRecipesBtn').addEventListener('click', () => {
  const pending = input.value.trim().toLowerCase();
  if (pending && !ingredients.includes(pending) && addIngredient() === false) return;
  if (!ingredients.length) {
    matchResults.innerHTML = '<div class="match-empty">Add at least one ingredient.</div>';
    return;
  }
  const btn = document.getElementById('findRecipesBtn');
  btn.classList.add('ksc-loading');
  const fd = new FormData();
  fd.append('action', 'ingredient_search');
  fd.append('ingredients', ingredients.join(','));
  fd.append('search_scope', 'index');
  fetch('backend.php', {method:'POST', body:fd}).then(r => r.json()).then(data => {
    btn.classList.remove('ksc-loading');
    const box = matchResults;
    if (!data.success) { box.innerHTML = `<div class="match-empty">${data.message}</div>`; return; }
    box.innerHTML = data.recipes.length ? data.recipes.map(r => {
      const searched = Number(r.searched_count ?? ingredients.length);
      const pct = Math.round(Number(r.match_score ?? (Number(r.matched_count) / Math.max(1, searched))) * 100);
      return `<a class="match-card" target="_blank" href="recipe_detail.php?id=${r.id}">
        <img src="${r.image}" alt="${r.title}">
        <div class="match-info">
          <div class="match-topline"><strong>${r.title}</strong><span>${pct}% match</span></div>
          <p>${r.description || 'Open the full recipe details.'}</p>
          <div class="match-meta">
            <span><i class="fas fa-clock"></i> ${r.duration_minutes} min</span>
            <span><i class="fas fa-signal"></i> ${r.difficulty}</span>
            <span><i class="fas fa-globe"></i> ${r.cuisine}</span>
          </div>
          <div class="match-bar"><i style="width:${Math.min(100, pct)}%"></i></div>
          <small>${r.matched_count}/${searched} searched ingredients matched</small>
        </div>
      </a>`;
    }).join('') : '<div class="match-empty">No close recipe matches yet.</div>';
  }).catch(() => { btn.classList.remove('ksc-loading'); });
});
</script>
</body>
</html>
