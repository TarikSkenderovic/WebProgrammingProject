<?php

// Allow cross-origin requests (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


require 'vendor/autoload.php';


require_once __DIR__ . '/dao/UserDao.php';
require_once __DIR__ . '/dao/CourseDao.php';
require_once __DIR__ . '/dao/InstructorDao.php';
require_once __DIR__ . '/dao/EnrollmentDao.php';
require_once __DIR__ . '/dao/ReviewDao.php';


require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CourseService.php';
require_once __DIR__ . '/services/InstructorService.php';
require_once __DIR__ . '/services/EnrollmentService.php';
require_once __DIR__ . '/services/ReviewService.php';


Flight::register('userService', 'UserService');
Flight::register('courseService', 'CourseService');
Flight::register('instructorService', 'InstructorService');
Flight::register('enrollmentService', 'EnrollmentService');
Flight::register('reviewService', 'ReviewService');

// --- Include all route files ---
require_once __DIR__ . '/routes/user_routes.php';
require_once __DIR__ . '/routes/course_routes.php';
require_once __DIR__ . '/routes/instructor_routes.php';
require_once __DIR__ . '/routes/enrollment_routes.php';
require_once __DIR__ . '/routes/review_routes.php';


Flight::start();
?>