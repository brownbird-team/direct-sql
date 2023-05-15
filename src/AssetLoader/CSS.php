<?php

namespace AssetLoader;

class CSS {
    private static $files_array = [];

    public static function include_file($file_path) {
        self::$files_array[] = $file_path;
    }

    public static function print_links() {
        foreach (self::$files_array as $file) {
            echo '<link type="text/css" rel="stylesheet" href="./public/css/'. $file .'" />';
        }
    }
}