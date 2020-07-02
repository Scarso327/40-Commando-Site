<?php

class Login extends Controller {

    public function __construct() {
        parent::__construct(false);
    }

    public function index () {
        // If action isset then time to login!
        if ((isset($_GET['_action']))) {
            Session::remove("reason"); // Wipe this shit...
            Steam::OpenIDSteam();
        } else {
            if (Session::get("reason")) {
                Controller::$currentPage = "Login";
                Controller::buildPage(array(ROOT . 'views/login/page'), false, array (
                    'css' => array ('login.min.css'),
                    'reason' => Session::get("reason")
                ));
                Session::remove("reason");
            } else {
                header("Location: ".URL."login/?_action");
            }
        }
    }
}