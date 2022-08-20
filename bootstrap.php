<?php

ini_set('display_errors', 0);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ERROR);

define('HOST', 'localhost');
define('BANCO', 'api');
define('USUARIO', 'root');
define('SENHA', '');

define('DS', DIRECTORY_SEPARATOR);
//define(DIR_APP, 'C:\xampp2\htdocs\apirest');
define('DIR_APP', __DIR__);
define('DIR_PROJETO', 'apirest');

if (file_exists('autoload.php')){
    include 'autoload.php';
} else {
    echo 'Erro ao incluir bootstrap';exit;
}
