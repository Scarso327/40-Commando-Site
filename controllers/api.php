<?php

class API extends Controller {

    public static $internal = false; // Indicates whether we require internal "feedback"...

    public function __construct() {
        header('Content-Type: application/json');
    }

    public function info () {
        $info = array("version" => API_VER);

        if (self::$internal) { return $info; }
        self::return($info);
    }

    public function responses ($paramID = null) {
        if (!self::$internal) { self::auth(); } // Only required if external...

        $responses = Logs::getResponse((($paramID == null) ? $_POST['id'] : $paramID));

        if (!$responses) {
            if (self::$internal) { return false; }
            self::return(array("result" => "fail", "reason" => "response-not-found"));
            exit;
        }

        if (self::$internal) { return $responses; }

        self::return(array(
            "result" => "success", 
            "responses" => $responses
        ));
    }
    
    public function toggleTheme () {
        if (isset($_COOKIE['dark-theme'])) {
            setcookie("dark-theme", null, -1, "/");
        } else {
            setcookie("dark-theme", true, time() + (10 * 365 * 24 * 60 * 60), "/");
        }

        if (self::$internal) { return true; } // Wtf...
        self::return(array("result" => "success"));
    }

    private function auth () {
        if (!parent::__construct(true, true)) {
            self::return(array("result" => "fail", "reason" => "auth-failed"));
            die();
        }
    }

    private function return ($json) {
        echo json_encode($json, JSON_PRETTY_PRINT);
    }

    // Created just in case... Likely won't use...
    private function makeExternalAPICall($api, $method = "POST") {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'authorization: '.EXT_API_KEY
        ));
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}