<?php
require_once 'BaseDao.php';

class ReviewDao extends BaseDao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * CREATE a review for a course
     */
    public function add_review($review) {
        $query = "INSERT INTO reviews (user_id, course_id, rating, comment) 
                  VALUES (:user_id, :course_id, :rating, :comment)";
        $this->execute($query, [
            ':user_id' => $review['user_id'],
            ':course_id' => $review['course_id'],
            ':rating' => $review['rating'],
            ':comment' => $review['comment']
        ]);
        return $this->conn->lastInsertId();
    }

    /**
     * READ all reviews for a specific course
     */
    public function get_reviews_by_course_id($course_id) {
        $query = "SELECT r.*, u.username 
                  FROM reviews r
                  JOIN users u ON r.user_id = u.id
                  WHERE r.course_id = :course_id
                  ORDER BY r.review_date DESC";
        return $this->query($query, [':course_id' => $course_id]);
    }

    /**
     * READ a single review by its ID
     */
    public function get_review_by_id($review_id) {
        return $this->query_unique("SELECT * FROM reviews WHERE id = :id", [':id' => $review_id]);
    }

    /**
     * UPDATE a review
     */
    public function update_review($review) {
        $query = "UPDATE reviews SET 
                    rating = :rating, 
                    comment = :comment
                  WHERE id = :id";
        $this->execute($query, [
            ':id' => $review['id'],
            ':rating' => $review['rating'],
            ':comment' => $review['comment']
        ]);
    }

    /**
     * DELETE a review
     */
    public function delete_review($review_id) {
        $this->execute("DELETE FROM reviews WHERE id = :id", [':id' => $review_id]);
    }
}
?>