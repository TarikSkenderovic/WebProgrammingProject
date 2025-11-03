<?php
require_once 'BaseDao.php';

class InstructorDao extends BaseDao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * CREATE instructor profile
     */
    public function add_instructor($instructor) {
        $query = "INSERT INTO instructors (user_id, bio, expertise) 
                  VALUES (:user_id, :bio, :expertise)";
        $this->execute($query, [
            ':user_id' => $instructor['user_id'],
            ':bio' => $instructor['bio'],
            ':expertise' => $instructor['expertise']
        ]);
        return $this->conn->lastInsertId();
    }

    /**
     * READ all instructors.
     */
    public function get_all_instructors() {
        $query = "SELECT i.*, u.first_name, u.last_name, u.email 
                  FROM instructors i
                  JOIN users u ON i.user_id = u.id";
        return $this->query($query, []);
    }

    /**
     * READ instructor by their user_id
     */
    public function get_instructor_by_user_id($user_id) {
        $query = "SELECT i.*, u.first_name, u.last_name, u.email
                  FROM instructors i
                  JOIN users u ON i.user_id = u.id
                  WHERE i.user_id = :user_id";
        return $this->query_unique($query, [':user_id' => $user_id]);
    }

    /**
     * UPDATE instructor profile
     */
    public function update_instructor($instructor) {
        $query = "UPDATE instructors SET 
                    bio = :bio, 
                    expertise = :expertise
                  WHERE id = :id";
        $this->execute($query, [
            ':id' => $instructor['id'],
            ':bio' => $instructor['bio'],
            ':expertise' => $instructor['expertise']
        ]);
    }

    /**
     * DELETE instructor profile
     */
    public function delete_instructor($instructor_id) {
        $this->execute("DELETE FROM instructors WHERE id = :id", [':id' => $instructor_id]);
    }
}
?>