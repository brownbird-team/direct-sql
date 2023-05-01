<?php 

namespace Errors;

// PandaExecuteError koristi se kada dođe do greške prilikom izvršavanja
// kompajliranog PandaSQL jezika (PHP-a)
class PandaExecuteError extends \Exception {
    protected int $line_number;

    public function __construct($error_message, $line_number) {
        parent::__construct('PandaSQL Execute error: '. $error_message .' on line '. $line_number);
        $this->line_number = $line_number;
    }
}

?>