<?php

namespace App\Middleware;

use App\Database\Database;
use PDO;

class RateLimiter
{
    private const LIMIT = 100; // 100 istek
    private const PERIOD = 60; // 60 saniye

    public function handle(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $db = Database::getConnection();
        
        // IP'nin son 60 saniyedeki istek sayısını kontrol et
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM rate_limit 
            WHERE ip = :ip 
            AND created_at >= DATE_SUB(NOW(), INTERVAL :period SECOND)
        ");
        
        $stmt->execute([
            ':ip' => $ip,
            ':period' => self::PERIOD
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] >= self::LIMIT) {
            http_response_code(429);
            echo json_encode([
                'status' => 'error',
                'message' => 'Too many requests. Please try again later.',
                'code' => 429
            ]);
            exit;
        }
        
        // Yeni isteği kaydet
        $stmt = $db->prepare("
            INSERT INTO rate_limit (ip, created_at) 
            VALUES (:ip, NOW())
        ");
        
        $stmt->execute([
            ':ip' => $ip
        ]);
    }
}
