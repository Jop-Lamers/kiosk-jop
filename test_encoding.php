<?php
// Test rawurlencode with UTF-8 filenames
$testFilename = 'The Supergreen Harvest (VG) – €9.50 (310 kcal).webp';
$encoded = rawurlencode($testFilename);

echo "Original: " . htmlspecialchars($testFilename) . "\n";
echo "Encoded: " . htmlspecialchars($encoded) . "\n";
echo "URL: menu-images/" . $encoded . "\n\n";

// Try to check if file exists
$path = __DIR__ . '/menu-images/' . $testFilename;
echo "File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
