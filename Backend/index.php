<?php

// --- ROBUST CORS HANDLING ---
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    // THIS IS THE FIX: It must be 'Authorization'
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    exit(0);
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require 'vendor/autoload.php';

// --- INCLUDE ALL NECESSARY FILES ---
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/dao/BaseDao.php';
require_once __DIR__ . '/services/BaseService.php';
require_once __DIR__ . '/dao/UserDao.php';
require_once __DIR__ . '/dao/CourseDao.php';
require_once __DIR__ . '/dao/InstructorDao.php';
require_once __DIR__ . '/dao/EnrollmentDao.php';
require_once __DIR__ . '/dao/ReviewDao.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CourseService.php';
require_once __DIR__ . '/services/InstructorService.php';
require_once __DIR__ . '/services/EnrollmentService.php';
require_once __DIR__ . '/services/ReviewService.php';

// --- REGISTER SERVICES WITH FLIGHT ---
Flight::register('authService', 'AuthService');
Flight::register('userService', 'UserService');
Flight::register('courseService', 'CourseService');
Flight::register('instructorService', 'InstructorService');
Flight::register('enrollmentService', 'EnrollmentService');
Flight::register('reviewService', 'ReviewService');

// --- MIDDLEWARE HOOK ---
// THIS IS THE CORRECTED WAY TO REGISTER A CLASS-BASED MIDDLEWARE
// 1. Create an instance of our middleware class.
$authMiddleware = new AuthMiddleware();
// 2. Tell Flight to call the 'handle' method on that object before starting.
Flight::before('start', [$authMiddleware, 'handle']);


// --- INCLUDE ALL ROUTE FILES ---
require_once __DIR__ . '/routes/auth_routes.php';
require_once __DIR__ . '/routes/user_routes.php';
require_once __DIR__ . '/routes/course_routes.php';
require_once __DIR__ . '/routes/instructor_routes.php';
require_once __DIR__ . '/routes/enrollment_routes.php';
require_once __DIR__ . '/routes/review_routes.php';

// --- CUSTOM 404 MAPPING ---
Flight::map('notFound', function(){
    Flight::json(['message' => 'Route not found'], 404);
});

// --- START THE FRAMEWORK ---
Flight::start();
?>