<?php

namespace Errors;

// 
class SqlQueryError extends \Exception {
    public function __construct(\mysqli_sql_exception $e, $line = null, $page = null) {
        parent::__construct(
            'SQL STATE ('. $e->getSqlState() .'): Error occured while executing SQL query'. 
            (($page !== null) ? (' in page "' . $page . '"') : '').
            (($line !== null) ? (' on line ' . $line) : '') . ': '. $e->getMessage()
        );
    }
}

?>