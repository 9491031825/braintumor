<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $url = 'http://127.0.0.1:5000/predict';
    $imagePath = $_FILES['file']['tmp_name'];

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    // Attach the uploaded file
    $cfile = curl_file_create($imagePath, 'image/png', 'file');
    $data = ['file' => $cfile];
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));

    // Execute cURL request
    $response = curl_exec($ch);
    if ($response === false) {
        echo json_encode(['error' => curl_error($ch)]);
    } else {
        // Decode the response from the prediction API
        $result = json_decode($response, true);
        
        if (isset($result['tumor_area_pixels']) && isset($result['total_area_pixels'])) {
            echo json_encode([
                'tumor_area_pixels' => $result['tumor_area_pixels'],
                'total_area_pixels' => $result['total_area_pixels'],
                'uploaded_image' => $result['uploaded_image'],
                'prediction' => $result['prediction'],
                'overlay' => $result['overlay']
            ]);
        } else {
            echo json_encode(['error' => 'Unexpected response format from prediction API.']);
        }
    }
    curl_close($ch);
} else {
    echo json_encode(['error' => 'No file uploaded or invalid request method.']);
}
