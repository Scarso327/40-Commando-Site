<?php

class Home extends Controller {

    public function __construct() {
        parent::__construct(false);

        Controller::$currentPage = "Home";
        Controller::addCrumb(array("Home", ""));
    }

    public function index () {
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/dash/body'), true, array (
            'css' => array ('frontend.min.css'),
            'nav_fixed' => true
        ));
    }
}