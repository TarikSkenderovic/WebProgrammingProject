<?php

require_once __DIR__.'/BaseService.php';
require_once __DIR__.'/../dao/EnrollmentDao.php';
require_once __DIR__.'/../dao/UserDao.php';
require_once __DIR__.'/../dao/CourseDao.php';

class EnrollmentService extends BaseService {

    private $userDao;
    private $courseDao;

    public function __construct() {
        parent::__construct(new EnrollmentDao());
        $this->userDao = new UserDao();
        $this->courseDao = new CourseDao();
    }

    public function add_enrollment($enrollment_data) {
        if (empty($enrollment_data['user_id']) || empty($enrollment_data['course_id'])) {
            throw new Exception("User ID and Course ID are required.", 400);
        }
        if (!$this->userDao->get_user_by_id($enrollment_data['user_id'])) {
            throw new Exception("User not found.", 404);
        }
        if (!$this->courseDao->get_course_by_id($enrollment_data['course_id'])) {
            throw new Exception("Course not found.", 404);
        }

        // Check if the user is already enrolled in this course
        $existing_enrollment = $this->dao->get_enrollment_by_user_and_course($enrollment_data['user_id'], $enrollment_data['course_id']);
        if ($existing_enrollment) {
            throw new Exception("User is already enrolled in this course.", 409); 
        }

        return $this->dao->add_enrollment($enrollment_data);
    }

    public function get_enrollments_by_user_id($user_id) {
        return $this->dao->get_enrollments_by_user_id($user_id);
    }

    public function get_enrollments_by_course_id($course_id) {
        return $this->dao->get_enrollments_by_course_id($course_id);
    }

    public function delete_enrollment($enrollment_id) {
        $enrollment = $this->dao->get_enrollment_by_id($enrollment_id);
        if (!$enrollment) {
            throw new Exception("Enrollment record not found.", 404);
        }
        $this->dao->delete_enrollment($enrollment_id);
    }

    public function count_all_enrollments() {
        return $this->dao->count_all_enrollments();
    }
    public function get_enrollment_by_user_and_course($user_id, $course_id) {
        return $this->dao->get_enrollment_by_user_and_course($user_id, $course_id);
    }
    public function get_all_enrollments() {
        return $this->dao->get_all_enrollments();
    }

}
?>