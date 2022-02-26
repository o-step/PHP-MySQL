<?php
class Route
{
 public function getPathArray() {

 $BASE_URL = 'localhost:80/journal/';
 $pathArray = [];
 $currentPath = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 if (strpos($currentPath, $BASE_URL) >= 0) {
 $tmpPath = substr($currentPath, strlen($BASE_URL)); 
}
$tmpArray = explode("/", $tmpPath);

$pathArray = array_values(array_filter($tmpArray, function($element) {
return !empty($element);
}));

return $pathArray;
}
static function start()
{
// указываем контроллер и действие по умолчанию
$controller_name = 'News'; 
$action_name = 'index';

//Обращаемся к методу нашего класса и получаем массив с роутингом
$routes = self::getPathArray();
// получаем имя контроллера, если есть
if ( !empty($routes[0]) )
{
$controller_name = $routes[0];
}

// получаем имя экшена
if ( !empty($routes[1]) )
{
$action_name = $routes[1];
}
// добавляем префиксы
$model_name = 'Model_'.$controller_name;
$controller_name = 'Controller_'.$controller_name;
$action_name = 'action_'.$action_name;
// подцепляем файл с классом модели (файла модели может и не быть)
$model_file = strtolower($model_name).'.php';
$model_path = "models/".$model_file;
if(file_exists($model_path))
{
include "models/".$model_file;
}
// подцепляем файл с классом контроллера
$controller_file = strtolower($controller_name).'.php';
$controller_path = "controllers/".$controller_file;
if(file_exists($controller_path))
{
include "controllers/".$controller_file;
}
else
{
/*
правильно было бы кинуть здесь исключение,
но для упрощения сразу сделаем редирект на страницу 404
*/
Route::ErrorPage404();
}

// создаем контроллер и действие с моделью, если есть
$controller = new $controller_name;
$action = $action_name;

//Если у класса существует указанный выше метод action_index например, вызываем действие контроллера
if(method_exists($controller, $action))
{
// вызываем действие контроллера
$controller->$action();
}
else
{ 
	// здесь также разумнее было бы кинуть исключение
	Route::ErrorPage404();
}

}

//Метод для организации редиректа на 404 при любом нештатном случае
function ErrorPage404()
{
$host = 'http://'.$_SERVER['HTTP_HOST'].'/journal/';
header('HTTP/1.1 404 Not Found');
header("Status: 404 Not Found");
header('Location:'.$host.'404');
}
}
?> 