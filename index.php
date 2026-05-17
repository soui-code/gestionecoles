<?php 
session_start();
 ini_set('display_errors', 1);
 error_reporting(E_ALL);
 define('BASE_URL', 'http://localhost/gestionecole');
require_once 'core/autoload.php';

$controller = $_GET['c'] ?? 'auth';
$action = $_GET['a'] ?? 'login';

$controller = ucfirst($controller) . 'Controller';

if(class_exists($controller) && method_exists($controller, $action)) {
    $c = new $controller();
    $c->$action();
} else {
    echo "404 - Page non trouvé";
}




 


 ?> 