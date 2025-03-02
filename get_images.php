<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $imageDir = 'NM_photos/';
    
    if (!is_dir($imageDir)) {
        throw new Exception("Folder $imageDir does not exist");
    }

    $images = [];
    $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    foreach (scandir($imageDir) as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, $validExtensions)) continue;

        $images[] = [
            'filename' => $file,
            'date' => date('Y-m-d', filemtime($imageDir.$file)),
            'time' => date('H:i:s', filemtime($imageDir.$file)).' UTC'
        ];
    }

    if (empty($images)) {
        throw new Exception("No images found in $imageDir");
    }

    echo json_encode([
        'status' => 'success',
        'data' => $images
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
