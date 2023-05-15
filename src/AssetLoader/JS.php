<?php

namespace AssetLoader;

class JS {
    private static $files_array = [];

    public static function include_file($file_path) {
        $files_array[] = $file_path;
    }

    public static function print_links() {
        foreach ($files_array as $file) {
            echo '<script src="'. $file .'" ></script>';
        }
    }
}