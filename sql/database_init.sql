-- Kreiraj sve tablice potrebne za glavni PandaSQL database

CREATE TABLE IF NOT EXISTS user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(256) NOT NULL UNIQUE,
    email VARCHAR(256) NOT NULL UNIQUE,
    password_hash VARCHAR(512) NOT NULL,
    database_name VARCHAR(512),
    database_username VARCHAR(512),
    database_password VARCHAR(512),
    maintenance_mode BOOLEAN NOT NULL DEFAULT TRUE,
    admin_user BOOLEAN NOT NULL DEFAULT FALSE,
    verified BOOLEAN NOT NULL DEFAULT FALSE,
    last_login DATETIME
);

CREATE TABLE IF NOT EXISTS invite_code (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invite_code VARCHAR(256) NOT NULL UNIQUE,
    expires DATETIME,
    max_uses INT NOT NULL DEFAULT 0,
    times_used INT NOT NULL DEFAULT 0
);