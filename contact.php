<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amrita CC - Contact Section</title>
  <link rel="stylesheet" href="contactstyle.css"> <!-- Link to the herostyle.css -->
</head>
<body>

<section class="contact" id="contact">
  <h2>Contact Us</h2>
  <div class="contact-form">
    <form action="submit_contact.php" method="POST">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>
      
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
      
      <label for="message">Message:</label>
      <textarea id="message" name="message" required></textarea>
      
      <button type="submit">Send Message</button>
    </form>
  </div>
</section>

</body>
</html>
