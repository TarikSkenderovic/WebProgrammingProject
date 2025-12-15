<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthMiddleware {

    public function handle() {
        $path = Flight::request()->url;
        $method = Flight::request()->method;

        // Public routes 
        $public_routes = [
            '/login' => ['POST'],
            '/users' => ['POST'],
            '/courses' => ['GET'],
            '/reviews' => ['GET']
        ];
        foreach ($public_routes as $public_path => $allowed_methods) {
            if (strpos($path, $public_path) === 0 && in_array($method, $allowed_methods)) {
                return;
            }
        }
        
        // --- token is required ---
        try {
            $auth_header = Flight::request()->getHeader("Authentication");
            if (!$auth_header) { Flight::halt(401, "Missing authentication header"); }
            $token_parts = explode(" ", $auth_header);
            if (count($token_parts) < 2 || $token_parts[0] !== 'Bearer') { Flight::halt(401, "Invalid token format."); }
            $token = $token_parts[1];
            if(!$token) { Flight::halt(401, "Token not found in header."); }
            $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));

            
            $user_role = $decoded_token->user->role;

            // Define which routes are for admins
            $admin_only_routes = [
                '/users',         
                '/instructors'    
            ];
            

            $is_admin_route = false;
            foreach ($admin_only_routes as $admin_route) {
                if (strpos($path, $admin_route) === 0) {
                    $is_admin_route = true;
                    break;
                }
            }
            
            
            if ($is_admin_route && $user_role !== 'admin') {
                Flight::halt(403, "Forbidden: You do not have admin privileges.");
            }

            // A special check for course management (POST, PUT, DELETE)
            if (strpos($path, '/courses') === 0 && $method !== 'GET' && $user_role !== 'admin') {
                 Flight::halt(403, "Forbidden: You do not have admin privileges to manage courses.");
            }

            Flight::set('user', $decoded_token->user);

        } catch (\Exception $e) {
            Flight::halt(401, $e->getMessage());
        }
    }
}
?>