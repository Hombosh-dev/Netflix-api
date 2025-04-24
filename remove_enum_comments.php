<?php

$enumsDir = __DIR__ . '/app/Enums';
$files = scandir($enumsDir);

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $filePath = $enumsDir . '/' . $file;
        $content = file_get_contents($filePath);
        
        // Remove doc comments (/** ... */)
        $content = preg_replace('/\/\*\*[\s\S]*?\*\//', '', $content);
        
        // Remove single-line comments (// ...)
        $content = preg_replace('/\/\/.*/', '', $content);
        
        // Remove hash comments (# ...)
        $content = preg_replace('/#.*/', '', $content);
        
        // Save the file
        file_put_contents($filePath, $content);
        
        echo "Removed comments from $file\n";
    }
}

// Also remove comments from language enum files
$langFiles = [
    __DIR__ . '/lang/en/enums.php',
    __DIR__ . '/lang/uk/enums.php'
];

foreach ($langFiles as $filePath) {
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Remove single-line comments (// ...)
        $content = preg_replace('/\/\/.*/', '', $content);
        
        // Remove hash comments (# ...)
        $content = preg_replace('/#.*/', '', $content);
        
        // Save the file
        file_put_contents($filePath, $content);
        
        echo "Removed comments from " . basename($filePath) . "\n";
    }
}

echo "All comments have been removed from enum files.\n";
