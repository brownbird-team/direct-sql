<?php

namespace Functions;

class Loader {
    public static $function_array = [];

    public static function exists(string $name) {
        return isset(self::$function_array[$name]);
    }
    public static function get_function(string $name) {
        return self::$function_array[$name];
    }
    public static function get_real_name(string $name) {
        return self::$function_array[$name]['real_name'];
    }
    public static function get_arguments(string $name) {
        return self::$function_array[$name]['arguments'];
    }
    public static function get_arguments_count($name) {
        return count(self::$function_array[$name]['arguments']);
    }
    public static function get_return_type(string $name) {
        return self::$function_array[$name]['returns'];
    }
}

// Sve build-in funkcije nalaze se u function_arrays subdirektoriju
$files = glob(__DIR__ . '/function_arrays/*.php');

foreach ($files as $file) {
    // Require functions file
    require $file;
    // U svakom file-u nalazi se array functions, dodaj ga u glavni array
    Loader::$function_array = array_merge(Loader::$function_array, $functions);
}

//var_dump($function_array);

?>