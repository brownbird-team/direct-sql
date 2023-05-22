<?php

namespace Database;

class DB {
    public $con;

    public function __construct($hostname, $username, $password, $database, $port = null) {
        try {
            $this->con = new \mysqli($hostname, $username, $password, $database, $port);
        } catch (\mysqli_sql_exception $e) {
            throw new \Errors\InternalServerError('Error connecting to database "'. $database .'": '. $e->getMessage());
        }
    }

    public function run($sql, $args = null, $line = null) {
        try {
            if (!$args)
                return $this->con->query($sql);

            $stmt = $this->con->prepare($sql);
            $stmt->execute($args);
            return $stmt->get_result();
        } catch (\mysqli_sql_exception $e) {
            throw new \Errors\SqlQueryError($e, $line);
        }
    }

    public function query($sql, $line = null) {
        try {
            return $this->con->query($sql);
        } catch (\mysqli_sql_exception $e) {
            throw new \Errors\SqlQueryError($e, $line);
        }
    }

    public function escape($str) {
        return $this->con->real_escape_string($str);
    }
}


?>