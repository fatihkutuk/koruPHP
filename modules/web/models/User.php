<?php

namespace App\Web\Models;

use Koru\Model;

class User extends Model
{
    protected string $table = 'users';
    
    public function getActiveUsers(): array
    {
        return sql("
            SELECT * FROM users 
            WHERE status = 'active' 
            ORDER BY created_at DESC
        ");
    }
    
    public function findByEmail(string $email): ?array
    {
        return sql_one("
            SELECT * FROM users 
            WHERE email = ?
        ", [$email]);
    }
    
    public function getUserStats(): array
    {
        return [
            'total' => sql_one("SELECT COUNT(*) as count FROM users")['count'],
            'active' => sql_one("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'],
            'new_this_month' => sql_one("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['count']
        ];
    }
}