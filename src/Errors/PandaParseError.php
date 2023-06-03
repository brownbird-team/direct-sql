<?php 

namespace Errors;

// PandaParseError koristi se kada dođe do greške prilikom kompajliranja
// programskog/template jezika PandaSQL u PHP
class PandaParseError extends \Exception {
    public function __construct($error_message, $line_number, $page_name) {
        parent::__construct('PandaSQL Parse error: '. $error_message .' in page "'. $page_name .'" on line '. $line_number);
    }
}

?>