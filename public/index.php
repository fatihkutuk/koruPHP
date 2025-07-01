<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/Helpers.php';

use Koru\Application;

try {
    // Global uygulama instance'ı
    global $app;
    $app = new Application();

    // Modülü belirle (URL'den veya konfigürasyondan)
    $module = $_GET['module'] ?? 'web';
    $app->setModule($module);

    // Uygulamayı çalıştır
    $app->run();

} catch (\Throwable $e) {
    // Production'da basit hata, development'ta detaylı
    if (\Koru\Environment::isDebugging()) {
        echo "<h1>Başlatma Hatası</h1>";
        echo "<p><strong>Hata:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Dosya:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        echo "<h1>Sistem Hatası</h1><p>Lütfen daha sonra tekrar deneyin.</p>";
    }
}