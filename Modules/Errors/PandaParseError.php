<?php 

namespace Errors;

// PandaParseError koristi se kada dođe do greške prilikom kompajliranja
// programskog/template jezika PandaSQL u PHP
class PandaParseError extends \Exception {
    protected int $line_number;

    public function __construct($error_message, $line_number) {
        parent::__construct('PandaSQL Parse error: '. $error_message .' on line '. $line_number);
        $this->line_number = $line_number;
    }
}

?>