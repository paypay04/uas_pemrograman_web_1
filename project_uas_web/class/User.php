<?php
class User {
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Count all users
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Get user by ID
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get recent users
    public function getRecent($limit = 5) {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'user' 
                ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get daily registrations
    public function getDailyRegistrations($days = 7) {
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as registrations 
                FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY) 
                GROUP BY DATE(created_at) 
                ORDER BY date";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Login function
    public function login($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username OR email = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            return true;
        }
        return false;
    }
    
    // Register function
    public function register($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO {$this->table} (username, email, password, full_name, phone, address) 
                VALUES (:username, :email, :password, :full_name, :phone, :address)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        
        return $stmt->execute();
    }
    
    // Get all users (for admin)
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>