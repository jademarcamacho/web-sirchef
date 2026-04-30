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
    <title>Privacy Policy | SirChef</title>
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
        <span style="left:6%;animation-duration:14s;animation-delay:0s">🔒</span>
        <span style="left:20%;animation-duration:10s;animation-delay:2.5s">🛡️</span>
        <span style="left:42%;animation-duration:16s;animation-delay:1s">🔐</span>
        <span style="left:63%;animation-duration:11s;animation-delay:3.5s">👁️</span>
        <span style="left:80%;animation-duration:13s;animation-delay:0.8s">🔒</span>
        <span style="left:32%;animation-duration:9s;animation-delay:4s">🛡️</span>
      </div>
      <div class="container">
        <div class="legal-hero-icon">
          <i class="fas fa-user-shield"></i>
        </div>
        <h1 class="legal-hero-title">Privacy <span>Policy</span></h1>
        <p class="legal-hero-subtitle">Your privacy is important to us. Here's exactly how we handle your data.</p>
        <div class="legal-hero-badge">
          <i class="fas fa-calendar-alt"></i> Last updated: March 1, 2026
        </div>
      </div>
    </section>

    <!-- ── MAIN CONTENT ── -->
    <section id="privacy-policy" class="legal-section">

      <p class="legal-last-updated">
        <i class="fas fa-clock"></i> Effective date: March 1, 2026
      </p>

      <!-- Table of Contents -->
      <div class="legal-toc">
        <p class="legal-toc-title"><i class="fas fa-list-ul"></i> In this document</p>
        <ul class="legal-toc-list">
          <li><a href="#info-collected">Information We Collect</a></li>
          <li><a href="#how-we-use">How We Use Your Information</a></li>
          <li><a href="#sharing">Sharing Your Information</a></li>
          <li><a href="#data-security">Data Security</a></li>
          <li><a href="#your-rights">Your Rights</a></li>
          <li><a href="#third-party">Third-Party Websites</a></li>
          <li><a href="#policy-changes">Changes to This Policy</a></li>
          <li><a href="#contact">Contact Us</a></li>
        </ul>
      </div>

      <!-- Intro callout -->
      <div class="legal-block">
        <div class="legal-callout">
          <span class="legal-callout-icon"><i class="fas fa-info-circle"></i></span>
          <p>This Privacy Policy explains how SirChef collects, uses, discloses, and safeguards your information when you visit our website. Please read this policy carefully. If you disagree with its terms, please discontinue use of the site.</p>
        </div>
        <div style="margin-top:0.8rem;">
          <span class="legal-pill"><i class="fas fa-lock"></i> Data Encrypted</span>
          <span class="legal-pill accent"><i class="fas fa-ban"></i> Never Sold</span>
          <span class="legal-pill"><i class="fas fa-check-circle"></i> GDPR Aware</span>
          <span class="legal-pill accent"><i class="fas fa-user-shield"></i> You're in Control</span>
        </div>
      </div>

      <!-- 1. Information We Collect -->
      <div class="legal-block" id="info-collected">
        <h2>
          <span class="legal-section-num">1</span>
          Information We Collect
        </h2>
        <hr class="legal-divider">
        <p>We collect information in a few different ways depending on how you interact with SirChef.</p>

        <h3>Personal Data</h3>
        <p>Information you voluntarily provide when registering or using the platform:</p>
        <ul>
          <li><strong>Name</strong> — used to personalize your account and public profile</li>
          <li><strong>Email address</strong> — used for account access and communications</li>
          <li><strong>Phone number</strong> — optional, used for account recovery only</li>
          <li><strong>Profile photo</strong> — optional, displayed on your public profile</li>
        </ul>

        <h3>Usage Data</h3>
        <p>Automatically collected when you interact with the platform:</p>
        <ul>
          <li>IP address and approximate geographic location</li>
          <li>Browser type, version, and operating system</li>
          <li>Pages visited, time spent, and navigation paths</li>
          <li>Referring URLs and search terms used to find us</li>
        </ul>

        <h3>Cookies & Tracking Technologies</h3>
        <ul>
          <li>Session cookies to keep you logged in</li>
          <li>Preference cookies to remember your settings</li>
          <li>Analytics cookies to understand how users navigate the site</li>
        </ul>

        <div style="margin-top:1rem;">
          <span class="legal-pill"><i class="fas fa-database"></i> Minimal Collection</span>
          <span class="legal-pill accent"><i class="fas fa-cookie-bite"></i> Cookie Controlled</span>
        </div>
      </div>

      <!-- 2. How We Use Your Information -->
      <div class="legal-block" id="how-we-use">
        <h2>
          <span class="legal-section-num">2</span>
          How We Use Your Information
        </h2>
        <hr class="legal-divider">
        <p>We only use your information for purposes that are clearly described below. We do not use your data for anything outside of these purposes without your explicit consent.</p>

        <h3>Core Platform Operations</h3>
        <ul>
          <li>Providing, operating, and maintaining the SirChef platform</li>
          <li>Processing account registration and managing your profile</li>
          <li>Personalizing recipe recommendations based on your preferences</li>
          <li>Enabling features like favorite recipes and ingredient searches</li>
        </ul>

        <h3>Communications</h3>
        <ul>
          <li>Responding to your support inquiries and feedback</li>
          <li>Sending administrative notices (e.g. password resets, policy updates)</li>
          <li>Sending promotional emails only where you have opted in</li>
        </ul>

        <h3>Platform Improvement</h3>
        <ul>
          <li>Analysing usage patterns to improve site functionality</li>
          <li>Detecting and preventing fraudulent or abusive activity</li>
          <li>Running internal analytics to improve recipe discovery</li>
        </ul>

        <div class="legal-callout" style="margin-top:1.2rem;">
          <span class="legal-callout-icon"><i class="fas fa-bell-slash"></i></span>
          <p>You can opt out of promotional emails at any time by clicking "Unsubscribe" in any email we send, or by updating your notification preferences in your account settings.</p>
        </div>
      </div>

      <!-- 3. Sharing Your Information -->
      <div class="legal-block" id="sharing">
        <h2>
          <span class="legal-section-num">3</span>
          Sharing Your Information
        </h2>
        <hr class="legal-divider">

        <div class="legal-callout">
          <span class="legal-callout-icon"><i class="fas fa-handshake-slash"></i></span>
          <p><strong>We do not sell, trade, or rent your personal information to third parties</strong> — ever. Your data is not a product.</p>
        </div>

        <p>We may share limited information only in the following situations:</p>
        <ul>
          <li><strong>Service providers:</strong> Trusted third parties that help us operate the platform (e.g. hosting, email delivery, analytics) — bound by strict confidentiality agreements</li>
          <li><strong>Legal compliance:</strong> If required by law, court order, or to protect the rights and safety of SirChef or its users</li>
          <li><strong>Business transfers:</strong> In the event of a merger or acquisition, your data may be transferred — you will be notified in advance</li>
          <li><strong>With your consent:</strong> Any other sharing only occurs with your explicit permission</li>
        </ul>

        <div style="margin-top:1rem;">
          <span class="legal-pill accent"><i class="fas fa-ban"></i> Never Sold</span>
          <span class="legal-pill"><i class="fas fa-file-contract"></i> Contractually Protected</span>
        </div>
      </div>

      <!-- 4. Data Security -->
      <div class="legal-block" id="data-security">
        <h2>
          <span class="legal-section-num">4</span>
          Data Security
        </h2>
        <hr class="legal-divider">
        <p>We take the security of your personal information seriously and implement industry-standard measures to protect it.</p>

        <h3>Technical Safeguards</h3>
        <ul>
          <li>All data transmitted between your browser and our servers is encrypted via HTTPS/TLS</li>
          <li>Passwords are hashed and salted — never stored in plain text</li>
          <li>Regular security audits and vulnerability assessments</li>
          <li>Access to personal data is restricted to authorised personnel only</li>
        </ul>

        <div class="legal-callout">
          <span class="legal-callout-icon"><i class="fas fa-exclamation-triangle"></i></span>
          <p>No method of transmission over the Internet is 100% secure. While we strive to protect your data, we cannot guarantee absolute security. If you suspect a breach, contact us immediately at <strong>hello@sirchef.com</strong>.</p>
        </div>

        <div style="margin-top:1rem;">
          <span class="legal-pill"><i class="fas fa-lock"></i> TLS Encrypted</span>
          <span class="legal-pill accent"><i class="fas fa-key"></i> Hashed Passwords</span>
          <span class="legal-pill"><i class="fas fa-shield-alt"></i> Regular Audits</span>
        </div>
      </div>

      <!-- 5. Your Rights -->
      <div class="legal-block" id="your-rights">
        <h2>
          <span class="legal-section-num">5</span>
          Your Rights
        </h2>
        <hr class="legal-divider">
        <p>Depending on your location, you may have the following rights under applicable data protection laws (including GDPR and similar regulations):</p>
        <ul>
          <li><strong>Right to access</strong> — request a copy of the personal data we hold about you</li>
          <li><strong>Right to correction</strong> — request that we correct inaccurate or incomplete data</li>
          <li><strong>Right to deletion</strong> — request that we delete your personal data ("right to be forgotten")</li>
          <li><strong>Right to portability</strong> — request your data in a structured, machine-readable format</li>
          <li><strong>Right to object</strong> — object to how we process your data in certain circumstances</li>
          <li><strong>Right to withdraw consent</strong> — withdraw consent for data uses that rely on your consent</li>
        </ul>
        <div class="legal-callout" style="margin-top:1.2rem;">
          <span class="legal-callout-icon"><i class="fas fa-envelope"></i></span>
          <p>To exercise any of these rights, email us at <strong>hello@sirchef.com</strong> with the subject line "Data Rights Request". We will respond within 30 days.</p>
        </div>
      </div>

      <!-- 6. Third-Party Websites -->
      <div class="legal-block" id="third-party">
        <h2>
          <span class="legal-section-num">6</span>
          Third-Party Websites
        </h2>
        <hr class="legal-divider">
        <p>SirChef may contain links to external websites or services that are not operated by us. This policy does not apply to those third-party sites.</p>
        <ul>
          <li>We have no control over the content or privacy practices of third-party sites</li>
          <li>We are not responsible for any data collected by those sites</li>
          <li>We encourage you to review the privacy policy of any site you visit</li>
        </ul>
        <div style="margin-top:1rem;">
          <span class="legal-pill danger"><i class="fas fa-external-link-alt"></i> External Sites Not Covered</span>
        </div>
      </div>

      <!-- 7. Changes to Policy -->
      <div class="legal-block" id="policy-changes">
        <h2>
          <span class="legal-section-num">7</span>
          Changes to This Privacy Policy
        </h2>
        <hr class="legal-divider">
        <p>We may update this Privacy Policy from time to time to reflect changes in our practices, technology, or legal requirements.</p>
        <div class="legal-callout">
          <span class="legal-callout-icon"><i class="fas fa-bell"></i></span>
          <p>When we make significant changes, we will notify you by updating the "Last updated" date at the top of this page and, where appropriate, by sending an email notification to your registered address.</p>
        </div>
        <ul>
          <li>Minor wording changes will be updated silently with a new date</li>
          <li>Material changes will be communicated via email or a prominent banner</li>
          <li>Continued use of the platform after changes constitutes acceptance</li>
        </ul>
      </div>

      <!-- 8. Contact -->
      <div class="legal-block" id="contact">
        <h2>
          <span class="legal-section-num">8</span>
          Contact Us
        </h2>
        <hr class="legal-divider">
        <p>If you have any questions, concerns, or requests regarding this Privacy Policy, our team is ready to help.</p>
        <ul>
          <li><strong>Email:</strong> hello@sirchef.com</li>
          <li><strong>Subject line:</strong> Privacy Policy Inquiry</li>
          <li><strong>Response time:</strong> Within 2–3 business days</li>
        </ul>
      </div>

      <!-- Contact CTA -->
      <div class="legal-contact-cta">
        <h3>Questions about your privacy?</h3>
        <p>We're committed to transparency. Our team is happy to walk you through how we handle your data.</p>
        <a href="mailto:hello@sirchef.com">
          <i class="fas fa-envelope"></i> Contact Privacy Team
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
        observer.observe(block);
      });
    </script>

  </body>
</html>
