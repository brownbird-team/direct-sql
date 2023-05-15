<?php

$functions = [];

// Funkcija za zbrajanje
function build_in_function_hello() {
    return 'Hello World !';
}

$functions['hello'] = [
    'real_name' => 'build_in_function_hello',
    'arguments' => [],
    'returns' => 'STRING'
];

function build_in_function_nothingness() {}

$functions['nothingness'] =  [
    'real_name' => 'build_in_function_nothingness',
    'arguments' => [],
    'returns' => 'NONE'
];