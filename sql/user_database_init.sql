-- Tablice unutar baze podataka svakog korisnika koje se koriste za
-- pohranu njegovih stranica i datoteka

CREATE TABLE IF NOT EXISTS pandasql_page (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL DEFAULT '',
    public BOOLEAN NOT NULL DEFAULT TRUE,
    is_partial BOOLEAN NOT NULL DEFAULT FALSE,
    changed TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS pandasql_page_dependency (
    page_id INT,
    dependency_id INT,

    PRIMARY KEY (page_id, dependency_id),
    FOREIGN KEY (page_id) REFERENCES pandasql_page(id),
    FOREIGN KEY (dependency_id) REFERENCES pandasql_page(id)
);

CREATE TABLE IF NOT EXISTS pandasql_file (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_name VARCHAR(255) UNIQUE,
    content LONGBLOB,
    public BOOLEAN,
    text_file BOOLEAN
);

