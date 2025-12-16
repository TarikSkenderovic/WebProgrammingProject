<?php

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authentication");
    exit(0);
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authentication");



require 'vendor/autoload.php';


// Configuration and Middleware
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// DAOs 
require_once __DIR__ . '/dao/BaseDao.php'; 
require_once __DIR__ . '/dao/UserDao.php';
require_once __DIR__ . '/dao/CourseDao.php';
require_once __DIR__ . '/dao/InstructorDao.php';
require_once __DIR__ . '/dao/EnrollmentDao.php';
require_once __DIR__ . '/dao/ReviewDao.php';

// Services 
require_once __DIR__ . '/services/BaseService.php'; 
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CourseService.php';
require_once __DIR__ . '/services/InstructorService.php';
require_once __DIR__ . '/services/EnrollmentService.php';
require_once __DIR__ . '/services/ReviewService.php';


// --- REGISTER SERVICES & MIDDLEWARE WITH FLIGHT ---
Flight::register('authService', 'AuthService');
Flight::register('userService', 'UserService');
Flight::register('courseService', 'CourseService');
Flight::register('instructorService', 'InstructorService');
Flight::register('enrollmentService', 'EnrollmentService');
Flight::register('reviewService', 'ReviewService');
Flight::register('authMiddleware', 'AuthMiddleware');


// --- MIDDLEWARE HOOK ---
Flight::before('start', [Flight::authMiddleware(), 'handle']);


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


Flight::start();
?>