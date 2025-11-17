<?php

require_once __DIR__.'/BaseService.php';
require_once __DIR__.'/../dao/ReviewDao.php';
require_once __DIR__.'/../dao/UserDao.php';
require_once __DIR__.'/../dao/CourseDao.php';


class ReviewService extends BaseService {

    private $userDao;
    private $courseDao;

    public function __construct() {
        parent::__construct(new ReviewDao());
        $this->userDao = new UserDao();
        $this->courseDao = new CourseDao();
    }

    public function add_review($review_data) {
        // Validation for required fields
        if (empty($review_data['user_id']) || empty($review_data['course_id']) || empty($review_data['rating'])) {
            throw new Exception("User ID, Course ID, and Rating are required.", 400);
        }
        
        if (!$this->userDao->get_user_by_id($review_data['user_id'])) {
            throw new Exception("User not found.", 404);
        }
        if (!$this->courseDao->get_course_by_id($review_data['course_id'])) {
            throw new Exception("Course not found.", 404);
        }

        // Validation for rating value
        if (!is_numeric($review_data['rating']) || $review_data['rating'] < 1 || $review_data['rating'] > 5) {
            throw new Exception("Rating must be an integer between 1 and 5.", 400);
        }

        // Use the primary DAO
        return $this->dao->add_review($review_data);
    }

    public function get_reviews_by_course_id($course_id) {
        return $this->dao->get_reviews_by_course_id($course_id);
    }

    public function update_review($review_id, $review_data) {
        // Validation for the update
        if (empty($review_data['rating']) && empty($review_data['comment'])) {
            throw new Exception("Either rating or comment must be provided for an update.", 400);
        }
        if (isset($review_data['rating']) && (!is_numeric($review_data['rating']) || $review_data['rating'] < 1 || $review_data['rating'] > 5)) {
            throw new Exception("Rating must be an integer between 1 and 5.", 400);
        }

        $review_data['id'] = $review_id;
        $this->dao->update_review($review_data);
        
        return $this->dao->get_review_by_id($review_id);
    }

    public function delete_review($review_id) {
        $review = $this->dao->get_review_by_id($review_id);
        if (!$review) {
            throw new Exception("Review not found.", 404);
        }
        
        $this->dao->delete_review($review_id);
    }
}
?>