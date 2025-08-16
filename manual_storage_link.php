<?php

$target = __DIR__.'/storage/app/public';
$link = __DIR__.'/public/storage';

if (file_exists($link)) {
    echo "Link already exists.\n";
} else {
    if (!file_exists($target)) {
        mkdir($target, 0755, true);
    }
    if (mkdir($link, 0755, true)) {
        echo "Directory created successfully.\n";
        
        // Copy contents
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                mkdir($link . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), 0755, true);
            } else {
                copy($item, $link . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
        
        echo "Contents copied successfully.\n";
    } else {
        echo "Failed to create directory.\n";
    }
}