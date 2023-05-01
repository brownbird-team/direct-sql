<?php

function autoload($class_path) {
    $path = str_replace('\\', '/', $class_path);
    $dir_name = dirname($path);

    // Provjeri postoji li folder za namespace i file za klasu unutar njega
    $class_file = __DIR__ . '/Modules/' . $path . '.php';
    if (file_exists($class_file)) {
        require $class_file;
        return;
    }
    // Provjeri postoji li folder za namespace i index.php unutar njega
    $dir_index_file = __DIR__ . '/Modules/' . $dir_name . '/index.php';
    if (file_exists($dir_index_file)) {
        require $dir_index_file;
        return;
    }
    // Provjeri postoji li file za namespace
    $namespace_file = __DIR__ . '/Modules/' . $dir_name . '.php';
    if ($path !== '.' && file_exists($namespace_file)) {
        require $namespace_file;
        return;
    }
}

spl_autoload_register('autoload');

?>