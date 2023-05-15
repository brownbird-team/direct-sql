-- Tablice unutar baze podataka svakog korisnika koje se koriste za
-- pohranu njegovih stranica i datoteka

CREATE TABLE IF NOT EXISTS pandasql_pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_name UNIQUE,
    content TEXT,
    public BOOLEAN,
    type_require BOOLEAN
);

CREATE TABLE IF NOT EXISTS pandasql_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_name UNIQUE,
    content LONGBLOB,
    public BOOLEAN,
    text_file BOOLEAN
);

