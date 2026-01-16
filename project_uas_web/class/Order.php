<?php
class Order {
    private $db;
    private $table = 'orders';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Count all orders
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Count orders by status
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Get total revenue
    public function getTotalRevenue() {
        $sql = "SELECT SUM(total_amount) as total FROM {$this->table} WHERE status = 'delivered'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Get recent orders
    public function getRecent($limit = 5) {
        $sql = "SELECT o.*, u.full_name as customer_name 
                FROM {$this->table} o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get daily sales
    public function getDailySales($days = 7) {
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as sales 
                FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY) 
                GROUP BY DATE(created_at) 
                ORDER BY date";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($result as $row) {
            $data[$row['date']] = (int)$row['sales'];
        }
        return $data;
    }
    
    // Get daily revenue
    public function getDailyRevenue($days = 7) {
        $sql = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue 
                FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY) 
                AND status = 'delivered'
                GROUP BY DATE(created_at) 
                ORDER BY date";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($result as $row) {
            $data[$row['date']] = (float)$row['revenue'];
        }
        return $data;
    }
    
    // Get category sales
    public function getCategorySales() {
        $sql = "SELECT c.name as category, COUNT(oi.id) as sales 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                JOIN categories c ON p.category_id = c.id 
                GROUP BY c.id 
                ORDER BY sales DESC 
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>