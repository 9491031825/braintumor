<?php require "../header.php"; ?>

<?php 
  if (isset($_SESSION["username"])) {
    header("location:" . APPURL . "");
  }

  if (isset($_POST['submit'])) {
    // Checking if the fields entered are empty
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
      echo "<script>alert('Some inputs are empty');</script>";
    } else {
      $username = $_POST['username'];
      $email = $_POST['email'];
      // Hashing the password for security
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

      // Insert the values into users table
      $insert = $conn->prepare("INSERT INTO users (username,email,mypassword) VALUES (:username, :email, :mypassword)");
      $insert->execute([":username" => $username, ":email" => $email, ":mypassword" => $password]);

      // Redirect the browser to login page
      header("location: login.php");
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup - Amrita CC</title>
  <!-- Link to the formstyle.css -->
  <link rel="stylesheet" href="../formstyle.css">
</head>
<body>

<!-- Simple Registration Page -->
<div class="container">
  <div class="registration-container">
    <h2>Register</h2>
    <form id="registration-form" method="POST" action="register.php">
      <!-- Username Input -->
      <label for="username">Username</label>
      <input type="text" name="username" placeholder="Enter your username" required>

      <!-- Email Input -->
      <label for="email">Email</label>
      <input type="email" name="email" placeholder="Enter your email" required>

      <!-- Password Input -->
      <label for="password">Password</label>
      <input type="password" name="password" placeholder="Enter your password" required>

      <!-- Register Button -->
      <button type="submit" name="submit">Register</button>
    </form>

    <!-- Login Section -->
    <p>Already have an account? <a href="login.php">Login Here</a></p>
  </div>
</div>

<?php require "../footer.php"; ?>

</body>
</html>
