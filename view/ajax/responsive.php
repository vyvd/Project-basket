<?php
// @todo: migrate the other /ajax/controller methods over to this

if($this->get["c"] == "" || $this->get["a"] == "") {
    header("HTTP/1.0 404 Not Found");
    include APP_ROOT_PATH . 'view/404/responsive.php';
    exit;
}

$controller = str_replace(" ", "", lcfirst(ucwords(str_replace("-", " ", $this->get["c"])))).'Controller';
$action = str_replace(" ", "", lcfirst(ucwords(str_replace("-", " ", $this->get["a"]))));

include APP_ROOT_PATH . 'controller/' . $controller . '.php';
$this->controller = new $controller();

$this->controller->$action();