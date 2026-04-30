<?php
session_start();
include 'db.php';
?>

<!doctype html>
<html lang="en">
  <head>
    <base target="_self" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About | SirChef</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/about.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet" />

    <style>
      
    </style>
  </head>
  <body>
    <?php include 'header.php'; ?>

    <section class="page-hero">
      <i class="fas fa-question    gh g1"></i>
      <i class="fas fa-fire        gh g2"></i>
      <i class="fas fa-leaf        gh g3"></i>
      <i class="fas fa-utensils    gh g4"></i>
      <i class="fas fa-paper-plane gh g5"></i>
      <div class="container">
        <h1>Cooking Made <em>Smarter,</em><br>Waste Made Less</h1>
        <p class="hero-sub">SirChef is your ingredient-first cooking companion — built to help you create amazing meals with what you already have at home.</p>
        <div class="hero-stats">
          <div class="hs-item"><span class="hs-num">10K+</span><span class="hs-lbl">Recipes</span></div>
          <div class="hs-item"><span class="hs-num">50K+</span><span class="hs-lbl">Happy Cooks</span></div>
          <div class="hs-item"><span class="hs-num">120+</span><span class="hs-lbl">Cuisines</span></div>
        </div>
      </div>
    </section>

    <section class="body-area" id="about">
      <div class="container">
        <div class="text-center mb-5 reveal">
          <span class="eyebrow">Who We Are</span>
          <h2 class="sec-title mb-0">About <span>SirChef</span></h2>
        </div>
        <div class="row g-4">
          <div class="col-lg-6 reveal-left">
            <div class="info-card">
              <div class="card-seq">01</div>
              <div class="card-icon"><i class="fas fa-seedling"></i></div>
              <h3>Our Story</h3>
              <p>SirChef was founded in 2023 with a simple mission: help people cook delicious meals with what they already have. Great cooking starts with creativity, not a grocery run.</p>
              <p>Our platform uses smart algorithms to match your ingredients with thousands of curated recipes from around the world — cutting food waste and grocery bills at the same time.</p>
              <p>We partner with food sustainability organisations and donate a portion of profits to hunger relief programs worldwide.</p>
              <div class="mt-3">
                <span class="chip">Food Technology</span>
                <span class="chip">Sustainability</span>
                <span class="chip">Culinary Arts</span>
                <span class="chip">Zero Food Waste</span>
              </div>
            </div>
          </div>
          <div class="col-lg-6 reveal-right">
            <div class="info-card">
              <div class="card-seq">02</div>
              <div class="card-icon"><i class="fas fa-bullseye"></i></div>
              <h3>Our Mission</h3>
              <p>We exist to bridge the gap between what's in your fridge and what ends up on your table. Every year millions of tons of food go to waste because people don't know what to cook.</p>
              <p>SirChef transforms leftover vegetables, pantry staples, and overlooked ingredients into inspired, restaurant-quality dishes.</p>
              <p>Our vision extends beyond recipes: a global community of mindful cooks passionate about flavour, sustainability, and sharing great meals.</p>
              <div class="mt-3">
                <span class="chip">Reduce Food Waste</span>
                <span class="chip">Community</span>
                <span class="chip">Inspiration</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="how-strip">
      <div class="container">
        <div class="text-center mb-5 reveal">
          <span class="eyebrow">Simple Process</span>
          <h2 class="sec-title mb-0">How <span>SirChef</span> Works</h2>
        </div>
        <div class="row g-4">
          <div class="col-md-4 reveal" style="transition-delay:.1s">
            <div class="step-card">
              <div class="step-num">01</div>
              <div class="step-icon"><i class="fas fa-carrot"></i></div>
              <h4>Add Your Ingredients</h4>
              <p>Tell us what's in your fridge, pantry, or garden — we accept anything from common staples to unusual leftovers.</p>
            </div>
          </div>
          <div class="col-md-4 reveal" style="transition-delay:.22s">
            <div class="step-card">
              <div class="step-num">02</div>
              <div class="step-icon"><i class="fas fa-wand-magic-sparkles"></i></div>
              <h4>Get Matched Recipes</h4>
              <p>Our algorithm scans 10,000+ recipes across 120 cuisines and surfaces the best matches in seconds.</p>
            </div>
          </div>
          <div class="col-md-4 reveal" style="transition-delay:.34s">
            <div class="step-card">
              <div class="step-num">03</div>
              <div class="step-icon"><i class="fas fa-bowl-food"></i></div>
              <h4>Cook &amp; Enjoy</h4>
              <p>Follow step-by-step instructions, save your favourites, and share your creations with the SirChef community.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="body-area" style="padding-top: 0;">
      <div class="container">
        <div class="row g-5 align-items-start">
          <div class="col-lg-6 reveal-left">
            <span class="eyebrow">What We Stand For</span>
            <h2 class="sec-title">Our Core <span>Values</span></h2>
            <div class="val-row"><div class="val-dot"></div><div><h5>Sustainability First</h5><p>Every feature we build is designed with the planet in mind. Reducing food waste isn't a side effect — it's the whole point.</p></div></div>
            <div class="val-row"><div class="val-dot"></div><div><h5>Accessibility for All</h5><p>Great food shouldn't be a luxury. SirChef stays free at its core so anyone can cook well regardless of budget or skill.</p></div></div>
            <div class="val-row"><div class="val-dot"></div><div><h5>Community &amp; Connection</h5><p>Food is a universal language. We nurture a warm, inclusive space where home cooks share, learn, and celebrate together.</p></div></div>
            <div class="val-row"><div class="val-dot"></div><div><h5>Continuous Improvement</h5><p>We're never done. User feedback directly shapes every update — the best product is built with the people who use it.</p></div></div>
          </div>
          <div class="col-lg-6 d-flex flex-column gap-4 reveal-right">
            <div class="quote-block">
              <p class="quote-text">Good food is the foundation of genuine happiness — we built SirChef so that happiness starts right in your kitchen.</p>
              <p class="quote-author">— The SirChef Team</p>
              <div class="quote-icons">
                <div class="q-icon"><i class="fas fa-utensils"></i></div>
                <div class="q-icon"><i class="fas fa-heart"></i></div>
                <div class="q-icon"><i class="fas fa-globe"></i></div>
              </div>
            </div>
            <div class="team-card">
              <div class="team-pulse"><i class="fas fa-users"></i></div>
              <h3>Meet the People Behind SirChef</h3>
              <p>Food lovers, tech minds &amp; culinary innovators — all under one kitchen.</p>
              <div class="dot-row"><span></span><span></span><span></span></div>
              <a href="team.php" class="btn-brand">Meet Our Team <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="faq-cta">
      <i class="fas fa-question    gh g1"></i>
      <i class="fas fa-leaf        gh g2"></i>
      <i class="fas fa-utensils    gh g3"></i>
      <i class="fas fa-paper-plane gh g4"></i>
      <div class="container">
        <span class="hero-badge" style="animation:fadeDown .6s ease .05s both"><i class="fas fa-circle-question"></i> Help Center</span>
        <h2>Still Have <em>Questions?</em></h2>
        <p class="hero-sub">Everything you need to know about SirChef — answered clearly and quickly. Visit our full FAQ page.</p>
        <div class="btn-wrap"><a href="faq.php" class="btn-brand-lg">View All FAQs <i class="fas fa-arrow-right"></i></a></div>
      </div>
    </section>

    <?php include 'footer.php'; ?>
    <?php include 'login_regis.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      /* Scroll reveal */
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
          if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }
        });
      }, { threshold: 0.12 });
      document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => observer.observe(el));
    </script>
  </body>
</html>
<!-- animation styles already included above -->
