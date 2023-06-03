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

    public function __construct($type, $line_number, $page_name) {
        $this->type = $type;
        $this->line_number = $line_number;
        $this->page_name = $page_name;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_line() {
        return $this->line_number;
    }

    public function get_page() {
        return $this->page_name;
    }
}

// Klasa koja predstavlja token koji ima neku vrijednost
class TokenTypeValue extends TokenType {
    protected $value;

    public function __construct($type, $value, $line_number, $page_name) {
        parent::__construct($type, $line_number, $page_name);
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
    function __construct($value, $line_number, $page_name) {
        parent::__construct('HTML_STRING', $value, $line_number, $page_name);
    }
}

// Običan broj (trenutno samo pozitivni INT podržan)
class TokenTypeNumber extends TokenTypeValue {
    function __construct($value, $line_number, $page_name) {
        parent::__construct('NUMBER', $value, $line_number, $page_name);
    }
}

// Običan niz znakova (string)
class TokenTypeString extends TokenTypeValue {
    function __construct($value, $line_number, $page_name) {
        parent::__construct('STRING', $value, $line_number, $page_name);
    }
}

// Naredba ili ime predefinirane funkcije
class TokenTypeName extends TokenTypeValue {
    function __construct($value, $line_number, $page_name) {
        parent::__construct('NAME', $value, $line_number, $page_name);
    }
}

// Običan niz znakova (string)
class TokenTypeVariable extends TokenTypeValue {
    protected $variable_type;

    function __construct($value, $var_type, $line_number, $page_name) {
        parent::__construct('VARIABLE', $value, $line_number, $page_name);

        $this->variable_type = $var_type;
    }

    function get_var_type() {
        return $this->variable_type;
    }
}

// Jedan od tipova zagrade
class TokenTypeParenthesis extends TokenType {
    protected $parenthesis_type;

    function __construct($value, $line_number, $page_name) {
        parent::__construct('PARENTHESIS', $line_number, $page_name);

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
    function __construct($line_number, $page_name) {
        parent::__construct('SQL_START', $line_number, $page_name);
    }
}

// Označava kraj SQL query-a
class TokenTypeSqlEnd extends TokenType {
    function __construct($line_number, $page_name) {
        parent::__construct('SQL_END', $line_number, $page_name);
    }
}

// Jedan od dijelova danog SQL query-a
// ako se u query-u nalazi varijabla bit će podjeljen na više dijelova
// između kojih je variable token
class TokenTypeSqlString extends TokenTypeValue {
    function __construct($value, $line_number, $page_name) {
        parent::__construct('SQL_STRING', $value, $line_number, $page_name);
    }
}

class TokenTypeCommandEnd extends TokenType {
    function __construct($line_number, $page_name) {
        parent::__construct('COMMAND_END', $line_number, $page_name);
    }
}

// Token označava kraj programa
class TokenTypeProgramEnd extends TokenType {
    function __construct($line_number, $page_name) {
        parent::__construct('END', $line_number, $page_name);
    }
}

?>