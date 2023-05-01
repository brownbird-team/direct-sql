<?php

namespace Errors;

// 
class SqlQueryError extends \Exception {
    public function __construct(\mysqli_sql_exception $e) {
        parent::__construct('SQL STATE ('. $e->getSqlState() .'): Error occured while executing SQL query: '. $e->getMessage());
    }
}

?>