<?php

// Skup svih ERROR kodova koji se koriste u aplikaciji

enum StatusCode {

    case OK;                    // Sve je O.K.

    case USER_NOT_FOUND;        // Traženi korisnik ne postoji
    case USER_ALREADY_EXISTS;   // Nije moguće kreirati korisnika (već postoji)
    case USER_UNDEFINED;        // Klasa user nije definirana, a pozvana je metoda za upravljanje korisnikom
    case USER_ALREADY_VERIFIED; // Nemoguće verificirati korisnika i kreirati mu bazu ako je već verificiran
    case ACCESS_DENIED;         // Pristup odbijen username ili password neispravan
    case EMAIL_INVALID;         // Email nije prošao regex test

    case PAGE_NOT_FOUND;        // Tražena stranica ne postoji

    case INVALID_USER_DATABASE; // Nešto ne valja sa shemom baze korisnika pandasql tablice su neispravne
}