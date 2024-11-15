<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amrita CC - Hero Section</title>
  <link rel="stylesheet" href="herostyle.css"> <!-- Link to the herostyle.css -->
  <style>
    /* Typing Effect Styling */
    .typing-cursor {
      display: inline-block;
      width: 2px;
      height: 1em;
      background-color: black;
      margin-left: 2px;
      animation: blink 0.7s steps(1) infinite;
    }
    @keyframes blink {
      0%, 50% { opacity: 1; }
      50.1%, 100% { opacity: 0; }
    }
  </style>
</head>
<body>

<section class="hero">
  <div class="hero-content">
    <h1 id="typing-heading"></h1><span class="typing-cursor"></span>
    <p>Specialized brain tumor care at your campus clinic</p>
    <a href="<?php echo APPURL;?>/td2.php" class="button">Detect Brain Tumor</a>
  </div>
</section>

<script>
  const text = "Welcome to Amrita CC";
  let index = 0;

  function type() {
    const heading = document.getElementById("typing-heading");
    if (index < text.length) {
      heading.innerHTML += text.charAt(index);
      index++;
      setTimeout(type, 100); // Adjust typing speed if needed
    }
  }

  window.onload = type;
</script>

</body>
</html>
