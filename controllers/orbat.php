<?php

class ORBAT extends Controller {
    
    public function __construct() {
        parent::__construct(false);

        Controller::$currentPage = "ORBAT";
        Controller::addCrumb(array("ORBAT", "orbat"));
    }

    public function index() {
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/banner', ROOT . 'views/orbat/orbat'), true, array (
            'css' => array ('frontend.min.css', 'orbat.min.css'),
            'nav_fixed' => true,
            'title' => 'Order of Battle',
            'fulllength' => false,
            'companies' => Assignments::getCompanies()
        ));
    }
}