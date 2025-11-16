<?php

require_once __DIR__.'/BaseService.php';
require_once __DIR__.'/../dao/InstructorDao.php';
require_once __DIR__.'/../dao/UserDao.php'; 


class InstructorService extends BaseService {

    private $userDao;
    public function __construct() {
        parent::__construct(new InstructorDao());
        $this->userDao = new UserDao();
    }

    public function add_instructor($instructor_data) {

        if (empty($instructor_data['user_id'])) {
            throw new Exception("User ID is required to create an instructor profile.", 400);
        }
        $user = $this->userDao->get_user_by_id($instructor_data['user_id']);
        if (!$user) {
            throw new Exception("A user with this ID does not exist.", 404);
        }

        // Use the primary Dao to add instructor
        return $this->dao->add_instructor($instructor_data);
    }

    public function get_all_instructors() {
        return $this->dao->get_all_instructors();
    }
    
    public function get_instructor_by_user_id($user_id) {
        return $this->dao->get_instructor_by_user_id($user_id);
    }

    public function update_instructor($instructor_id, $instructor_data) {
        $existing_instructor = $this->dao->get_instructor_by_id($instructor_id); 
        if (!$existing_instructor) {
            throw new Exception("Instructor profile not found.", 404);
        }

        $instructor_data['id'] = $instructor_id;
        $this->dao->update_instructor($instructor_data);
        
        // Return the updated profile
        return $this->dao->get_instructor_by_user_id($existing_instructor['user_id']);
    }

    public function delete_instructor($instructor_id) {
        $this->dao->delete_instructor($instructor_id);
    }
}
?>