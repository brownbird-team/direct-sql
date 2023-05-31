<?php

namespace Environment;
use Errors\PandaExecuteError;

class VariableControl {

    private $next_query;  // Mjesto u stacku na koje će biti ubačen sljedeći unos
    private $query_stack; // Stack svih trenutnih lista query varijabli
    private $global_vars; // Lista globalnih varijabli

    // Provjeri je li dana varijabla danog tipa
    private function check_type($var, $type, $line, $page) {
        $vtype = gettype($var);

        switch ($type) {
            case 'ANY':
                return; break;
            case 'STRING':
                if (
                    $vtype === 'string'
                ) return; break;
            case 'NUMBER':
                if (
                    $vtype === 'integer' || $vtype === 'boolean'
                ) return; break;

            case 'NONE':
                throw new PandaExecuteError('Variable cannot have value of "NONE" (THIS IS SYSTEM ERROR, REPORT TO ADMIN)', $line, $page);
            default:
                throw new PandaExecuteError('Given variable type does not exist (THIS IS SYSTEM ERROR, REPORT TO ADMIN)', $line, $page);
        }

        throw new PandaExecuteError('Variable of wrong type used', $line);
    }

    public function __construct() {
        $this->next_query = 0;
        $this->query_stack = [];
        $this->global_vars = [];
    }
    
    // Ubaci array trenutnog retka
    public function push_query(array $vars) {
        $this->query_stack[$this->next_query++] = $vars;
    }

    // Dobavi query varijablu i provjeri je li odgovarajućeg tipa
    public function get_query(string $name, string $type, int $line, string $page) {

        for ($i = $this->next_query - 1; $i >= 0; $i--) {
            if (isset($this->query_stack[$i][$name])) {
                $this->check_type($this->query_stack[$i][$name], $type, $line, $page);
                return $this->query_stack[$i][$name];
            }
        }

        throw new PandaExecuteError('Trying to access value of non-existent QUERY variable $$'. $name, $line, $page);
    }

    // Izbaci zadnji dodani array varijabli
    public function pop_query() {
        unset($this->query_stack[--$this->next_query]);
    }

    // Dodaj globalnu varijablu
    public function set_global(string $name, $value, int $line, string $page) {
        $this->global_vars[$name] = $value;
    }

    // Pobriši globalnu varijablu
    public function delete_global(string $name, int $line, string $page) {
        if (!isset($this->global_vars[$name]))
            throw new PandaExecuteError('Trying to delete non-existent GLOBAL variable $'. $name, $line, $page);

        unset($this->global_vars[$name]);
    }

    // Dobavi globalnu varijablu i provjeri je li odgovarajućeg tipa
    public function get_global(string $name, string $type, int $line, string $page) {

        if (!isset($this->global_vars[$name]))
            throw new PandaExecuteError('Trying to access value of non-existent GLOBAL variable $'. $name, $line, $page);

        $this->check_type($this->global_vars[$name], $type, $line, $page);

        return $this->global_vars[$name];
    }
}