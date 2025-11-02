<?php
require_once 'BaseDao.php';

class EnrollmentDao extends BaseDao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * CREATE enrollment 
     */
    public function add_enrollment($enrollment) {
        $query = "INSERT INTO enrollments (user_id, course_id, progress) 
                  VALUES (:user_id, :course_id, :progress)";
        $this->execute($query, [
            ':user_id' => $enrollment['user_id'],
            ':course_id' => $enrollment['course_id'],
            ':progress' => isset($enrollment['progress']) ? $enrollment['progress'] : 0.00
        ]);
        return $this->conn->lastInsertId();
    }

    /**
     * READ all enrollments for a specific user
     */
    public function get_enrollments_by_user_id($user_id) {
        $query = "SELECT e.*, c.title AS course_title
                  FROM enrollments e
                  JOIN courses c ON e.course_id = c.id
                  WHERE e.user_id = :user_id";
        return $this->query($query, [':user_id' => $user_id]);
    }
    
    /**
     * READ for a specific course
     */
    public function get_enrollments_by_course_id($course_id) {
        $query = "SELECT e.*, u.username, u.email
                  FROM enrollments e
                  JOIN users u ON e.user_id = u.id
                  WHERE e.course_id = :course_id";
        return $this->query($query, [':course_id' => $course_id]);
    }

    /**
     * UPDATE enrollment 
     */
    public function update_enrollment_progress($enrollment) {
        $query = "UPDATE enrollments SET 
                    progress = :progress
                  WHERE id = :id";
        $this->execute($query, [
            ':id' => $enrollment['id'],
            ':progress' => $enrollment['progress']
        ]);
    }

    /**
     * DELETE enrollment 
     */
    public function delete_enrollment($enrollment_id) {
        $this->execute("DELETE FROM enrollments WHERE id = :id", [':id' => $enrollment_id]);
    }
}
?>