<?php

class Helpers {
    public static function random_string($length) {
        
        $characters = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        $characters_length = strlen($characters);

        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= $characters[random_int(0, $characters_length - 1)];
        }

        return $str;
    }
}