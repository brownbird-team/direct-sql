<?php

// Konfguracijski file za beskorisnu aplikaciju

// ovo je samo uzorak za konfiguracijski file, kreirajte file
// pod imenom config.php u ovom direktoriju i u njega kopirajte
// sadržaj ovog file-a

// ----------------------- Database Connections -----------------------

// Korisnik koji se prijavljuje na bazu
$config['user'] = 'username';

// Password za tog korisnika
$config['pass'] = 'password';

// Ime baze podataka
$config['name'] = 'database';

// Hostname gdje se nalazi baza
$config['host'] = 'localhost';

// ----------------------- Table configuration ------------------------

$config['table_name'] = 'tabla1';
$config['table_fields'] = [
    'id', 'polje1', 'polje2', 'polje3'
];

?>