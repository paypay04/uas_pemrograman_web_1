<?php
class Cart {
    private $db;
    private $table = 'cart_items';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get cart items for user
    public function getCartItems($userId) {
        $sql = "SELECT ci.*, p.name, p.price, p.discount_price, p.image_url, 
                       p.stock, c.name as category_name 
                FROM {$this->table} ci
                JOIN products p ON ci.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE ci.user_id = :user_id AND p.is_active = 1
                ORDER BY ci.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Add item to cart
    public function addItem($userId, $productId, $quantity = 1) {
        // Check if item already exists
        $existing = $this->getCartItem($userId, $productId);
        
        if ($existing) {
            // Update quantity
            return $this->updateQuantity($userId, $productId, $existing['quantity'] + $quantity);
        } else {
            // Add new item
            $sql = "INSERT INTO {$this->table} (user_id, product_id, quantity) 
                    VALUES (:user_id, :product_id, :quantity)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':quantity', $quantity);
            
            return $stmt->execute();
        }
    }
    
    // Update item quantity
    public function updateQuantity($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $productId);
        }
        
        $sql = "UPDATE {$this->table} SET quantity = :quantity 
                WHERE user_id = :user_id AND product_id = :product_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        
        return $stmt->execute();
    }
    
    // Remove item from cart
    public function removeItem($userId, $productId) {
        $sql = "DELETE FROM {$this->table} 
                WHERE user_id = :user_id AND product_id = :product_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        
        return $stmt->execute();
    }
    
    // Clear cart
    public function clearCart($userId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }
    
    // Get cart item count
    public function getCartCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    // Get cart total amount
    public function getCartTotal($userId) {
        $sql = "SELECT SUM(ci.quantity * COALESCE(p.discount_price, p.price)) as total
                FROM {$this->table} ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = :user_id AND p.is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Get specific cart item
    private function getCartItem($userId, $productId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND product_id = :product_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Check if product is in cart
    public function isInCart($userId, $productId) {
        $item = $this->getCartItem($userId, $productId);
        return $item ? true : false;
    }
}
?>