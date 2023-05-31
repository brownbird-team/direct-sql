<?php

namespace Database;

use \Database\User;
use \Compiler\Tokenizer;
use \Compiler\Parser;
use \Compiler\Compiler;
use \StatusCode;
use \Errors\InternalException;

const USER_CACHE_DIR_PERMISSIONS = 660;

// Lista tipova svih polja iz page tablice
const PANDASQL_PAGE_FIELDS = [
    'id'         => 'integer',  // INT
    'name'       => 'string',   // VARCHAR(255)
    'content'    => 'string',   // TEXT
    'public'     => 'integer',  // BOOLEAN
    'is_partial' => 'integer',  // BOOLEAN
    'changed'    => 'integer',  // TIMESTAMP
];

// Lista tipova svih polja iz page dependency tablice
const PANDASQL_PAGE_DEPENDENCY_FIELDS = [
    'page_id'       => 'integer',  // INT
    'dependency_id' => 'integer',  // INT
];

class Page {
    private $db;
    private $page_data;
    private $require_callback;
    private $config;
    private $username;

    public function __construct(User $user) {
        // Dobavi connection na database user-a
        $this->db = $user->get_database_connection();
        // Dobavi username usera
        $this->username = $user->get_username();

        $this->require_callback = function ($name) {
            $this->find_required_page($name);
        };

        require __DIR__ . '/../../config/config.php';
        $this->config = $config;
    }

    // Provjeri array koji je stigao kao record iz baze ima li sve
    // keyeve kao i prototipni array i jesu li mu sve vrijednosti
    // odgovarajućeg tipa
    private function check_record($record, $record_fields_type) {
        var_dump($record);
        foreach ($record_fields_type as $key => $type) {
            if (!isset($record[$key]) || gettype($record[$key]) !== $type)
                throw new InternalException(StatusCode::INVALID_USER_DATABASE);
        }
    }

    // Dobavi id, name i tokene za traženi page, ako page nije partial ili
    // ne postoji vrati null
    private function find_required_page($name) {
        $result = $this->db->run('SELECT * FROM pandasql_page WHERE name = ?', [ $name ])->fetch_assoc();
        
        if ($result->num_rows === 0)
            return null;

        $page_data = $result->fetch_assoc();
        $this->check_record($page_data, PANDASQL_PAGE_FIELDS);

        if (!$page_data['is_partial'])
            return null;

        $tokenizer = new Tokenizer($name);
        return [
            'id' => $page_data['id'],
            'name' => $page_data['name'],
            'tokens' => $tokenizer->tokenize($page_data['content']),
        ];
    }

    // Dobavi podatke za page prema id-u, i provjeri je li record OK
    public function get_page_by_id($id) {
        $this->page_data = $this->db->run('SELECT * FROM pandasql_page WHERE id = ?', [ $id ])->fetch_assoc();
        $this->page_data || throw new InternalException(StatusCode::PAGE_NOT_FOUND);
        $this->check_record($this->page_data, PANDASQL_PAGE_FIELDS);
    }

    // Dobavi podatke za page prema imenu i provjeri je li record OK
    public function get_page_by_name($name) {
        $this->page_data = $this->db->run('SELECT * FROM pandasql_page WHERE name = ?', [ $name ])->fetch_assoc();
        $this->page_data || throw new InternalException(StatusCode::PAGE_NOT_FOUND);
        $this->check_record($this->page_data, PANDASQL_PAGE_FIELDS);
    }

    // Kreiraj novi page
    public function create($name) {
        // Provjeri postoji li page
        $this->page_data = $this->db->run('SELECT * FROM pandasql_page WHERE name = ?', [ $name ])->fetch_assoc();
        // Nemoguće kreirati page ako već postoji
        $this->page_data && throw new InternalException(StatusCode::PAGE_EXISTS);
        // Ubaci page
        $this->db->run('INSERT INTO pandasql_page (name, changed) VALUES (?, ?)', [ $name, date("Y-m-d H:i:s") ]);
        // Dobavi podatke za novokreirani page
        $this->page_data = $this->db->run('SELECT * FROM pandasql_page WHERE name = ?', [ $name ])->fetch_assoc();
    }

    // Postavi page kao public ili private
    public function set_public($public) {
        $this->db->run('UPDATE pandasql_page SET public = ? WHERE id = ?', [ $public, $this->page_data['id'] ]);
        $this->page_data['public'] = (int)$public;
    }

    // Provjeri je li page public
    public function is_public() {
        return $this->page_data['public'];
    }

    // Postavi je li page partial
    public function set_partial($partial) {
        $this->db->run('UPDATE pandasql_page SET is_partial = ? WHERE id = ?', [ $partial, $this->page_data['id'] ]);
        $this->page_data['is_partial'] = (int)$partial;
    }

    // Provjeri je li page partial
    public function is_partial() {
        return $this->page_data['is_partial'];
    }

    // Postavi PandaSQL kod za page
    public function set_content($content) {
        return $this->run('UPDATE pandasql_page SET content = ? WHERE id = ?', [ $content, $this->page_data['id'] ]);
        $this->page_data['content'] = (string)$content;

        $this->run('
            UPDATE pandasql_page SET changed = ? WHERE id = ? OR id IN (
                SELECT page_id FROM pandasql_page_dependency WHERE dependency_id = ?
            )
        ', [ time(), $this->page_data['id'], $this->page_data['id'] ]);
    }

    // Kompajliraj sadržaj stranice ako je potrebno i kreiraj file
    // sa php kodom stranice u cache direktoriju
    public function compiled_file_path() {
        // Napravi tokene za page
        $tokenizer = new Tokenizer($this->page_data['name']);
        $tokens = $tokenizer->tokenize($this->page_data['content']);
        // Napravi abstraktno sintaksno drvo za page
        $parser = new Parser($this->require_callback);
        $ast = $parser->parse($tokens);
        // Kompajliraj drvo u PHP kod
        $compiler = new Compiler();
        $php_code = $compiler->compile($ast);

        // Obriši svaki prijašnji dependency za ovaj page
        $this->db->run('DELETE FROM pandasql_page_dependency WHERE page_id = ?', [ $this->page_data['id'] ]);
        // Ubaci nove dependency-je u tablicu
        try {
            $stmt = $this->db->con->prepare('INSERT INTO pandasql_page_dependency VALUES (?, ?)');
            
            foreach ($parser->get_dependencies() as $dep_id) {
                $stmt->execute([ $this->page_data['id'], $dep_id ]);
            }
            $stmt->close();
        } catch (\mysqli_sql_exception $e) {
            throw new SqlQueryError($e);
        }

        // Provjeri postoji li cache dir
        if (!is_dir($this->config['compiled_cache_dir']))
            throw new InternalException(StatusCode::CACHE_DIR_NOT_FOUND);

        // Provjeri postoji li cache dir za korisnika ako ne kreiraj ga
        $user_dir = $this->config['compiled_cache_dir'] . '/' . $this->username;
        if (!is_dir($user_dir))
            mkdir($user_dir, USER_CACHE_DIR_PERMISSIONS);

        // Ubaci PHP kod u cache file i vrati path
        $page_cache_file = $user_dir . $this->page_data['id'] . '.php';
        file_put_contents($page_cache_file, $php_code);
        return $page_cache_file;
    }
}