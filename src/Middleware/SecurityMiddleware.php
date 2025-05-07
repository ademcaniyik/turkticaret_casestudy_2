<?php

namespace App\Middleware;

class SecurityMiddleware
{
    public function handle(): void
    {
        // CORS ayarları
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // OPTIONS isteklerini yanıtla
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // XSS koruması için Content-Type ve X-Content-Type-Options
        header('Content-Type: application/json; charset=UTF-8');
        header('X-Content-Type-Options: nosniff');

        // Temel güvenlik başlıkları
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        header('Content-Security-Policy: default-src \'none\'; frame-ancestors \'none\'');

        // Rate limiting kontrolü
        $this->checkRateLimit();
    }

    private function checkRateLimit(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_limit:{$ip}";
        
        // Redis veya benzeri bir önbellekleme sistemi kullanılabilir
        // Şimdilik basit bir dosya tabanlı rate limiting uygulayalım
        $limitFile = sys_get_temp_dir() . "/{$key}";
        
        if (file_exists($limitFile)) {
            $data = json_decode(file_get_contents($limitFile), true);
            $now = time();
            
            // Son 1 dakika içindeki istek sayısını kontrol et
            if ($now - $data['timestamp'] <= 60) {
                if ($data['count'] >= 60) { // Dakikada maksimum 60 istek
                    http_response_code(429);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Too many requests. Please try again later.'
                    ]);
                    exit;
                }
                $data['count']++;
            } else {
                $data = [
                    'timestamp' => $now,
                    'count' => 1
                ];
            }
        } else {
            $data = [
                'timestamp' => time(),
                'count' => 1
            ];
        }
        
        file_put_contents($limitFile, json_encode($data));
    }
}
