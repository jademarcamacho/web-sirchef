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
    <title>Terms of Service | SirChef</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/legal.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet" />
  </head>
  <body>

    <?php include 'header.php'; ?>

    <!-- ── HERO BANNER ── -->
    <section class="legal-hero">
      <div class="legal-hero-particles">
        <span style="left:8%;animation-duration:13s;animation-delay:0s">⚖️</span>
        <span style="left:22%;animation-duration:9s;animation-delay:2s">📜</span>
        <span style="left:45%;animation-duration:15s;animation-delay:1s">🔒</span>
        <span style="left:68%;animation-duration:11s;animation-delay:3s">🛡️</span>
        <span style="left:85%;animation-duration:10s;animation-delay:0.5s">⚖️</span>
        <span style="left:35%;animation-duration:12s;animation-delay:4s">📋</span>
      </div>
      <div class="container">
        <div class="legal-hero-icon">
          <i class="fas fa-file-contract"></i>
        </div>
        <h1 class="legal-hero-title">Terms of <span>Service</span></h1>
        <p class="legal-hero-subtitle">Please read these terms carefully before using SirChef.</p>
        <div class="legal-hero-badge">
          <i class="fas fa-calendar-alt"></i> Last updated: March 1, 2026
        </div>
      </div>
    </section>

    <!-- ── MAIN CONTENT ── -->
    <section id="terms-of-service" class="legal-section">

      <p class="legal-last-updated">
        <i class="fas fa-clock"></i> Effective date: March 1, 2026
      </p>

      <!-- Table of Contents -->
      <div class="legal-toc">
        <p class="legal-toc-title"><i class="fas fa-list-ul"></i> In this document</p>
        <ul class="legal-toc-list">
          <li><a href="#use-of-service">Use of Service</a></li>
          <li><a href="#user-accounts">User Accounts</a></li>
          <li><a href="#intellectual-property">Intellectual Property</a></li>
          <li><a href="#limitation">Limitation of Liability</a></li>
          <li><a href="#termination">Termination</a></li>
          <li><a href="#changes">Changes to Terms</a></li>
          <li><a href="#contact">Contact Us</a></li>
        </ul>
      </div>

      <!-- Intro callout -->
      <div class="legal-block">
        <div class="legal-callout">
          <span class="legal-callout-icon"><i class="fas fa-info-circle"></i></span>
          <p>By accessing or using SirChef, you agree to be bound by these Terms of Service. If you do not agree with any part of these terms, please do not use our platform.</p>
        </div>
        <div style="margin-top:0.8rem;">
          <span class="legal-pill"><i class="fas fa-shield-alt"></i> User Protection</span>
          <span class="legal-pill accent"><i class="fas fa-check-circle"></i> Fair Use Policy</span>
          <span class="legal-pill"><i class="fas fa-globe"></i> Global Standards</span>
        </div>
      </div>

      <!-- 1. Use of Service -->
      <div class="legal-block" id="use-of-service">
        <h2>
          <span class="legal-section-num">1</span>
          Use of Service
        </h2>
        <hr class="legal-divider">
        <p>You agree to use SirChef only for lawful purposes and in a way that does not infringe the rights of others or restrict their use and enjoyment of the platform.</p>
        <h3>Acceptable Use</h3>
        <ul>
          <li>Use the platform to discover, share, and enjoy recipes</li>
          <li>Engage respectfully with other community members</li>
          <li>Provide accurate information when submitting recipes or reviews</li>
          <li>Comply with all applicable local and international laws</li>
        </ul>
        <h3>Prohibited Activities</h3>
        <ul>
          <li>Attempting to disrupt or interfere with the proper functioning of the website</li>
          <li>Uploading malicious code, viruses, or harmful content</li>
          <li>Impersonating other users or SirChef staff</li>
          <li>Scraping or harvesting data without written permission</li>
        </ul>
      </div>

      <!-- 2. User Accounts -->
      <div class="legal-block" id="user-accounts">
        <h2>
          <span class="legal-section-num">2</span>
          User Accounts
        </h2>
        <hr class="legal-divider">
        <p>To access certain features of SirChef, you must register for an account. You are responsible for maintaining the security of your account.</p>
        <div class="legal-callout">
          <span class="legal-callout-icon"><i class="fas fa-exclamation-triangle"></i></span>
          <p><strong>Important:</strong> You are fully responsible for all activities that occur under your account. If you suspect unauthorized access, contact us immediately at <strong>hello@sirchef.com</strong>.</p>
        </div>
        <ul>
          <li>Keep your password confidential and use a strong, unique password</li>
          <li>Do not share your account credentials with others</li>
          <li>You must be at least 13 years old to create an account</li>
          <li>Provide accurate and up-to-date registration information</li>
        </ul>
      </div>

      <!-- 3. Intellectual Property -->
      <div class="legal-block" id="intellectual-property">
        <h2>
          <span class="legal-section-num">3</span>
          Intellectual Property
        </h2>
        <hr class="legal-divider">
        <p>All content on SirChef — including recipes, images, text, logos, and design elements — is the property of SirChef or its content contributors.</p>
        <h3>Our Content</h3>
        <ul>
          <li>SirChef branding and design may not be reproduced without written permission</li>
          <li>Platform code and architecture are proprietary and protected</li>
          <li>Original editorial content belongs to SirChef</li>
        </ul>
        <h3>Your Content</h3>
        <ul>
          <li>You retain ownership of recipes and content you submit</li>
          <li>By submitting, you grant SirChef a non-exclusive license to display and share your content</li>
          <li>You confirm that your submitted content does not infringe third-party rights</li>
        </ul>
        <div style="margin-top:1rem;">
          <span class="legal-pill accent"><i class="fas fa-copyright"></i> Content Protected</span>
          <span class="legal-pill"><i class="fas fa-user-check"></i> You Own Your Recipes</span>
        </div>
      </div>

      <!-- 4. Limitation of Liability -->
      <div class="legal-block" id="limitation">
        <h2>
          <span class="legal-section-num">4</span>
          Limitation of Liability
        </h2>
        <hr class="legal-divider">
        <p>SirChef is provided on an "as is" and "as available" basis without warranties of any kind, either express or implied.</p>
        <div class="legal-callout">
          <span class="legal-callout-icon"><i class="fas fa-balance-scale"></i></span>
          <p>To the fullest extent permitted by law, SirChef shall not be liable for any indirect, incidental, special, or consequential damages resulting from your use of — or inability to use — the platform.</p>
        </div>
        <ul>
          <li>We do not guarantee uninterrupted or error-free operation of the service</li>
          <li>Recipe outcomes may vary — we are not liable for any dietary or health consequences</li>
          <li>We are not responsible for third-party links or content referenced on the platform</li>
        </ul>
      </div>

      <!-- 5. Termination -->
      <div class="legal-block" id="termination">
        <h2>
          <span class="legal-section-num">5</span>
          Termination
        </h2>
        <hr class="legal-divider">
        <p>We reserve the right to suspend or permanently terminate any account that violates these Terms of Service, without prior notice or liability.</p>
        <ul>
          <li>Accounts may be terminated for abuse, spam, or harmful behaviour</li>
          <li>You may delete your account at any time from your profile settings</li>
          <li>Upon termination, your right to use the service ceases immediately</li>
          <li>Certain provisions of these terms survive termination</li>
        </ul>
        <div style="margin-top:1rem;">
          <span class="legal-pill danger"><i class="fas fa-ban"></i> Violations Result in Termination</span>
        </div>
      </div>

      <!-- 6. Changes to Terms -->
      <div class="legal-block" id="changes">
        <h2>
          <span class="legal-section-num">6</span>
          Changes to Terms
        </h2>
        <hr class="legal-divider">
        <p>We may update or revise these Terms of Service at any time. When we make changes, we will update the "Last updated" date at the top of this page.</p>
        <div class="legal-callout">
          <span class="legal-callout-icon"><i class="fas fa-bell"></i></span>
          <p>Continued use of SirChef after any changes indicates your acceptance of the revised terms. We encourage you to review this page periodically.</p>
        </div>
        <ul>
          <li>Material changes will be communicated via email or a platform notice</li>
          <li>If you disagree with updated terms, you should stop using the service</li>
        </ul>
      </div>

      <!-- 7. Contact -->
      <div class="legal-block" id="contact">
        <h2>
          <span class="legal-section-num">7</span>
          Contact Us
        </h2>
        <hr class="legal-divider">
        <p>If you have any questions about these Terms of Service, please don't hesitate to reach out to our team.</p>
        <ul>
          <li><strong>Email:</strong> hello@sirchef.com</li>
          <li><strong>Subject line:</strong> Terms of Service Inquiry</li>
          <li><strong>Response time:</strong> Within 2–3 business days</li>
        </ul>
      </div>

      <!-- Contact CTA -->
      <div class="legal-contact-cta">
        <h3>Questions about our Terms?</h3>
        <p>Our team is happy to clarify anything in this document.</p>
        <a href="mailto:hello@sirchef.com">
          <i class="fas fa-envelope"></i> Email Us
        </a>
      </div>

      <a href="#" class="legal-back-top">
        <i class="fas fa-arrow-up"></i> Back to top
      </a>

    </section>

    <?php include 'footer.php'; ?>
    <?php include 'login_regis.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      // Staggered scroll reveal for legal blocks
      const blocks = document.querySelectorAll('.legal-block');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });

      blocks.forEach((block, i) => {
        block.style.opacity = '0';
        block.style.transform = 'translateY(24px)';
        block.style.transition = `opacity 0.6s ease ${i * 0.08}s, transform 0.6s ease ${i * 0.08}s`;
        observer.unobserve(block);
        observer.observe(block);
      });
    </script>
  </body>
</html>
