<?php
require_once 'BaseDao.php';

class UserDao extends BaseDao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * CREATE user
     */
    public function add_user($user) {
        $query = "INSERT INTO users (username, email, password_hash, first_name, last_name) 
                  VALUES (:username, :email, :password_hash, :first_name, :last_name)";
        $this->execute($query, [
            ':username' => $user['username'],
            ':email' => $user['email'],
            ':password_hash' => $user['password_hash'],
            ':first_name' => $user['first_name'],
            ':last_name' => $user['last_name']
        ]);
        return $this->conn->lastInsertId();
    }

    /**
     * READ all users
     */
    public function get_all_users() {
        return $this->query("SELECT * FROM users", []);
    }

    /**
     * READ user by ID
     */
    public function get_user_by_id($user_id) {
        return $this->query_unique("SELECT * FROM users WHERE id = :id", [':id' => $user_id]);
    }


      /**
     * READ user by email 
     */
    public function get_user_by_email($email) {
        return $this->query_unique("SELECT * FROM users WHERE email = :email", [':email' => $email]);
    }

    /**
     * READ user by username 
     */
    public function get_user_by_username($username) {
        return $this->query_unique("SELECT * FROM users WHERE username = :username", [':username' => $username]);
    }

    public function count_all_users() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM users");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * UPDATE user
     */
    public function update_user($user) {
        $query = "UPDATE users SET 
                    username = :username, 
                    email = :email, 
                    first_name = :first_name, 
                    last_name = :last_name,
                    role = :role 
                  WHERE id = :id";
                  
        $this->execute($query, [
            ':id' => $user['id'],
            ':username' => $user['username'],
            ':email' => $user['email'],
            ':first_name' => $user['first_name'],
            ':last_name' => $user['last_name'],
            ':role' => $user['role']
        ]);
    }

    /**
     * DELETE user
     */
    public function delete_user($user_id) {
        $this->execute("DELETE FROM users WHERE id = :id", [':id' => $user_id]);
    }
}
?>

