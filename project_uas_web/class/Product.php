<?php
class Product {
    private $db;
    private $table = 'products';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Count all products
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Product.php - Tambahkan method ini
    public function countLowStock($threshold = 10) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE is_active = 1 AND stock <= :threshold";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Get recent products
    public function getRecent($limit = 5) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1 
                ORDER BY p.created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get product by ID
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Product.php - tambahkan method
    public function getByCategory($categoryId, $limit = 4, $excludeId = null) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1 AND p.category_id = :category_id";
        
        if ($excludeId) {
            $sql .= " AND p.id != :exclude_id";
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get all products (for product page)
    // Product.php - method getAll()
    public function getAll($limit = 12, $offset = 0, $categoryId = null, $search = null) {
        // Validasi parameter
        $limit = max(1, intval($limit)); // Pastikan minimal 1
        $offset = max(0, intval($offset)); // Pastikan tidak negatif

        $sql = "SELECT p.*, c.name as category_name, c.icon 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1";

        $params = [];

        if ($categoryId) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        if ($search) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Count products for pagination
    public function countProducts($categoryId = null, $search = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} p WHERE p.is_active = 1";
        
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }
        
        if ($search) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Create product
    public function create($data) {
        $slug = $this->createSlug($data['name']);
        
        $sql = "INSERT INTO {$this->table} 
                (name, slug, description, price, discount_price, category_id, 
                 image_url, stock, is_featured, is_active) 
                VALUES 
                (:name, :slug, :description, :price, :discount_price, :category_id, 
                 :image_url, :stock, :is_featured, :is_active)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':discount_price', $data['discount_price']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':image_url', $data['image_url']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':is_featured', $data['is_featured']);
        $stmt->bindParam(':is_active', $data['is_active']);
        
        return $stmt->execute();
    }
    
    // Helper: Create slug
    private function createSlug($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        
        return $text ?: 'product-' . uniqid();
    }
}
?>