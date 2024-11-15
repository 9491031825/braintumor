<?php require "../header.php"; ?>

<?php 
  if (isset($_SESSION["username"])) {
    header("location:" . APPURL . "");
  }

  if (isset($_POST['submit'])) {
    // Check if the fields entered are empty
    if (empty($_POST['email']) || empty($_POST['password'])) {
      echo "<script>alert('Please fill in both fields.');</script>";
    } else {
      $email = $_POST['email'];
      $password = $_POST['password'];

      // Check credentials against the database (example query)
      $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
      $stmt->execute([':email' => $email]);
      $user = $stmt->fetch();

      if ($user && password_verify($password, $user['mypassword'])) {
        // Start the session and set the username
        $_SESSION['username'] = $user['username'];
        header("location: " . APPURL . "/index.php");
      } else {
        echo "<script>alert('Invalid email or password.');</script>";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Amrita CC</title>
  <!-- Link to formstyle.css (one directory back) -->
  <link rel="stylesheet" href="../formstyle.css"> 
</head>
<body>

<!-- Simple Login Page -->
<div class="container">
  <div class="login-container">
    <h2>Login</h2>
    <form id="login-form" method="POST" action="login.php">
      <!-- Email Input -->
      <label for="email">Email</label>
      <input type="email" name="email" placeholder="Enter your email" required>

      <!-- Password Input -->
      <label for="password">Password</label>
      <input type="password" name="password" placeholder="Enter your password" required>

      <!-- Login Button -->
      <button type="submit" name="submit">Login</button>
    </form>

    <!-- Registration Link -->
    <p>Don't have an account? <a href="signup.php">Sign Up Here</a></p>
  </div>
</div>

<?php require "../footer.php"; ?>

</body>
</html>
