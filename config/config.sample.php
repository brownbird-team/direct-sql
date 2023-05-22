<?php

$config = [
    // Database settings
    'database_host'                 => 'localhost',
    'database_name'                 => 'myDatabaseName',
    'database_admin_username'       => 'myDatabaseUsername',
    'database_admin_password'       => 'myDatabasePassword',
    'database_user_prefix'          => 'pandasql_user_',
    'database_user_hostname'        => 'localhost',
    'database_user_password_length' => 32,

    // User environment settings
    'max_execution_time'            => 10,
    'memory_limit'                  => '64M',
    'sql_query_limit'               => 20,

    // Email server settings
    'smtp_hostname'                 => 'mail.example.com',
    'smtp_port'                     => 587,
    'smtp_username'                 => 'pandasql@example.com',
    'smtp_password'                 => 'mySmtpPassword',

    // Email other options
    'email_from'                    => 'PandaSQL Instance',

    // Gravatar
    'use_gravatar'                  => true,
    'default_avatar'                => 'robohash',
];

?>