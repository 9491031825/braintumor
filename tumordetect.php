<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Brain Tumor Detection</title>
  <link rel="stylesheet" href="tumordetect.css"> <!-- Link to CSS -->
</head>
<body>

<?php include 'header.php'; ?>

<div class="tumor-detect">
  <!-- Page Header Section -->
  <header class="page-header">
    <h1>Brain Tumor Detection</h1>
    <p class="subtext">Leverage advanced AI for reliable, precise tumor analysis.</p>
  </header>

  <!-- Information Section -->
  <section class="info-section">
    <h2>About This Service</h2>
    <p>Our brain tumor detection tool is designed to assist medical professionals by analyzing MRI images for tumor presence. Using cutting-edge AI technology, we aim to improve diagnostic accuracy and support early intervention.</p>
  </section>

  <!-- Upload Form Section -->
  <section class="upload-section">
    <h2>Upload an MRI Image</h2>
    <form action="tumordetect.php" method="POST" enctype="multipart/form-data">
      <label for="file">Choose a Brain MRI Image:</label>
      <input type="file" name="file" id="file" required>
      <button type="submit" name="submit">Analyze Image</button>
    </form>
  </section>

  <!-- Results Section -->
  <section class="results-section">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
      $url = 'http://127.0.0.1:5000/predict';
      $imagePath = $_FILES['file']['tmp_name'];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      $cfile = curl_file_create($imagePath, 'image/png', 'file');
      $data = ['file' => $cfile];
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));

      $response = curl_exec($ch);
      if ($response === false) {
        echo '<p>Error: ' . curl_error($ch) . '</p>';
      } else {
        $result = json_decode($response, true);
        echo "<h2>Results</h2>";
        echo "<p><strong>Tumor Area (pixels):</strong> " . $result['tumor_area_pixels'] . "</p>";
        echo "<p><strong>Total Area (pixels):</strong> " . $result['total_area_pixels'] . "</p>";
        echo "<div><strong>Uploaded Image:</strong><br><img src='http://127.0.0.1:5000/" . $result['uploaded_image'] . "' alt='Uploaded Image'></div>";
        echo "<div><strong>Prediction Mask:</strong><br><img src='http://127.0.0.1:5000/" . $result['prediction'] . "' alt='Prediction Mask'></div>";
        echo "<div><strong>Overlay Image:</strong><br><img src='http://127.0.0.1:5000/" . $result['overlay'] . "' alt='Overlay Image'></div>";
      }
      curl_close($ch);
    }
    ?>
  </section>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
