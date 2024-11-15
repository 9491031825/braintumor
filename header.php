<?php
session_start();
define("APPURL", "http://localhost/braintumorapp");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amrita CC Header</title>
  <link rel="stylesheet" href="<?php echo APPURL; ?>/headerstyle.css"> 
</head>
<body>

<header class="header">
  <nav class="nav">
    <div class="logo">
      <a href="<?php echo APPURL; ?>" class="logo-link">
        <span class="logo-plus">+</span>
        <span class="logo-text">Amrita CC</span>
      </a>
    </div>
    <ul class="nav-links">
      <li><a href="<?php echo APPURL;?>">Home</a></li>
      <li><a href="#team">Team</a></li>
      <li><a href="#contact">Contact</a></li>
      <li><a href="<?php echo APPURL;?>/auth/login.php">Login</a></li>
      <li><a href="<?php echo APPURL;?>/auth/signup.php">Signup</a></li>
    </ul>
  </nav>
</header>

</body>
</html>
