<?php

namespace Errors;

// 
class InternalException extends \Exception {
    private $error_code;

    public function __construct($code, $message = null) {
        parent::__construct(
            'Internal exception ERROR CODE ('. 
            str_pad((string)(int)$code, 4, "0", STR_PAD_LEFT) .
            '): '. (($message) ?? 'no message')
        );

        $this->error_code = $code;
    }

    public function get_error_code() {
        return $this->error_code;
    }
}

?>