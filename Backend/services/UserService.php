<?php

require_once __DIR__.'/BaseService.php';
require_once __DIR__.'/../dao/UserDao.php';

class UserService extends BaseService {


    public function __construct() {
        parent::__construct(new UserDao());
    }

    public function add_user($user_data) {
        // Validation: Check for empty fields
        if (empty($user_data['username']) || empty($user_data['email']) || empty($user_data['password'])) {
            throw new Exception("Username, email, and password are required.", 400);
        }

        if ($this->dao->get_user_by_username($user_data['username'])) {
            throw new Exception("Username already exists.", 409);
        }
        if ($this->dao->get_user_by_email($user_data['email'])) {
            throw new Exception("Email already exists.", 409);
        }

        $user_to_add = [
            'username' => $user_data['username'],
            'email' => $user_data['email'],
            'password_hash' => password_hash($user_data['password'], PASSWORD_BCRYPT),
            'first_name' => $user_data['first_name'] ?? null,
            'last_name' => $user_data['last_name'] ?? null
        ];

        return $this->dao->add_user($user_to_add);
    }
    
    public function get_all_users() {
        return $this->dao->get_all_users();
    }

    public function get_user_by_id($user_id) {
        return $this->dao->get_user_by_id($user_id);
    }

    public function update_user($user_id, $user_data) {
        $user_data['id'] = $user_id;
        $this->dao->update_user($user_data);
        return $this->get_user_by_id($user_id);
    }

    public function delete_user($user_id) {
        $this->dao->delete_user($user_id);
    }
}
?>