<?php

// Check if the 'pdf' key exists in the POST request and file is uploaded
if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/'; // Define the directory to store the uploaded files
    $fileName = $_FILES['pdf']['name']; // Get the original file name
    $fileTmpName = $_FILES['pdf']['tmp_name']; // Get the temporary file path
    $filePath = $uploadDir . $fileName; // Define the destination path
    
    // Ensure the 'uploads' folder exists and is writable
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the folder if it doesn't exist
    }

    // Move the uploaded file to the desired location
    if (move_uploaded_file($fileTmpName, $filePath)) {
        // Respond with success and the file path for the download link
        echo json_encode(['success' => true, 'filePath' => $filePath]);
    } else {
        // Respond with failure if the file couldn't be moved
        echo json_encode(['success' => false, 'message' => 'Failed to save the PDF']);
    }
} else {
    // Handle error if the file wasn't uploaded properly
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
}

?>
