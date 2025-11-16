<?php

// Allow cross-origin requests (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Main API Entry Point
// This file is located in the /backend/ directory

// Use 'require' which will cause a fatal error if the file is not found.
// This path is relative to the current file, looking for a 'vendor' folder in the same directory.
require 'vendor/autoload.php';

// --- Include DAO classes ---
// __DIR__ is a magic constant that gives the directory of the current file (i.e., /backend).
require_once __DIR__ . '/dao/UserDao.php';
require_once __DIR__ . '/dao/CourseDao.php';
require_once __DIR__ . '/dao/InstructorDao.php';
require_once __DIR__ . '/dao/EnrollmentDao.php';
require_once __DIR__ . '/dao/ReviewDao.php';

// --- Include SERVICE classes ---
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CourseService.php';
require_once __DIR__ . '/services/InstructorService.php';
require_once __DIR__ . '/services/EnrollmentService.php';
require_once __DIR__ . '/services/ReviewService.php';

// --- Register SERVICE classes with Flight so routes can use them ---
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

// Start the Flight framework to listen for requests
Flight::start();
?>