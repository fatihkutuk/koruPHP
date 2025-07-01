<?php

namespace App\Web\Controllers;

use Koru\Controller;

class ScadaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Permission kontrolü
        if (cannot('scada.view')) {
            http_response_code(403);
            echo "<h1>Erişim Reddedildi</h1><p>SCADA panelini görüntüleme yetkiniz yok.</p>";
            exit;
        }
    }
    
    public function panel(): void
    {
        $user = user();
        
        // Kullanıcının SCADA kontrol yetkisi var mı?
        $canControl = can('scada.control');
        $canConfigure = can('scada.config');
        
        // SCADA verileri
        $sensorData = sql("
            SELECT * FROM sensor_data 
            ORDER BY created_at DESC 
            LIMIT 20
        ");
        
        $this->render('web.scada.panel', [
            'title' => 'SCADA Kontrol Paneli',
            'user' => $user,
            'sensorData' => $sensorData,
            'permissions' => [
                'can_control' => $canControl,
                'can_configure' => $canConfigure,
                'can_view_only' => !$canControl && !$canConfigure
            ]
        ]);
    }
    
    public function controlDevice(): void
    {
        if (cannot('scada.control')) {
            $this->json(['error' => 'Insufficient permissions'], 403);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $deviceId = $input['device_id'] ?? '';
        $action = $input['action'] ?? '';
        
        // Device control logic
        logger()->info("SCADA device controlled", [
            'user_id' => auth_user_id(),
            'device_id' => $deviceId,
            'action' => $action
        ]);
        
        $this->json([
            'success' => true,
            'message' => "Device {$deviceId} {$action} command sent"
        ]);
    }
}