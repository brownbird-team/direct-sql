<?php

$functions = [];

// Funkcija za zbrajanje
function build_in_function_add(int $a, int $b) {
    return $a + $b;
}

$functions['add'] = [
    'real_name' => 'build_in_function_add',
    'arguments' => [ 'NUMBER', 'NUMBER' ],
    'returns' => 'NUMBER'
];

// Funkcija za oduzimanje
function build_in_function_sub(int $a, int $b) {
    return $a - $b;
}

$functions['sub'] = [
    'real_name' => 'build_in_function_sub',
    'arguments' => [ 'NUMBER', 'NUMBER' ],
    'returns' => 'NUMBER'
];

// Funkcija za množenje
function build_in_function_mul(int $a, int $b) {
    return $a * $b;
}

$functions['mul'] = [
    'real_name' => 'build_in_function_mul',
    'arguments' => [ 'NUMBER', 'NUMBER' ],
    'returns' => 'NUMBER'
];

// Funkcija za djeljenje
function build_in_function_div(int $a, int $b) {
    return $a / $b;
}

$functions['div'] = [
    'real_name' => 'build_in_function_div',
    'arguments' => [ 'NUMBER', 'NUMBER' ],
    'returns' => 'NUMBER'
];

?>