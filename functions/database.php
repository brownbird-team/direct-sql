<?php

$config['user'] = 'username';
$config['pass'] = 'password';
$config['name'] = 'database';
$config['host'] = 'localhost';

include __DIR__ . '/../config/config.php';

$conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);

if ($conn -> connect_error)
    die('Something is wrong with the database');

?>