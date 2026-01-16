<?php
class Category {
    private $db;
    private $table = 'categories';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all categories
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get category by slug
    public function getBySlug($slug) {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get category by ID
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Create new category
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, slug, description, icon) 
                VALUES (:name, :slug, :description, :icon)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':icon', $data['icon']);
        
        return $stmt->execute();
    }
    
    // Update category
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name, 
                slug = :slug, 
                description = :description, 
                icon = :icon 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':icon', $data['icon']);
        
        return $stmt->execute();
    }
    
    // Delete category
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Get categories with product count
    public function getWithCount() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1 
                GROUP BY c.id 
                ORDER BY c.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Category.php - Tambahkan method ini
    public function getSalesByCategory() {
        $sql = "SELECT c.name as category, COUNT(oi.id) as sales 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                JOIN categories c ON p.category_id = c.id 
                GROUP BY c.id 
                ORDER BY sales DESC 
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>