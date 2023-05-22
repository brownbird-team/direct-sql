<?php

namespace Database;
use \Database\DB;
use \Helpers;

class User {

    // status codes
    const STATUS_OK                      = 0;
    const STATUS_NOT_FOUND               = 1;
    const STATUS_USERNAME_OR_EMAIL_TAKEN = 2;
    const STATUS_ACCESS_DENIED           = 3;
    const STATUS_INVALID_EMAIL           = 4;

    // User information
    private $user_data;
    // Global configuration
    private $config;

    public function __construct(DB $db) {
        $this->db = $db;

        require __DIR__ . '/../../config/config.php';
        $this->config = $config;
    }

    public function defined() {
        return !!($this->user_data);
    }

    // Functions which can select user

    public function get_user_by_id(int $id) {
        $this->user_data = $this->db->run('SELECT * FROM users WHERE id = ?', [ $id ])->fetch_assoc();
        return ($this->user_data) ? self::STATUS_OK : self::STATUS_NOT_FOUND;
    }

    public function get_user_by_name(string $username) {
        $this->user_data = $this->db->run('SELECT * FROM users WHERE username = ?', [ $username ])->fetch_assoc();
        return ($this->user_data) ? self::STATUS_OK : self::STATUS_NOT_FOUND;
    }

    public function get_user_by_email(string $email) {
        $this->user_data = $this->db->run('SELECT * FROM users WHERE email = ?', [ $email ])->fetch_assoc();
        return ($this->user_data) ? self::STATUS_OK : self::STATUS_NOT_FOUND;
    }

    public function authenticate(string $username, string $password) {
        $user = $this->db->run('SELECT * FROM users WHERE username = ?', [ $username ])->fetch_assoc();

        if (!$user) return self::STATUS_ACCESS_DENIED;

        if (password_verify($password, $user['password_hash'])) {
            $this->user_data = $user;
            return self::STATUS_OK;
        }

        return self::STATUS_ACCESS_DENIED;
    }

    public function create($username, $email, $password) {
        $res = $this->db->run('SELECT COUNT(*) AS user_exists FROM users WHERE username = ? OR email = ?', [ $username, $email ]);

        if ($res->fetch_assoc()['user_exists']) {
            return self::STATUS_USERNAME_OR_EMAIL_TAKEN;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return self::STATUS_INVALID_EMAIL;

        $this->db->run('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)', [ $username, $email, $password ]);
        $this->get_user_by_name($username);

        return self::STATUS_OK;
    }

    // Functions which can be used once user is selected

    public function is_verified() {
        return !!$this->user_data['verified'];
    }

    public function finish_creation() {
        // Ako user nije definiran ili je već verificiran izađi
        if (!$this->defined() || $this->user_data['verified'])
            return;

        $user_hostname = $this->config['database_user_hostname'];

        // Spoji prefix i username za ime baze
        $this->user_data['database_username'] = $username = $this->config['database_user_prefix'] . $this->user_data['username'];
        // Generiraj random password
        $this->user_data['database_password'] = $password = Helpers::random_string($this->config['database_user_password_length']);

        // Kreiraj bazu za korisnika
        $this->db->run('CREATE DATABASE IF NOT EXISTS `'. $this->user_data['database_username'] . '`');
        // Kreiraj MySQL usera za korisnika
        $this->db->run("CREATE USER IF NOT EXISTS '$username'@'$user_hostname' IDENTIFIED BY '$password'");
        // Daj mu sve ovlasti nad bazom
        $this->db->run("GRANT ALL PRIVILEGES ON `$username`.* TO '$username'@'$user_hostname'");
        // Upiši novi username i password za pristup bazi korisnika u glavnu bazu
        $this->db->run(
            'UPDATE users SET verified = TRUE, database_name = ?, database_username = ?, database_password = ? WHERE id = ?', [ $username, $username, $password, $this->user_data['id'] ]
        );

        $this->user_data['verified'] = true;
    }

    public function update_password(string $new_password) {
        $this->db->run('UPDATE user SET password_hash = ? WHERE id = ?', [
            password_hash($new_password), $this->user_data['id']
        ]);
    }

    public function set_maintenance(bool $state) {
        $this->db->run(
            'UPDATE users SET maintenance_mode = ? WHERE id = ?', [ (int)$state, $this->user_data['id'] ]
        );
        $this->user_data['maintenance_mode'] = (int)$state;
    }

    public function get_maintenance() {
        return $this->user_data['maintenance_mode'];
    }

    public function get_avatar_url(int $size) {
        $email_hash = md5(strtolower(trim($this->user_data['email'])));
        return 'https://www.gravatar.com/avatar/'. $email_hash .'?d='. $this->config['default_avatar'] .'&s='. $size;
    }

    public function get_database_connection() {
        return new DB(
            $this->config['database_host'],
            $this->user_data['database_username'],
            $this->user_data['database_password'],
            $this->user_data['database_name']
        );
    }

    public function is_admin() {
        return $this->user_data['admin_user'];
    }

    public function set_admin(bool $admin) {
        $this->db->run(
            'UPDATE users SET admin_user = ? WHERE id = ?', [ (int)$admin, $this->user_data['id'] ]
        );
        $this->user_data['admin_user'] = (int)$admin;
    }

    public function delete() {

        if (!$this->defined())
            return;

        $this->db->run("DELETE FROM users WHERE id = ?", [ $this->user_data['id'] ]);

        if (!$this->user_data['verified'])
            return;

        $database = $this->user_data['database_name'];
        $username = $this->user_data['database_username'];
        $user_hostname = $this->config['database_user_hostname'];

        // Pobriši njegovu bazu i MySQL korisnika
        $this->db->run("REVOKE ALL PRIVILEGES ON `$database`.* FROM '$username'@'$user_hostname'");
        $this->db->run("DROP DATABASE $database");
        $this->db->run("DROP USER '$username'@'$user_hostname'");
    }

    public function refresh_last_login() {
        $new_date = date('Y-m-d H:i:s', time());
        $this->db->run('UPDATE users SET last_login = ? WHERE id = ?', [ date('Y-m-d H:i:s', time()), $this->user_data['id'] ]);
        $this->user_data['last_login'] = $new_date;
    }
    public function seconds_since_last_login() {
        if ($this->user_data['last_login'] === null)
            return null;

        return time() - strtotime($this->user_data['last_login']);
    }

    // Jednostavne get funkcije
    public function get_username() {
        return $this->user_data['username'];
    }
    public function get_email() {
        return $this->user_data['email'];
    }
}

?>