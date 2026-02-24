<?php
require_once 'config/db.php';
$conn->set_charset("utf8mb4");

echo "<h2>Fixing Image Links</h2>";

// 1. Get all actual files
$dir = 'menu-images';
$files = scandir($dir);
$realFiles = [];
foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    $realFiles[] = $f;
}

// 2. Get DB Images
$sql = "SELECT image_id, filename, description FROM images";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $dbName = $row['filename'];
    $id = $row['image_id'];
    $desc = $row['description']; // This is usually the product name

    // Check exact match
    if (in_array($dbName, $realFiles)) {
        echo "✅ Exact match: $dbName<br>";
        continue;
    }

    // Helper: sanitize strings for matching
    $sanitize = function ($s) {
        // Remove leading dashes/spaces, normalize unicode dashes, trim
        $s = preg_replace('/^[\-\–\—\s]+/u', '', $s);
        return trim($s);
    };

    // Remove diet tags like (VG) or (V) from description for matching
    $descBase = preg_replace('/\s*\((VG|V)\)\s*/i', '', $desc);
    $descBase = $sanitize($descBase);

    // 1) Try strong prefix match: file starts with product name
    $prefixMatch = '';
    foreach ($realFiles as $rf) {
        $rfSan = $sanitize($rf);
        if (stripos($rfSan, $descBase) === 0) {
            $prefixMatch = $rf;
            break;
        }
        // Also allow optional leading dash in file names
        if (stripos($rfSan, $descBase) === 2 && strpos($rfSan, '- ') === 0) {
            $prefixMatch = $rf;
            break;
        }
    }

    if ($prefixMatch) {
        echo "🔧 Prefix match for ID $id: <br>Old: $dbName <br>New: $prefixMatch<br>";
        $stmt = $conn->prepare("UPDATE images SET filename = ? WHERE image_id = ?");
        $stmt->bind_param("si", $prefixMatch, $id);
        $stmt->execute();
        echo "Updated.<br><br>";
        continue;
    }

    // Try to find a match
    $bestMatch = '';
    $highestSim = 0;

    foreach ($realFiles as $rf) {
        // Consider sanitized similarity to handle dash/euro issues
        $dbSan = $sanitize($dbName);
        $rfSan = $sanitize($rf);
        similar_text($dbSan, $rfSan, $perc);
        if ($perc > $highestSim) {
            $highestSim = $perc;
            $bestMatch = $rf;
        }

        // Specialized check for the "leading dash" issue
        // If DB has "- Fruit" and File has "Fruit" or similar
        // Or if encoding of Euro symbol is different
    }

    if ($highestSim > 80) {
        echo "🔧 Fixing ID $id: <br>Old: $dbName <br>New: $bestMatch <br> (Sim: $highestSim%)<br>";

        $stmt = $conn->prepare("UPDATE images SET filename = ? WHERE image_id = ?");
        $stmt->bind_param("si", $bestMatch, $id);
        $stmt->execute();
        echo "Updated.<br><br>";
    } else {
        echo "❌ Could not find reliable match for: $dbName (Best: $bestMatch - $highestSim%)<br>";
    }
}
