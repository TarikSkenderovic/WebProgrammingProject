<?php

class BaseDao {
    protected $conn;

    public function __construct() {
        
        $servername = "127.0.0.1"; 
        $username = "root"; 
        $password = ""; 
        $dbname = "course_platform"; 

        try {
            
            $this->conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);   
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

   
    protected function query_unique($query, $params) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

  
    protected function query($query, $params) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  
    protected function execute($query, $params) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
?>