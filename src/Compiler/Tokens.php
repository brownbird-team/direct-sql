<?php

namespace Compiler\Tokens;
use \Errors\PandaParseError;

// -------------------------------------------------------------------------- //
//                          Generični tipovi tokena                           //
// -------------------------------------------------------------------------- //

// Klasa koja predstavlja bilo koji token
// (pohranjuje tip tokena i broj linije gdje je nađen)
class TokenType {
    protected $type;
    protected $line_number;

    public function __construct($type, $line_number) {
        $this->type = $type;
        $this->line_number = $line_number;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_line() {
        return $this->line_number;
    }
}

// Klasa koja predstavlja token koji ima neku vrijednost
class TokenTypeValue extends TokenType {
    protected $value;

    public function __construct($type, $value, $line_number) {
        parent::__construct($type, $line_number);
        $this->value = $value;
    }

    public function get_value() {
        return $this->value;
    }
}


// -------------------------------------------------------------------------- //
//                            Klase tipova tokena                             //
// -------------------------------------------------------------------------- //

// Niz znakova HTML-a, sav tekst koji se nalazi van {%  %}
class TokenTypeHtmlString extends TokenTypeValue {
    function __construct($value, $line_number) {
        parent::__construct('HTML_STRING', $value, $line_number);
    }
}

// Običan broj (trenutno samo pozitivni INT podržan)
class TokenTypeNumber extends TokenTypeValue {
    function __construct($value, $line_number) {
        parent::__construct('NUMBER', $value, $line_number);
    }
}

// Običan niz znakova (string)
class TokenTypeString extends TokenTypeValue {
    function __construct($value, $line_number) {
        parent::__construct('STRING', $value, $line_number);
    }
}

// Naredba ili ime predefinirane funkcije
class TokenTypeName extends TokenTypeValue {
    function __construct($value, $line_number) {
        parent::__construct('NAME', $value, $line_number);
    }
}

// Običan niz znakova (string)
class TokenTypeVariable extends TokenTypeValue {
    protected $variable_type;

    function __construct($value, $var_type, $line_number) {
        parent::__construct('VARIABLE', $value, $line_number);

        $this->variable_type = $var_type;
    }

    function get_var_type() {
        return $this->variable_type;
    }
}

// Jedan od tipova zagrade
class TokenTypeParenthesis extends TokenType {
    protected $parenthesis_type;

    function __construct($value, $line_number) {
        parent::__construct('PARENTHESIS', $line_number);

        if ($value == '(') {
            $this->parenthesis_type = 'LEFT';
        } else if ($value == ')') {
            $this->parenthesis_type = 'RIGHT';
        } else {
            throw new PandaParseError('Internal error, cannot create class for parenthesis token because spacified value is not parenthesis', __LINE__);
        }
    }

    function get_par_type() {
        return $this->parenthesis_type;
    }
}

// Označava početak SQL query-a
class TokenTypeSqlStart extends TokenType {
    function __construct($line_number) {
        parent::__construct('SQL_START', $line_number);
    }
}

// Označava kraj SQL query-a
class TokenTypeSqlEnd extends TokenType {
    function __construct($line_number) {
        parent::__construct('SQL_END', $line_number);
    }
}

// Jedan od dijelova danog SQL query-a
// ako se u query-u nalazi varijabla bit će podjeljen na više dijelova
// između kojih je variable token
class TokenTypeSqlString extends TokenTypeValue {
    function __construct($value, $line_number) {
        parent::__construct('SQL_STRING', $value, $line_number);
    }
}

class TokenTypeCommandEnd extends TokenType {
    function __construct($line_number) {
        parent::__construct('COMMAND_END', $line_number);
    }
}

// Token označava kraj programa
class TokenTypeProgramEnd extends TokenType {
    function __construct($line_number) {
        parent::__construct('END', $line_number);
    }
}

?>