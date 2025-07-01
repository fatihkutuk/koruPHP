<?php

namespace Koru;

class Controller
{
    protected View $view;
    protected Database $database;
    
    public function __construct()
    {
        $this->view = app()->getView();
        $this->database = app()->getDatabase();
    }
    
    protected function render(string $view, array $data = []): string
    {
        echo $this->view->render($view, $data);
        return '';
    }
    
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}