<?php

namespace AssetLoader;

const CSS_DIRECTORY_PATH = './public/css/';

class CSS {
    private static $files_array = [];
    private static $external_files_array = [];

    public static function include_file($file_path) {
        self::$files_array[] = $file_path;
    }

    public static function include_external_file($link) {
        self::$external_files_array[] = $link;
    }

    public static function print_links() {
        foreach (self::$external_files_array as $file) {
            echo '<link type="text/css" rel="stylesheet" href="'. $file .'" />';
        }
        foreach (self::$files_array as $file) {
            echo '<link type="text/css" rel="stylesheet" href="'. CSS_DIRECTORY_PATH . $file .'" />';
        }
    }
}