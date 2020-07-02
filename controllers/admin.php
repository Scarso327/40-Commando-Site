<?php

class Admin extends Controller {

    public function __construct() {
        parent::__construct(true);

        if (!Accounts::IsAdmin(Account::$steamid)) {
            new DisplayError("#403");
            exit;
        };
        
        Controller::$currentPage = "Admin";
        Controller::addCrumb(array("Admin", "admin/"));
    }

    public function index () {
        $params = array ();

        Controller::$subPage = "Home";
        Controller::addCrumb(array("Home", "admin/"));
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/admin/home'), false, $params);
    }
}