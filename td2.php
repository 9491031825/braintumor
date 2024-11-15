<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['mri-upload'])) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["mri-upload"]["name"]);
    move_uploaded_file($_FILES["mri-upload"]["tmp_name"], $targetFile);
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Brain Tumor Detection</title>
  <link rel="stylesheet" href="td2.css">
</head>
<body class="min-h-screen bg-gradient-to-b">

  <!-- Include header.php -->
  <?php require "header.php"; ?>

    <section class="process-section">
        <div class="container">
        <h2 class="section-title">User Guide</h2>
        <div class="steps-grid">
            <div class="step-card" id="upload-step">
            <div class="step-icon bg-blue-100">
                <!-- Replace with appropriate icon, e.g., an SVG or image for "Upload" -->
                <img src="upload.jpg" alt="Upload Icon" class="icon">
            </div>
            <h3 class="step-title">Upload MRI Scan</h3>
            <p class="step-description">
                Begin by uploading a clear MRI scan image of the brain. Our system accepts various image formats for your convenience.
            </p>
            </div>
            <div class="step-card" id="scan-step">
            <div class="step-icon bg-blue-100">
                <!-- Replace with appropriate icon, e.g., an SVG or image for "Scan" -->
                <img src="scan-icon.png" alt="Scan Icon" class="icon">
            </div>
            <h3 class="step-title">Initiate Brain Scan</h3>
            <p class="step-description">
                Click the "Scan Brain" button to start our advanced AI analysis. Our algorithm will carefully examine the MRI for any anomalies.
            </p>
            </div>
            <div class="step-card" id="review-step">
            <div class="step-icon bg-blue-100">
                <!-- Replace with appropriate icon, e.g., an SVG or image for "Alert" -->
                <img src="alert-icon.png" alt="Alert Icon" class="icon">
            </div>
            <h3 class="step-title">Review Results</h3>
            <p class="step-description">
                Examine the scan results. If a potential tumor is detected, you'll see an option to proceed with a detailed analysis.
            </p>
            </div>
            <div class="step-card" id="analyze-step">
            <div class="step-icon bg-blue-100">
                <!-- Replace with appropriate icon, e.g., an SVG or image for "Activity" -->
                <img src="analyze-icon.png" alt="Analyze Icon" class="icon">
            </div>
            <h3 class="step-title">Analyze Tumor</h3>
            <p class="step-description">
                Click "Analyze Tumor" for an in-depth report. You'll receive detailed information about the tumor's characteristics and location.
            </p>
            </div>
        </div>
        </div>
    </section>

    <div><h2 class="model-title text-centre">Tumor Detection Model</h2></div>
    <!-- Main Content -->
    <main class="container">
        <!-- Upload Section -->
        <section id="upload" class="upload-section">
        <div class="card">
            <h2 class="section-title text-center">Upload Your Brain MRI Scan</h2>
            <p class="text-gray-600 text-center" style="padding-bottom: 16px;">Share your MRI scan to get insights and contribute to our research.</p>
            
            <form id="uploadForm" enctype="multipart/form-data" class="form-upload">
            <label for="mri-upload" class="upload-label">
                <div class="upload-area" id="previewContainer">
                <div class="upload-icon" id="uploadPlaceholder">
                    <img src="upload.jpg" alt="Upload Icon">
                    <p>Click or drag to upload</p>
                </div>
                <!-- Uploaded image preview -->
                <img id="uploadedPreview" class="uploaded-image" style="display: none;" alt="Uploaded MRI Scan">
                </div>
            </label>
            <input type="file" name="file" id="mri-upload" accept="image/*" class="hidden-input" required>
            <button type="submit" class="btn">Analyze Image</button>
            </form>
        </div>
        </section>

        <!-- Results Section -->
    <section class="results-section" id="resultsSection" style="display:none;">
        <h2 class="section-title">Results</h2>
        <div class="image-row">
            <div class="image-container">
                <strong>Uploaded Image:</strong><br>
                <img id="uploadedImage" alt="Uploaded Image">
            </div>
            <div class="image-container">
                <strong>Prediction Mask:</strong><br>
                <img id="predictionMask" alt="Prediction Mask">
            </div>
            <div class="image-container">
                <strong>Overlay Image:</strong><br>
                <img id="overlayImage" alt="Overlay Image">
            </div>
        </div>
        <p><strong>Tumor Area (pixels):</strong> <span id="tumorArea"></span></p>
        <p><strong>Total Area (pixels):</strong> <span id="totalArea"></span></p>
    </section>



    </main>

    <!-- JavaScript to handle AJAX upload and display preview -->
    <script>
        document.getElementById("mri-upload").addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
            document.getElementById("uploadPlaceholder").style.display = "none";
            document.getElementById("uploadedPreview").src = e.target.result;
            document.getElementById("uploadedPreview").style.display = "block";
            };
            reader.readAsDataURL(file);
        }
        });

        document.getElementById("uploadForm").onsubmit = function(event) {
        event.preventDefault(); // Prevent traditional form submission

        const formData = new FormData();
        formData.append("file", document.getElementById("mri-upload").files[0]);

        fetch("upload.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
            alert("Error: " + data.error);
            } else {
            // Display the results
            document.getElementById("tumorArea").textContent = data.tumor_area_pixels;
            document.getElementById("totalArea").textContent = data.total_area_pixels;
            document.getElementById("uploadedImage").src = "http://127.0.0.1:5000/" + data.uploaded_image;
            document.getElementById("predictionMask").src = "http://127.0.0.1:5000/" + data.prediction;
            document.getElementById("overlayImage").src = "http://127.0.0.1:5000/" + data.overlay;
            document.getElementById("resultsSection").style.display = "block";
            }
        })
        .catch(error => {
            alert("Error uploading file: " + error);
        });
        };
    </script>

    <section id="info-about-mri">
        <h2 class="section-title">
            <span id="infoTitleAboutMRI" onclick="toggleInfoContent('infoContentAboutMRI', 'expandArrowAboutMRI')" style="cursor: pointer;">
                About MRI Scans<span id="expandArrowAboutMRI">▼</span> <!-- Arrow icon -->
            </span>
        </h2>
        <div id="infoContentAboutMRI" class="infoContent" style="display: none;">
            <div>
                <h3>What is an MRI scan and how does it work?</h3>
                <p>An MRI (Magnetic Resonance Imaging) scan uses strong magnetic fields and radio waves to create detailed images of the inside of your body.</p>
                <h3>What are the common uses of MRI scans?</h3>
                <p>MRI scans are commonly used to diagnose and monitor conditions affecting the brain, spine, joints, and internal organs.</p>
                <h3>Are there any risks or side effects associated with MRI scans?</h3>
                <p>MRI scans are generally safe, but some people may experience discomfort from the loud noises or feel claustrophobic inside the machine.</p>
                <h3>How should I prepare for an MRI scan?</h3>
                <p>You may need to avoid eating or drinking for a few hours before the scan and remove any metal objects from your body.</p>
                <h3>What can I expect during an MRI scan?</h3>
                <p>You will lie still on a table that slides into the MRI machine. The scan is painless but can be noisy.</p>
                <h3>How long does an MRI scan take?</h3>
                <p>An MRI scan typically takes between 30 to 60 minutes, depending on the area being examined.</p>
                <h3>Can anyone have an MRI scan?</h3>
                <p>Most people can have an MRI scan, but it may not be suitable for those with certain implants or metal fragments in their body.</p>
                <h3>What do the results of an MRI scan mean?</h3>
                <p>The results will show detailed images of the scanned area, helping doctors diagnose and plan treatment for various conditions.</p>
                <h3>How is an MRI different from a CT scan or X-ray?</h3>
                <p>Unlike CT scans or X-rays, MRI does not use ionizing radiation and provides more detailed images of soft tissues.</p>
                <h3>Are there any alternatives to MRI scans?</h3>
                <p>Alternatives include CT scans, X-rays, and ultrasound, depending on the condition being investigated.</p>
            </div>
        </div>
    </section>

    <section id="info-understanding-tumors">
        <h2 class="section-title">
            <span id="infoTitleUnderstandingTumors" onclick="toggleInfoContent('infoContentUnderstandingTumors', 'expandArrowUnderstandingTumors')" style="cursor: pointer;">
                About Brain Tumors<span id="expandArrowUnderstandingTumors">▼</span> <!-- Arrow icon -->
            </span>
        </h2>
        <div id="infoContentUnderstandingTumors" class="infoContent" style="display: none;">
            <div>
                <h3>What is a brain tumor?</h3>
                <p>A brain tumor is an abnormal growth of cells in the brain.</p>

                <h3>What are the common symptoms of a brain tumor?</h3>
                <p>Symptoms can include headaches, seizures, vision problems, and changes in behavior or personality.</p>

                <h3>What causes brain tumors?</h3>
                <p>The exact cause is often unknown, but genetic factors and exposure to radiation may play a role.</p>

                <h3>How are brain tumors diagnosed?</h3>
                <p>Diagnosis typically involves imaging tests like MRI or CT scans, and sometimes a biopsy.</p>

                <h3>What are the different types of brain tumors?</h3>
                <p>Brain tumors can be benign (non-cancerous) or malignant (cancerous), and they are classified based on the type of cells involved.</p>

                <h3>What treatment options are available for brain tumors?</h3>
                <p>Treatment may include surgery, radiation therapy, chemotherapy, or a combination of these.</p>

                <h3>Can brain tumors be prevented?</h3>
                <p>There is no sure way to prevent brain tumors, but reducing exposure to radiation and maintaining a healthy lifestyle may help.</p>

                <h3>What is the prognosis for someone with a brain tumor?</h3>
                <p>Prognosis depends on the type, location, and stage of the tumor, as well as the patient's overall health.</p>

                <h3>How do brain tumors affect daily life?</h3>
                <p>Brain tumors can impact cognitive functions, physical abilities, and emotional well-being, requiring adjustments in daily activities.</p>

                <h3>What support resources are available for brain tumor patients and their families?</h3>
                <p>Support resources include counseling, support groups, and organizations dedicated to brain tumor research and patient assistance.</p>
            </div>
        </div>
    </section>

    <section id="info-understanding-cnn-unet">
        <h2 class="section-title">
            <span id="infoTitleUnderstandingCNNUNet" onclick="toggleInfoContent('infoContentUnderstandingCNNUNet', 'expandArrowUnderstandingCNNUNet')" style="cursor: pointer;">
                About Model<span id="expandArrowUnderstandingCNNUNet">▼</span> <!-- Arrow icon -->
            </span>
        </h2>
        <div id="infoContentUnderstandingCNNUNet" class="infoContent collapsed">
            <div>
                <h3>What are CNN and UNet models?</h3>
                <p>CNN (Convolutional Neural Network) and UNet are types of deep learning models used in image analysis and medical imaging.</p>
                <h3>How do CNN and UNet models work in the context of tumor detection?</h3>
                <p>These models analyze medical images to identify and segment tumors, helping in accurate diagnosis and treatment planning.</p>
                <h3>What are the advantages of using CNN and UNet models for tumor detection?</h3>
                <p>They provide high accuracy, can handle large datasets, and automate the detection process, reducing human error.</p>
                <h3>How accurate are CNN and UNet models in detecting tumors?</h3>
                <p>These models are highly accurate, often outperforming traditional methods, but their performance depends on the quality of the training data.</p>
                <h3>What datasets are commonly used to train CNN and UNet models for tumor detection?</h3>
                <p>Common datasets include medical imaging databases like the Brain Tumor Segmentation (BraTS) dataset.</p>
                <h3>How do CNN and UNet models handle different types of tumors?</h3>
                <p>They can be trained to recognize various tumor types by learning from labeled images of different tumor categories.</p>
                <h3>What are the limitations of CNN and UNet models in tumor detection?</h3>
                <p>Limitations include the need for large, annotated datasets and potential biases in the training data.</p>
                <h3>How is the performance of CNN and UNet models evaluated?</h3>
                <p>Performance is evaluated using metrics like accuracy, sensitivity, specificity, and the Dice coefficient.</p>
                <h3>What are some recent advancements in CNN and UNet models for tumor detection?</h3>
                <p>Recent advancements include improved architectures, transfer learning, and the integration of multimodal data.</p>
                <h3>How can CNN and UNet models be integrated into clinical practice for tumor detection?</h3>
                <p>They can be integrated through software applications that assist radiologists in analyzing medical images and making diagnostic decisions.</p>
            </div>
        </div>
    </section>



    <script>
        function toggleInfoContent(contentId, arrowId) {
            const content = document.getElementById(contentId);
            const arrow = document.getElementById(arrowId);

            if (content.style.display === "none" || content.classList.contains("collapsed")) {
                content.style.display = "block"; // Show content
                content.classList.remove("collapsed");
                content.classList.add("expanded");
                arrow.style.transform = "rotate(180deg)"; // Rotate arrow down
            } else {
                content.style.display = "none"; // Hide content
                content.classList.remove("expanded");
                content.classList.add("collapsed");
                arrow.style.transform = "rotate(0deg)"; // Rotate arrow up
            }
        }
    </script>


    <!-- Interactive Brain Model Section -->
    <section class="interactive-section">
        <h2 class="section-title">Interactive Brain Model</h2>
        <p class="section-description">
            Explore the human brain in 3D with this interactive model. This tool allows you to navigate through different parts of the brain, helping you understand its structure and functions. It's an excellent resource for anyone interested in learning more about the brain's anatomy, and it can be particularly informative for users looking to understand brain tumors and their relation to brain structures.
        </p>
        <div class="interactive-container">
            <iframe src="https://www.brainfacts.org/3d-brain#intro=false&focus=Brain" title="Interactive 3D Brain Model" class="iframe" style="height: 600px;" loading="lazy"></iframe>
        </div>
    </section>


  </main>

  <?php require "footer.php"; ?>

</body>
</html>

