# Panda SQL

Panda SQL je sučelje za izradu web stranica pomoću posebnog pojednostavljenog jezika koji omogućuje upis SQL naredbi direktno u HTML dokument, te na taj način pojednostavljuje učenje koncepata koji se koriste pri izradi web stranica spojenih na MySQL bazu podataka. Taj jezik se kasnije kompajlira u PHP i izvršava na serveru.

Izrada Panda SQL-a je u tijeku te će svi detalji biti napisani kasnije.

## Neke informacije o PandaSQL jeziku

Osnovna sintaksa i naredbe pandaSQL jezika. Neke naredbe koje compiler podržava nisu napisane ispod, kao što je izvršavanje build-in funkcija.

### SQL Upiti

Opis strukture SQL bloka

```{% sql query SELECT * FROM table %}```

Definira query koji izvršavamo

```{% sql empty %}```

Ispisuje se ako nema rezultata
(sve je prošlo OK ali nema ispisa npr za INSERT)

```{% sql error %}```

Ispisuje se ako je došlo do greške
(varijabla `$error` sadrži grešku)

```{% sql end %}```

Završetak SQL bloka

### Varijable

```$var```

globalna varijabla ista na cijeloj stranici
među ovim varijablama se nalaze i predefinirane
varijable koje su dostupne korisnicima

```$$var```

query varijabla, odnosi se na atribut prvog
upita iznad koji definira tu varijablu (samo unutar sql bloka)

```{% set $var_name value %}```

postavlja vrijednost varijable (ako izostavimo value postavlja ju na 1)

```{% delete $var_name %}```

briše varijablu iz memorije (ako je definirana)

### Osnovne naredbe za ispis

```{% print $var_name %}```

ispisuje sadržaj kao i print raw ali koristi htmlspecialchars

```{% printraw $var_name ' ja sam string ' $var2 %}```

ispisuje sadržaj varijabli brojeva stringova, točno onakve kakvi jesu

```{% file $var_name %}```

Ispiši link na file (blob objekt) iz baze

```{% image $var_name %}```

Ispiši varijablu kao link za sliku

```{{ $var_name }}```

shorthand za `{% print $var_name %}`

```{{{ $var_name }}}```

shorthand za `{% printraw $var_name %}`


### Logika

```{% if ($var_name and $var_name_2) or $var_name_3 %}```

Ako je varijabla definirana i nema false vrijednost (0, null)

```{% else %}```

Inače

```{% endif %}```

završi if blok
