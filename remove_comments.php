<?php

$migrationsDir = __DIR__ . '/database/migrations';
$files = scandir($migrationsDir);

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $filePath = $migrationsDir . '/' . $file;
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

echo "All comments have been removed from migration files.\n";
