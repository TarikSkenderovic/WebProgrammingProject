<?php

require_once __DIR__.'/BaseService.php';
require_once __DIR__.'/../dao/CourseDao.php';
require_once __DIR__.'/../dao/InstructorDao.php'; 

class CourseService extends BaseService {

    
    private $instructorDao; 

    public function __construct() {
        parent::__construct(new CourseDao());
        $this->instructorDao = new InstructorDao(); 
    }

    public function add_course($course_data) {
        // Validation
        if (empty($course_data['title']) || empty($course_data['instructor_id'])) {
            throw new Exception("Title and instructor ID are required.", 400);
        }

        if (!$this->instructorDao->get_instructor_by_id($course_data['instructor_id'])) {
            throw new Exception("Instructor with the provided ID does not exist.", 404);
        }
        
        if (!is_numeric($course_data['price']) || $course_data['price'] < 0) {
            throw new Exception("Price must be a non-negative number.", 400);
        }

        if ($this->dao->get_course_by_title($course_data['title'])) {
            throw new Exception("A course with this title already exists.", 409);
        }

        return $this->dao->add_course($course_data);
    }

    public function get_all_courses() {
        return $this->dao->get_all_courses();
    }

    public function get_course_by_id($course_id) {
        $course = $this->dao->get_course_by_id($course_id);
        if (!$course) {
            throw new Exception("Course not found.", 404);
        }
        return $course;
    }

    public function update_course($course_id, $course_data) {
        $course_data['id'] = $course_id;
        $this->dao->update_course($course_data);
        return $this->get_course_by_id($course_id);
    }

    public function delete_course($course_id) {
        $this->dao->delete_course($course_id);
    }

    public function count_all_courses() {
        return $this->dao->count_all_courses();
    }
}

?>