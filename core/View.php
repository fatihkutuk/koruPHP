<?php

namespace Koru;

use Twig\Environment as TwigEnvironment;  // ← Alias kullan
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class View
{
    private TwigEnvironment $twig;  // ← TwigEnvironment kullan
    private array $globalData = [];
    
    public function __construct(string $viewPath = '')
    {
        $viewPath = $viewPath ?: __DIR__ . '/../modules';
        
        // Twig loader'ı yapılandır
        $loader = new FilesystemLoader();
        
        // Tüm modüllerin view dizinlerini ekle
        $modules = ['web', 'api', 'admin'];
        foreach ($modules as $module) {
            $modulePath = $viewPath . "/{$module}/views";
            if (is_dir($modulePath)) {
                $loader->addPath($modulePath, $module);
            }
        }
        
        // Cache ve debug ayarları (bizim Environment sınıfımızı kullan)
        $options = [
            'cache' => \Koru\Environment::isProduction() ? __DIR__ . '/../storage/cache/twig' : false,  // ← \Koru\Environment
            'debug' => \Koru\Environment::isDebugging(),  // ← \Koru\Environment
            'auto_reload' => \Koru\Environment::isDevelopment(),  // ← \Koru\Environment
        ];
        
        $this->twig = new TwigEnvironment($loader, $options);  // ← TwigEnvironment
        
        // Debug extension (development'ta)
        if (\Koru\Environment::isDebugging()) {  // ← \Koru\Environment
            $this->twig->addExtension(new DebugExtension());
        }
        
        // Custom functions/filters ekle
        $this->addCustomFunctions();
    }
    
    public function render(string $template, array $data = []): string
    {
        // Global data ile merge et
        $data = array_merge($this->globalData, $data);
        
        // Template adını düzenle: web.user.index -> @web/user/index.html.twig
        if (strpos($template, '.') !== false) {
            $parts = explode('.', $template);
            $namespace = array_shift($parts);
            $path = implode('/', $parts) . '.html.twig';
            $template = "@{$namespace}/{$path}";
        }
        
        try {
            return $this->twig->render($template, $data);
        } catch (\Throwable $e) {
            // Development'ta detaylı hata
            if (\Koru\Environment::isDebugging()) {  // ← \Koru\Environment
                throw new \Exception("Template render error: " . $e->getMessage() . "\nTemplate: {$template}");
            }
            
            // Production'da basit hata
            logger()->error("Template render error", [
                'template' => $template,
                'error' => $e->getMessage()
            ]);
            
            return "<h1>Template Error</h1><p>Template could not be rendered.</p>";
        }
    }
    
    public function share(string $key, $value): void
    {
        $this->globalData[$key] = $value;
    }
    
    private function addCustomFunctions(): void
    {
        // URL helper
        $this->twig->addFunction(new \Twig\TwigFunction('url', function($path = '') {
            return url($path);
        }));
        
        // Config helper  
        $this->twig->addFunction(new \Twig\TwigFunction('config', function($key, $default = null) {
            return config($key, $default);
        }));
        
        // Asset helper
        $this->twig->addFunction(new \Twig\TwigFunction('asset', function($path) {
            return url('assets/' . ltrim($path, '/'));
        }));
        
        // Route helper (basic)
        $this->twig->addFunction(new \Twig\TwigFunction('route', function($name, $params = []) {
            // Basit route helper - geliştirilecek
            return $name;
        }));
    }
    
    public function getTwig(): TwigEnvironment  // ← TwigEnvironment
    {
        return $this->twig;
    }
}