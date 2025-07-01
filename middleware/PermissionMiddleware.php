<?php

namespace App\Middleware;

use Koru\Auth;

class PermissionMiddleware
{
    private string $permission;
    
    public function __construct(string $permission = '')
    {
        $this->permission = $permission;
    }
    
    public function handle(): bool
    {
        // Router'dan permission bilgisini al
        $this->extractPermissionFromRoute();
        
        if (!Auth::hasPermission($this->permission)) {
            http_response_code(403);
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['error' => "Permission '{$this->permission}' required"]);
            } else {
                echo "<h1>Yetki Gerekli</h1><p>'{$this->permission}' yetkisine sahip değilsiniz.</p>";
            }
            return false;
        }
        
        return true;
    }
    
    private function extractPermissionFromRoute(): void
    {
        // URL'den permission çıkar: /middleware/permission:scada
        $uri = $_SERVER['REQUEST_URI'];
        if (preg_match('/permission:(\w+)/', $uri, $matches)) {
            $this->permission = $matches[1];
        }
    }
}