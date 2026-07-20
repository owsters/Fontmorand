<?php
$pageTitle = 'Contact - Fontmorand, Prissac, France';
$pageDescription = 'Get in touch about Fontmorand, a historic manor house in Prissac, central France.';
$activeNav = 'contact';
$baseUrl = '../';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/nav.php';
?>
<div class="hero">
  <h1>Fontmorand</h1>
  <p>For more information, please get in touch.</p>
</div>

<div class="content">
  <h1 class="page-title">Contact us</h1>
  <p style="text-align:center;">Should you wish to contact us for further information, please complete the form below.</p>

  <div style="text-align:center;">
    <a class="btn" href="#contact-modal" data-modal-target="contact-modal">Contact Us</a>
  </div>

  <?php if (isset($_GET['s'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['s']); ?></div>
  <?php elseif (isset($_GET['e'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['e']); ?></div>
  <?php endif; ?>

  <div id="contact-modal" class="modal-overlay">
    <div class="modal-box">
      <button type="button" class="modal-close btn" style="float:right;">&times;</button>
      <h4>Get in touch</h4>
      <form method="post" action="https://formspree.io/f/YOUR_FORMSPREE_ID">
        <input type="hidden" name="_next" value="https://fontmorand.com/pages/contact.php?s=Thank+you.+Your+message+has+been+sent.">
        <input type="hidden" name="_subject" value="New enquiry from fontmorand.com">
        <div class="form-field">
          <label class="form-label" for="name">Name</label>
          <input required type="text" class="form-input" id="name" name="name" placeholder="Your name">
        </div>
        <div class="form-field">
          <label class="form-label" for="email">Email</label>
          <input required type="email" class="form-input" id="email" name="_replyto" placeholder="Your email">
        </div>
        <div class="form-field">
          <label class="form-label" for="subject">Subject</label>
          <input required type="text" class="form-input" id="subject" name="subject" placeholder="Subject">
        </div>
        <div class="form-field">
          <label class="form-label" for="message">Message</label>
          <textarea required class="form-textarea" rows="6" id="message" name="message" placeholder="Your message..."></textarea>
        </div>
        <div class="form-field" style="text-align:center;">
          <button type="submit" class="btn">Send Message</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$extraScripts = [];
require __DIR__ . '/../includes/footer.php';
?>
