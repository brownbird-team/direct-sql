<?php

namespace Errors;

// 
class InternalServerError extends \Exception {
    public function __construct($error_message) {
        parent::__construct('Internal server error: '. $error_message);
    }
}

?>