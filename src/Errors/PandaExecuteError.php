<?php 

namespace Errors;

// PandaExecuteError koristi se kada dođe do greške prilikom izvršavanja
// kompajliranog PandaSQL jezika (PHP-a)
class PandaExecuteError extends \Exception {
    protected int $line_number;

    public function __construct($error_message, $line_number = null, $page_name = null) {
        parent::__construct(
            'PandaSQL Execute error: '. $error_message .
            (($page_name !== null) ? (' in page "' . $page_name . '"') : '') .
            (($line_number !== null) ? (' on line '. $line_number) : '')
        );
        $this->line_number = $line_number;
    }
}

?>