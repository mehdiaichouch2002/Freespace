<?php
    include 'partials/header.php'
?>
   <section class="contact">
  <div class="contact__container">
    <h2 class="contact__title">Contact Us</h2>
    <p class="contact__subtitle">Got a question? We'd love to hear from you! Drop us a message and we'll get back to you as soon as we can.</p>
    <form class="contact__form">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="v" id="name" name="name" required>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="v" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="message">Message:</label>
        <textarea id="message" class="v" name="message" required></textarea>
      </div>
      <button type="submit" class="btn">Send Message</button>
    </form>
  </div>
</section>
    <?php
    include 'partials/footer.php'
    ?>