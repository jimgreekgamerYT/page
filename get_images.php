<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$imageDir = 'NM_photos/';
$images = [];

foreach (glob($imageDir . '*.*') as $file) {
    $filename = basename($file);
    $metadata = [
        'filename' => $filename,
        'satellite' => 'Unknown',
        'type' => 'APT',
        'frequency' => '137.50 MHz',
        'date' => date('Y-m-d', filemtime($file)),
        'time' => date('H:i', filemtime($file)) . ' UTC',
        'note' => 'Automatically processed'
    ];

    // Extract from filename if possible
    if (preg_match('/(noaa|meteor).*apt/i', $filename, $matches)) {
        $metadata['satellite'] = strtoupper($matches[1]);
        $metadata['type'] = 'APT';
    } elseif (preg_match('/(meteor).*lrpt/i', $filename)) {
        $metadata['satellite'] = 'METEOR-M2';
        $metadata['type'] = 'LRPT';
    }

    $images[] = $metadata;
}

// Sort by capture time (newest first)
usort($images, function($a, $b) {
    return strtotime($b['date'] . ' ' . $b['time']) - strtotime($a['date'] . ' ' . $a['time']);
});

echo json_encode($images);
?>
