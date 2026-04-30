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
    <title>Our Team | SirChef</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/team.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet" />
  </head>
  <body>

    <?php include 'header.php'; ?>

    <!-- ── Hero ── -->
    <section class="team-hero text-center">
      <div class="container">
        <div class="team-hero-inner">
          <div class="section-label">The People Behind SirChef</div>
          <h1>Meet Our <span>Team</span></h1>
          <p class="subtitle">Passionate food lovers, developers, and innovators working together to change the way you cook.</p>

          <!-- About Us Button -->
          <a href="about.php" class="btn-about-us">
            <i class="fas fa-seedling"></i> About Us
          </a>
        </div>
      </div>
    </section>

    <!-- ── About Modal ── -->
    <div class="about-modal-overlay" id="aboutModal">
      <div class="about-modal">
        <div class="about-modal-inner">
          <button class="about-modal-close" onclick="document.getElementById('aboutModal').classList.remove('active')">
            <i class="fas fa-times"></i>
          </button>
          <div class="row g-4">
            <div class="col-lg-6">
              <div class="about-card-dark">
                <div class="about-icon-wrap"><i class="fas fa-seedling"></i></div>
                <h3>About SirChef</h3>
                <p>SirChef was founded in 2023 with a simple mission: to help people cook delicious meals with what they already have. We believe that great cooking starts with creativity, not necessarily with a trip to the grocery store.</p>
                <p>Our platform uses advanced algorithms to match your ingredients with thousands of recipes from around the world. We're passionate about reducing food waste and helping people discover new favorite dishes.</p>
                <p>We partner with food sustainability organizations and donate a portion of our profits to hunger relief programs worldwide.</p>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="about-card-dark">
                <div class="about-icon-wrap"><i class="fas fa-users"></i></div>
                <h3>About Our Developers</h3>
                <p>Our team is composed of food enthusiasts, chefs, and tech innovators who share a common passion for cooking and sustainability.</p>
                <p>Led by Jarylle Keannu (System Analyst) and Jade Mar Camacho (Admin Developer), our diverse team brings together culinary expertise with cutting-edge technology.</p>
                <p>We continuously improve our recipe matching algorithms and expand our database to include more cuisines, dietary restrictions, and cooking styles.</p>
                <div class="mt-4">
                  <span class="tag-pill">Food Technology</span>
                  <span class="tag-pill">Sustainability</span>
                  <span class="tag-pill">Culinary Arts</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Stats Bar ── -->
    <div class="stats-bar">
      <div class="container">
        <div class="d-flex justify-content-center align-items-center gap-5 flex-wrap">
          <div class="stat-item">
            <span class="stat-number">10K+</span>
            <span class="stat-label">Recipes</span>
          </div>
          <div class="stat-divider d-none d-md-block" style="height:48px;"></div>
          <div class="stat-item">
            <span class="stat-number">500+</span>
            <span class="stat-label">Ingredients</span>
          </div>
          <div class="stat-divider d-none d-md-block" style="height:48px;"></div>
          <div class="stat-item">
            <span class="stat-number">50K+</span>
            <span class="stat-label">Happy Cooks</span>
          </div>
          <div class="stat-divider d-none d-md-block" style="height:48px;"></div>
          <div class="stat-item">
            <span class="stat-number">7</span>
            <span class="stat-label">Team Members</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Team Cards ── -->
    <section class="team-section">
      <div class="container">
        <div class="text-center mb-5">
          <div class="section-label">Our People</div>
          <h2 class="section-heading">The <span>Passionate</span> Minds</h2>
          <p class="section-sub">Each member brings a unique skill set that powers SirChef's mission every single day.</p>
        </div>

        <div class="row g-4 justify-content-center">

          <!-- Keannu -->
          <div class="col-6 col-md-4 col-lg-3">
            <div class="team-card-new">
              <div class="team-card-inner">
                <div class="team-photo-wrap">
                  <img src="../Assets/keannu.jpg" alt="Jarylle Keannu Evangelio" />
                  <div class="role-badge">System Analyst</div>
                </div>
                <div class="team-body">
                  <h4>Jarylle Keannu Evangelio</h4>
                  <div class="name-accent"></div>
                  <p>Culinary enthusiast passionate about turning simple ingredients into extraordinary dishes.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Akeziah -->
          <div class="col-6 col-md-4 col-lg-3">
            <div class="team-card-new">
              <div class="team-card-inner">
                <div class="team-photo-wrap">
                  <img src="../Assets/Frame 3 (37).png" alt="Akeziah Limbo" />
                  <div class="role-badge">Back-End Developer</div>
                </div>
                <div class="team-body">
                  <h4>Akeziah Limbo</h4>
                  <div class="name-accent"></div>
                  <p>The tech mind behind SirChef's smart recipe-matching algorithm.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Kristine -->
          <div class="col-6 col-md-4 col-lg-3">
            <div class="team-card-new">
              <div class="team-card-inner">
                <div class="team-photo-wrap">
                  <img src="../Assets/Frame 2 (43).png" alt="Kristine Lopez" />
                  <div class="role-badge">Back-End Developer</div>
                </div>
                <div class="team-body">
                  <h4>Kristine Lopez</h4>
                  <div class="name-accent"></div>
                  <p>Connecting food lovers worldwide and growing the SirChef family.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Jade -->
          <div class="col-6 col-md-4 col-lg-3">
            <div class="team-card-new">
              <div class="team-card-inner">
                <div class="team-photo-wrap">
                  <img src="../Assets/Frame 2 (41).png" alt="Jade Mar Camacho" />
                  <div class="role-badge">Admin Programmer</div>
                </div>
                <div class="team-body">
                  <h4>Jade Mar Camacho</h4>
                  <div class="name-accent"></div>
                  <p>Designs beautiful and user-friendly experiences for SirChef.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- John Lenard -->
          <div class="col-6 col-md-4 col-lg-3">
            <div class="team-card-new">
              <div class="team-card-inner">
                <div class="team-photo-wrap">
                  <img src="../Assets/Frame 1 (60).png" alt="John Lenard Robosa" />
                  <div class="role-badge">Business Process Manager</div>
                </div>
                <div class="team-body">
                  <h4>John Lenard Robosa</h4>
                  <div class="name-accent"></div>
                  <p>Builds the systems that power recipe searching and matching.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Rei -->
          <div class="col-6 col-md-4 col-lg-3">
            <div class="team-card-new">
              <div class="team-card-inner">
                <div class="team-photo-wrap">
                  <img src="../Assets/Frame 2 (42).png" alt="Rei Johnric Portugaliza" />
                  <div class="role-badge">Front-End Developer</div>
                </div>
                <div class="team-body">
                  <h4>Rei Johnric Portugaliza</h4>
                  <div class="name-accent"></div>
                  <p>Manages recipe content and ensures high quality cooking guides.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Paolo -->
          <div class="col-6 col-md-4 col-lg-3">
            <div class="team-card-new">
              <div class="team-card-inner">
                <div class="team-photo-wrap">
                  <img src="../Assets/pao.jpg" alt="Paolo Ballesteros" />
                  <div class="role-badge">Front-End Developer</div>
                </div>
                <div class="team-body">
                  <h4>Paolo Ballesteros</h4>
                  <div class="name-accent"></div>
                  <p>Helps users and builds a welcoming SirChef community.</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <?php include 'footer.php'; ?>
    <?php include 'login_regis.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Close modal on overlay click -->
    <script>
      document.getElementById('aboutModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
      });
    </script>

  </body>
</html>
