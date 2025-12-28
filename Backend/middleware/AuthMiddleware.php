<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthMiddleware {

    public function handle() {
        $path = Flight::request()->url;
        $method = Flight::request()->method;

        // Define public routes
        $public_routes = [
            '/login' => ['POST'],
            '/users' => ['POST'],    
            '/courses' => ['GET'],   
            '/reviews' => ['GET']    
        ];

        
        $is_public = false;
        foreach ($public_routes as $public_path => $allowed_methods) {
            
            if ($public_path == '/login' || $public_path == '/users') {
                if ($path == $public_path && in_array($method, $allowed_methods)) {
                    $is_public = true;
                    break;
                }
            } 
            
            else {
                if (strpos($path, $public_path) === 0 && in_array($method, $allowed_methods)) {
                    $is_public = true;
                    break;
                }
            }
        }

        
        if (!$is_public) {
            try {
                $auth_header = Flight::request()->getHeader("Authorization");
                if (!$auth_header) { Flight::halt(401, "Missing authorization header"); }
                
                $token_parts = explode(" ", $auth_header);
                if (count($token_parts) < 2 || $token_parts[0] !== 'Bearer') { Flight::halt(401, "Invalid token format."); }
                
                $token = $token_parts[1];
                if(!$token) { Flight::halt(401, "Token not found in header."); }
                
                $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

                Flight::set('user', $decoded_token->user);

                // Authorization logic
                $user_role = $decoded_token->user->role;
                

                if ($path == '/users' && $method == 'GET' && $user_role != 'admin') {
                     Flight::halt(403, "Forbidden: You must be an admin to view all users.");
                }
                
                if (strpos($path, '/instructors') === 0 && $user_role != 'admin') {
                    Flight::halt(403, "Forbidden: You must be an admin to manage instructors.");
                }
                
                if (strpos($path, '/courses') === 0 && $method != 'GET' && $user_role != 'admin') {
                     Flight::halt(403, "Forbidden: You must be an admin to manage courses.");
                }

            } catch (\Exception $e) {
                Flight::halt(401, $e->getMessage());
            }
        }
    }
}
?>