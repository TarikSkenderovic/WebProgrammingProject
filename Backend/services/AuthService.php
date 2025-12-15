<?php


require_once __DIR__.'/BaseService.php';
require_once __DIR__.'/../dao/UserDao.php';


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


class AuthService extends BaseService {


    public function __construct() {
        parent::__construct(new UserDao());
    }


    public function login_user($user_data) {
        $user = $this->dao->get_user_by_email($user_data['email']);

        if ($user && password_verify($user_data['password'], $user['password_hash'])) {
            $jwt_payload = [
                'user' => $user, 
                'iat' => time(), 
                'exp' => time() + (60 * 60 * 24) 
            ];
            
 
            $token = JWT::encode(
                $jwt_payload,
                Config::JWT_SECRET(),
                'HS256'
            );
            
            // Return the generated token
            return ['token' => $token];
        } else {
            throw new Exception("Invalid email or password", 401); 
        }
    }
}
?>