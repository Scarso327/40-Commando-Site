<?php

class Application {

    private $controller = null;
    private $action = null;
    private $params = array();

    public static $isDark = true;
    public static $ranks = array();

    public function __construct() {
        self::URLSetup();
        self::$isDark = isset($_COOKIE['dark-theme']);
        self::$ranks = System::getRanks();

        if (isset($_GET['logout'])) { Account::logout(); } // Logout...
        
        switch (true) {

            case ($this->controller):

                // If the controller is equal to E it's an error redirect...
                if ($this->controller == "e") {
                    new DisplayError('#'.$this->action);
                    exit;
                }

                if (file_exists(ROOT.'controllers/'.$this->controller.'.php')) {

                    require_once ROOT.'controllers/'.$this->controller.'.php';
                    $this->controller = new $this->controller;

                    self::actionHandle();
                    exit;
                } else {
                    new DisplayError("#404");
                }

                break;

            default:
                $this->controller = new Home;
                self::actionHandle();
        }
    }

    public function URLSetup () {
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            $this->controller = isset($url[0]) ? $url[0] : null;
            $this->action = isset($url[1]) ? $url[1] : null;
            unset($url[0], $url[1]);
            $this->params = array_values($url);
        }
    }

    public static function convertBool($bool) {
        return ($bool ? 1 : 0);
    }

    public static function randomStrGen($length = 64) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        
        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= $characters[rand(0, $charactersLength - 1)];
        }

        return $str;
    }

    public function actionHandle () {
        if($this->action) {
            if (Steam::isSteamID($this->action)) {
                $this->controller->index($this->action);
            } else {
                if(method_exists($this->controller, $this->action)) {
                    if (!empty($this->params)) {
                        call_user_func_array(array($this->controller, $this->action), $this->params);
                    } else {
                        $this->controller->{$this->action}();
                    }
                } else {
                    new DisplayError("#404");
                }
            }
        } else {
            $this->controller->index();
        }
    }

    public static function getRankInfo($key, $assignment = "") {
        $assignment_key = str_replace(" ", "-", strtolower($assignment));
        
        if (array_key_exists($key, self::$ranks)) {
            if (array_key_exists($assignment_key, self::$ranks[$key]->info)) {
                return self::$ranks[$key]->info[$assignment_key];
            }

            return self::$ranks[$key]->info["default"];
        }

        return false;
    }

    public static function buildName($shortrank, $firstname, $lastname, $is_acting = false) {
        return (($is_acting) ? "A/" : "").$shortrank.". ".$firstname[0].". ".$lastname;
    }
}