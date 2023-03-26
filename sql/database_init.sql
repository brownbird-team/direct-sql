-- Pokreni sljedeći SQL kod za kreiraje potrebnih tablica

CREATE TABLE IF NOT EXISTS cars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    description VARCHAR(255),
    price INT,
    image LONGBLOB,
    color VARCHAR(32)
);