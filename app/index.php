<?php
include_once '/var/www/html/src/DishController.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// All of our endpoints will start by /dish
if ($uri[2] !== 'dish') {
  header("HTTP/1.1 404 Not Found");
  exit();
}

// the dish id is optional
$dishId = null;
if (isset($uri[3])) {
  $dishId = (int)$uri[3];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
// Let's pass the request method and the dish id to the Dish controller and process the request
$controller = new DishController($requestMethod, $dishId);
$controller->processRequest();