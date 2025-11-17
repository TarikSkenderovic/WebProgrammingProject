<?php
require_once 'BaseDao.php';

class CourseDao extends BaseDao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * CREATE course
     */
    public function add_course($course) {
        $query = "INSERT INTO courses (title, description, instructor_id, price, difficulty_level) 
                  VALUES (:title, :description, :instructor_id, :price, :difficulty_level)";
        $this->execute($query, [
            ':title' => $course['title'],
            ':description' => $course['description'],
            ':instructor_id' => $course['instructor_id'],
            ':price' => $course['price'],
            ':difficulty_level' => $course['difficulty_level']
        ]);
        return $this->conn->lastInsertId();
    }

    /**
     * READ all courses
     */
    public function get_all_courses() {
        return $this->query("SELECT * FROM courses", []);
    }

    /**
     * READ course by ID
     */
    public function get_course_by_id($course_id) {
        return $this->query_unique("SELECT * FROM courses WHERE id = :id", [':id' => $course_id]);
    }

    /**
     * READ course by title - for validation
     */
    public function get_course_by_title($title) {
        return $this->query_unique("SELECT * FROM courses WHERE title = :title", [':title' => $title]);
    }

    /**
     * UPDATE course
     */
    public function update_course($course) {
        $query = "UPDATE courses SET 
                    title = :title, 
                    description = :description, 
                    price = :price, 
                    difficulty_level = :difficulty_level
                  WHERE id = :id";
        $this->execute($query, [
            ':id' => $course['id'],
            ':title' => $course['title'],
            ':description' => $course['description'],
            ':price' => $course['price'],
            ':difficulty_level' => $course['difficulty_level']
        ]);
    }

    /**
     * DELETE course
     */
    public function delete_course($course_id) {
        $this->execute("DELETE FROM courses WHERE id = :id", [':id' => $course_id]);
    }
}
?>