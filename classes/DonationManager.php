<?php
class DonationManager {
    protected $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function fulfillRequest($requestId, $donorId) {
        try {
            $this->conn->beginTransaction();
            
            // Common fulfillment logic
            $stmt = $this->conn->prepare("SELECT * FROM DonationRequest WHERE request_id = ?");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // ... rest of the shared fulfillment logic ...
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}