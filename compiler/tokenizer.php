<?php

// Unutar niza znakova moguće se nalaziti u jednom
// od sljedećih prostora
const HTML_STRING       = 0;
const SQL_QUERY_STRING  = 1;
const COMMAND_SPACE     = 2;

// Regexi koji se koriste pri određivanju tokena
const IS_LETTER = "/[a-z]/i";
const IS_NUMBER = "/[0-9]/i";
const IS_WHITESPACE = "/\s/";
const IS_NEWLINE = "/\n|\r/s";
const IS_NAME = "/_|[0-9]|[a-z]|-/i";
const IS_NAME_START = "/_|[a-z]|-/i";
const IS_PARENTHESIS = "/\(|\)/";

// Posebni znakovi
const QUOTE_CHARACTER = '\'';
const ESCAPE_CHARACTER = '\\';
const VARIABLE_START_CHARACTER = '$';

// Dobavi sve potrebne klase
require __DIR__ . '/tokenizer_classes.php';

// Funkcija koja pretvara niz znakova u niz tokena
// za kasniju obradu i pretvornu u AST
function tokenizer($input) {
    $tokens = [];
    $pointer = 0;
    $line_count = 1;
    $input_length = strlen($input);

    $buffer = '';
    $position = HTML_STRING;

    while ($pointer < $input_length) {
        $ch = $input[$pointer];

        if (preg_match(IS_NEWLINE, $ch))
            $line_count++;

        // Ako je napisan znak za povratak u HTML mode
        // vrati se u njega
        if (substr($input, $pointer, 2) == '%}') {

            if ($position == HTML_STRING) {
                throw new PandaParseError('found extra %}', $line_count);
            }

            if ($position == SQL_QUERY_STRING) {
                if (strlen($buffer) > 0)
                    $tokens[] = new TokenTypeSqlString($buffer, $line_count);

                $tokens[] = new TokenTypeSqlEnd($line_count);
            }

            $pointer += 2;
            $buffer = '';
            $position = HTML_STRING;
            continue;
        }

        if ($position == HTML_STRING) {
            if (substr($input, $pointer, 2) == '{%') {
                $position = COMMAND_SPACE;

                if (strlen($buffer) > 0) {
                    $tokens[] = new TokenTypeHtmlString($buffer, $line_count);
                }

                $pointer += 2;
            } else {
                $buffer .= $ch;
                $pointer++;
            }
            continue;
        }

        if ($position == COMMAND_SPACE) {
            // Ako je napisana zagrada
            if (preg_match(IS_PARENTHESIS, $ch)) {
                $tokens[] = new TokenTypeParenthesis($ch, $line_count);
                $pointer++;
                continue;
            }
            // Ako je napisana naredba
            if (preg_match(IS_NAME_START, $ch)) {
                $buffer = '';

                while (preg_match(IS_NAME, $ch)) {
                    $buffer .= $ch;
                    $ch = $input[++$pointer];
                }

                $tokens[] = new TokenTypeCommand($buffer, $line_count);

                // Ako je napisana naredba naredba za početak upita
                // uđi u mode za upit
                if ($buffer == 'query') {
                    $position = SQL_QUERY_STRING;
                    $buffer = '';
                    $tokens[] = new TokenTypeSqlStart($line_count);
                }

                continue;
            }
            // Ako je napisan broj
            if (preg_match(IS_NUMBER, $ch)) {
                $buffer = '';

                while (preg_match(IS_NUMBER, $ch)) {
                    $buffer .= $ch;
                    $ch = $input[++$pointer];
                }

                $tokens[] = new TokenTypeNumber($buffer, $line_count);

                continue;
            }
            // Ako je napisan string
            if ($ch == QUOTE_CHARACTER) {
                $buffer = '';
                $ch = $input[++$pointer];
                $start_line = $line_count;

                while ($ch !== QUOTE_CHARACTER) {

                    if (preg_match(IS_NEWLINE, $ch))
                        $line_count++;

                    if ($pointer == $input_length - 1)
                        throw new PandaParseError('Failed to find matching quote for string', $start_line);

                    if ($ch == ESCAPE_CHARACTER) {
                        $ch = $input[++$pointer];

                        if ($ch == ESCAPE_CHARACTER || $ch == QUOTE_CHARACTER) {
                            $buffer .= $ch;
                            $ch = $input[++$pointer];
                            continue;
                        }

                        throw new PandaParseError('Character "'. $ch .'" cannot be escaped', $line_count);
                    }

                    $buffer .= $ch;
                    $ch = $input[++$pointer];
                }

                $pointer++;
                $tokens[] = new TokenTypeString($buffer, $start_line);

                continue;
            }
        }

        if ($ch == VARIABLE_START_CHARACTER) {
            $var_type = 'GLOBAL';
            $name_start = $pointer + 1;

            if ($input[$name_start] == VARIABLE_START_CHARACTER) {
                $name_start += 1;
                $var_type = 'QUERY';
            }

            if (preg_match(IS_NAME_START, $input[$name_start])) {
                $pointer = $name_start;

                if ($position == SQL_QUERY_STRING && strlen($buffer) > 0)
                    $tokens[] = new TokenTypeSqlString($buffer, $line_count);
                
                $buffer = '';
                $ch = $input[$pointer];

                while (preg_match(IS_NAME, $ch)) {
                    $buffer .= $ch;
                    $ch = $input[++$pointer];
                }

                $tokens[] = new TokenTypeVariable($buffer, $var_type, $line_count);

                $buffer = '';
                continue;
            }

            if ($position == COMMAND_SPACE) {
                throw new PandaParseError('Variable name cannot start with character "'. $input[$name_start] .'"', $line_count);
            }
        }

        if ($position == SQL_QUERY_STRING) {
            $buffer .= $ch;
            $pointer++;
            continue;
        }

        if (preg_match(IS_WHITESPACE, $ch)) {
            $pointer++;
            continue;
        }

        throw new PandaParseError('Unexpected character "'. $ch .'" found in command mode', $line_count);
    }

    if ($position !== HTML_STRING) {
        throw new PandaParseError('Error: Failed to find matching %}', $line_count);
    }

    if (strlen($buffer) > 0) {
        $tokens[] = new TokenTypeHtmlString($buffer, $line_count);
    }

    return $tokens;
}

$test_string = file_get_contents(__DIR__ . '/input.psql');

try {
    $result = tokenizer($test_string); 
    var_dump($result);

    echo "\n\n---------------------------\n\n";

    echo $result[10]->get_type();
    echo "\n";
    echo $result[10]->get_var_type();

    echo "\n\n";
} catch (PandaParseError $e) {
    echo $e->errorMessage() . "\n\n";
}

?>