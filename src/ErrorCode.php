<?php

// Skup svih ERROR kodova koji se koriste u aplikaciji

enum ExceptionCode {

    case OK;               // Sve je O.K.

    case USER_EXISTS;      // Nije moguće kreirati korisnika (već postoji)
    case USER_NOT_FOUND;   // Traženi korisnik ne postoji
    case ACCESS_DENIED;    // Pristup odbijen username ili password neispravan
    case EMAIL_INVALID;    // Email nije prošao regex test

}