-- Kreiraj sve tablice potrebne za glavni PandaSQL database

CREATE TABLE IF NOT EXISTS users (
    username VARCHAR(256) PRIMARY KEY,
    email VARCHAR(256) NOT NULL UNIQUE,
    password_hash VARCHAR(512) NOT NULL,
    database_name VARCHAR(512) NOT NULL,
    database_user VARCHAR(512) NOT NULL,
    database_password VARCHAR(512) NOT NULL,
    maintenance_mode BOOLEAN NOT NULL DEFAULT TRUE,
    admin_user BOOLEAN NOT NULL DEFAULT FALSE,
);

CREATE TABLE IF NOT EXISTS invite_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invite_code VARCHAR(256) NOT NULL UNIQUE,
    expires DATETIME,
    max_uses INT NOT NULL DEFAULT 0,
    times_used INT NOT NULL DEFAULT 0
);