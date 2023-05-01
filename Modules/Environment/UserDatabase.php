<?php

namespace Environment;
use \Errors\InternalServerError;
use \Errors\SqlQueryError;

require_once __DIR__ . '/../../config/config.php';

class UserDatabase {
    private $connection;
    private $username;
    private $db_password;

    public function __construct($user, $pass) {
        $this->username = $user;
        $this->db_password = $pass;
    }

    public function connect() {
        global $config;

        try {
            $this->connection = new \mysqli(
                $config['database_host'], 
                $config['database_user_prefix'] . $this->username,
                $this->db_password,
                $config['database_user_prefix'] . $this->username
            );
        } catch (\mysqli_sql_exception $e) {
            throw new InternalServerError('Error while connecting to user database for user "'. $this->username .'"');
        }
    }

    public function query($query_string) {
        try {
            $result = $this->connection->query($query_string);
        } catch (\mysqli_sql_exception $e) {
            throw new SqlQueryError($e);
        }

        return $result;
    }
}

?>