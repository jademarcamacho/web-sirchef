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
    <title>FAQ | SirChef</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/faq.css" />
    <meta name="description" content="Find recipes based on your ingredients - A bohemian-style recipe discovery platform" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet" />
  </head>
  <body>

    <?php include 'header.php'; ?>

    <!-- ── FAQ Hero ── -->
    <section class="faq-hero">
      <div class="faq-hero-deco d1"><i class="fas fa-question"></i></div>
      <div class="faq-hero-deco d2"><i class="fas fa-utensils"></i></div>
      <div class="faq-hero-deco d3"><i class="fas fa-leaf"></i></div>
      <div class="faq-hero-deco d4"><i class="fas fa-mortar-pestle"></i></div>
      <div class="faq-hero-deco d5"><i class="fas fa-fire"></i></div>
      <div class="faq-hero-glow"></div>

     
        <h1 class="faq-hero-title">Frequently Asked Questions</h1>
        <p class="faq-hero-subtitle">
          Everything you need to know about SirChef — answered clearly and quickly.
        </p>

        <div class="faq-stats-row">
          <div class="faq-stat">
            <span class="faq-stat-number">10</span>
            <span class="faq-stat-label">Questions</span>
          </div>
          <div class="faq-stat-divider"></div>
          <div class="faq-stat">
            <span class="faq-stat-number">5</span>
            <span class="faq-stat-label">Categories</span>
          </div>
          <div class="faq-stat-divider"></div>
          <div class="faq-stat">
            <span class="faq-stat-number">24h</span>
            <span class="faq-stat-label">Response Time</span>
          </div>
        </div>

        <div class="faq-search-wrap">
          <i class="fas fa-search faq-search-icon"></i>
          <input
            type="text"
            class="faq-search-input"
            id="faqSearchInput"
            placeholder="Search questions…"
          />
        </div>
      </div>
    </section>

    <!-- ── FAQ Main ── -->
    <section class="faq-main-section">
      <div class="container">

        <div class="faq-categories" id="faqCategories">
          <button class="faq-cat-btn active" data-cat="all">
            <i class="fas fa-th"></i> All
          </button>
          <button class="faq-cat-btn" data-cat="general">
            <i class="fas fa-info-circle"></i> General
          </button>
          <button class="faq-cat-btn" data-cat="account">
            <i class="fas fa-user"></i> Account
          </button>
          <button class="faq-cat-btn" data-cat="recipes">
            <i class="fas fa-book-open"></i> Recipes
          </button>
          <button class="faq-cat-btn" data-cat="diet">
            <i class="fas fa-leaf"></i> Diet & Health
          </button>
          <button class="faq-cat-btn" data-cat="tech">
            <i class="fas fa-mobile-alt"></i> Technical
          </button>
        </div>

        <div id="faqList" style="max-width: 780px; margin: 0 auto;"></div>
        <div class="faq-no-results" id="faqNoResults">
          <i class="fas fa-search"></i>
          <p>No questions match your search. Try different keywords!</p>
        </div>

      </div>
    </section>

    <!-- ── Still Have Questions CTA ── -->
    <section class="faq-cta-section">
      <div class="container" style="position:relative; z-index:2;">
        <h2 class="faq-cta-title">Still have questions?</h2>
        <p class="faq-cta-sub">Can't find the answer you're looking for? Our team is happy to help.</p>
        <div>
          <a href="contact.php" class="faq-cta-btn">
            <i class="fas fa-paper-plane"></i> Contact Us
          </a>
          <a href="recipe.php" class="faq-cta-btn faq-cta-btn-outline">
            <i class="fas fa-search"></i> Find Recipes
          </a>
        </div>
      </div>
    </section>

    <?php include 'footer.php'; ?>
    <?php include 'login_regis.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      const faqItems = [
        { id: 1,  cat: "general", icon: "fas fa-magic",       question: "How does SirChef find recipes based on my ingredients?", answer: "SirChef uses a smart matching algorithm that analyzes thousands of recipes and identifies which ones can be made with the ingredients you have." },
        { id: 2,  cat: "general", icon: "fas fa-tag",          question: "Is SirChef free to use?",                                 answer: "Yes, SirChef offers a completely free basic version with access to thousands of recipes." },
        { id: 3,  cat: "account", icon: "fas fa-bookmark",     question: "Can I save my favorite recipes?",                         answer: "Absolutely! Once you create a free account, you can save recipes to your personal cookbook." },
        { id: 4,  cat: "diet",    icon: "fas fa-leaf",         question: "What if I have dietary restrictions?",                    answer: "SirChef allows you to set dietary preferences including vegetarian, vegan, gluten-free, dairy-free, and more." },
        { id: 5,  cat: "recipes", icon: "fas fa-carrot",       question: "How many ingredients can I search with at once?",         answer: "You can search with up to 15 ingredients at once. For best results, we recommend entering 3-8 main ingredients." },
        { id: 6,  cat: "recipes", icon: "fas fa-ban",          question: "Can I exclude certain ingredients?",                      answer: "Yes, you can exclude ingredients you don't like or are allergic to from your account settings." },
        { id: 7,  cat: "recipes", icon: "fas fa-star",         question: "Are the recipes suitable for beginners?",                 answer: "Definitely! We categorize recipes by difficulty level (Easy, Medium, Hard) and include step-by-step instructions." },
        { id: 8,  cat: "general", icon: "fas fa-sync-alt",     question: "How often is the recipe database updated?",               answer: "We add new recipes weekly, with seasonal collections and special themes throughout the year." },
        { id: 9,  cat: "account", icon: "fas fa-pencil-alt",   question: "Can I contribute my own recipes?",                        answer: "Yes! Registered users can submit their own recipes to be reviewed by our culinary team." },
        { id: 10, cat: "tech",    icon: "fas fa-mobile-alt",   question: "Does SirChef work on mobile devices?",                    answer: "Yes, SirChef is fully responsive and works on smartphones, tablets, and desktop computers." },
        { id: 11, cat: "account", icon: "fas fa-envelope-circle-check", question: "Why do I need email verification?", answer: "SirChef sends a verification code during registration so users can confirm their email. Later logins only need your password." },
        { id: 12, cat: "account", icon: "fas fa-key", question: "What happens if I forget my password?", answer: "Use Forgot Password. SirChef sends a reset code to your registered email, then lets you create a new hashed password." },
        { id: 13, cat: "recipes", icon: "fas fa-star", question: "How do favorites work?", answer: "Favorites are your personal cookbook list for recipes you especially love and want to find again quickly." },
        { id: 14, cat: "recipes", icon: "fas fa-share-nodes", question: "Can I post my own recipes?", answer: "Yes. Registered users can share thoughts, photos, videos, and complete recipes from the dashboard share modal." },
        { id: 15, cat: "general", icon: "fas fa-user-shield", question: "How is my privacy handled?", answer: "SirChef stores account and activity data in MySQL for app features. Passwords are hashed, and private settings remain account-only." },
        { id: 16, cat: "recipes", icon: "fas fa-carrot", question: "Why do guests have limited ingredient search?", answer: "Guests can preview the matching system with fewer ingredients. Registered users get expanded matching and community features." },
      ];

      let activeCategory = "all";
      let searchQuery = "";

      function getFilteredItems() {
        return faqItems.filter(function(item) {
          const matchesCat    = activeCategory === "all" || item.cat === activeCategory;
          const matchesSearch = searchQuery === "" ||
            item.question.toLowerCase().includes(searchQuery) ||
            item.answer.toLowerCase().includes(searchQuery);
          return matchesCat && matchesSearch;
        });
      }

      function renderFAQ() {
        const list  = document.getElementById("faqList");
        const noRes = document.getElementById("faqNoResults");
        const items = getFilteredItems();

        list.innerHTML = "";

        if (items.length === 0) {
          noRes.style.display = "block";
          return;
        }
        noRes.style.display = "none";

        items.forEach(function(faq, index) {
          const div = document.createElement("div");
          div.className = "faq-item";
          div.style.animationDelay = (index * 0.06) + "s";
          div.innerHTML = `
            <button class="faq-question-btn" onclick="toggleFAQ(this)">
              <div class="faq-q-icon"><i class="${faq.icon}"></i></div>
              <span class="faq-q-text">${faq.question}</span>
              <span class="faq-num-badge">${String(index + 1).padStart(2, '0')}</span>
              <div class="faq-q-arrow"><i class="fas fa-chevron-down"></i></div>
            </button>
            <div class="faq-answer">
              <div class="faq-answer-inner">${faq.answer}</div>
            </div>`;
          list.appendChild(div);
        });
      }

      function toggleFAQ(btn) {
        const item   = btn.closest(".faq-item");
        const isOpen = item.classList.contains("open");
        document.querySelectorAll(".faq-item.open").forEach(function(el) { el.classList.remove("open"); });
        if (!isOpen) item.classList.add("open");
      }

      document.getElementById("faqCategories").addEventListener("click", function(e) {
        const btn = e.target.closest(".faq-cat-btn");
        if (!btn) return;
        document.querySelectorAll(".faq-cat-btn").forEach(function(b) { b.classList.remove("active"); });
        btn.classList.add("active");
        activeCategory = btn.dataset.cat;
        renderFAQ();
      });

      document.getElementById("faqSearchInput").addEventListener("input", function() {
        searchQuery = this.value.trim().toLowerCase();
        renderFAQ();
      });

      renderFAQ();
    </script>
  </body>
</html>
