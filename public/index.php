<?php
ob_start(); // For the "remember session"...

// Directories...
define("ROOT", dirname(__DIR__) . DIRECTORY_SEPARATOR);
define("WEB", ROOT . 'public' . DIRECTORY_SEPARATOR);

define('SETTING', require ROOT.'settings.php');
define('LAN_OVERRIDE', require ROOT.'lang-override.php');
define('Errors', ROOT.'errors.php');

define('URL_PROTOCOL', ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://"));
define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
define('URL', URL_PROTOCOL . URL_DOMAIN . '/' . SETTING["sub-directory"]);

// Database Settings (For Webserver)
define('DB_TYPE', SETTING["db-type"]);
define('DB_HOST', SETTING["db-host"]);
define('DB_NAME', SETTING["db-name"]);
define('DB_USER', SETTING["db-user"]);
define('DB_PASS', SETTING["db-pass"]);
define('DB_CHARSET', SETTING["db-set"]);

// Other Settings
define('VER', '1.0.0');
define('API_VER', '1.0.0');

require_once "../includes.php";
Session::start();

new Application;