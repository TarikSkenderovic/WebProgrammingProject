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
        $query = "INSERT INTO courses (title, description, instructor_id, price, difficulty_level, image_url) 
                  VALUES (:title, :description, :instructor_id, :price, :difficulty_level, :image_url)";
        $this->execute($query, [
            ':title' => $course['title'],
            ':description' => $course['description'],
            ':instructor_id' => $course['instructor_id'],
            ':price' => $course['price'],
            ':difficulty_level' => $course['difficulty_level'],
            ':image_url' => $course['image_url'] ?? null 
        ]);
        return $this->conn->lastInsertId();
    }


    public function get_all_courses() {
        $query = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) AS instructor_name 
                  FROM courses c
                  LEFT JOIN instructors i ON c.instructor_id = i.id
                  LEFT JOIN users u ON i.user_id = u.id";
        return $this->query($query, []);
    }


    public function get_course_by_id($course_id) {
        $query = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) AS instructor_name 
                  FROM courses c
                  LEFT JOIN instructors i ON c.instructor_id = i.id
                  LEFT JOIN users u ON i.user_id = u.id
                  WHERE c.id = :id";
        return $this->query_unique($query, [':id' => $course_id]);
    }

    /**
     * READ course by title
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
                    difficulty_level = :difficulty_level,
                    image_url = :image_url
                  WHERE id = :id";
        $this->execute($query, [
            ':id' => $course['id'],
            ':title' => $course['title'],
            ':description' => $course['description'],
            ':price' => $course['price'],
            ':difficulty_level' => $course['difficulty_level'],
            ':image_url' => $course['image_url'] ?? null
        ]);
    }

    /**
     * DELETE course
     */
    public function delete_course($course_id) {
        $this->execute("DELETE FROM courses WHERE id = :id", [':id' => $course_id]);
    }
   
    public function count_all_courses() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM courses");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>