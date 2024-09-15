<?php
namespace helpers;
class View{
    public string $view;
    public array $params;
    public function __construct($view, $params = []){
        $this->view = $view;
        $this->params = $params;
    }
    public static function make($view, $params = [])
    {
        return new static($view, $params);
    }
    public function render()
    {
        $viewpath = __DIR__ . '/../views/' . $this->view . '.php';
        if (!file_exists($viewpath)) {
            return 'View not found';
        }
        ob_start();
        include_once __DIR__ . '/../views/' . $this->view . '.php';
        return (string) ob_get_clean();
    }
    public function renderLayout($view)
    {
        ob_start();
        include_once __DIR__ . '/../views/layout/main.php';
        return str_replace('{{content}}', $view, ob_get_clean());
    }
}