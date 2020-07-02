<?php

class Structure extends Controller {
    
    public function __construct() {
        parent::__construct(false);

        Controller::$currentPage = "Ranks & Positions";
        Controller::addCrumb(array("Ranks & Positions", "structure"));
    }

    public function index() {
        $ranks = array();

        foreach (Application::$ranks as $rank) {
            if ($rank->info) {
                foreach ($rank->info as $key=>$value) {
                    if (!array_key_exists($value->type, $ranks)) { $ranks[$value->type] = array(); }

                    array_push($ranks[$value->type], $value);
                }
            }
        }

        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/banner', ROOT . 'views/dash/structure'), true, array (
            'css' => array ('frontend.min.css', 'structure.min.css'),
            'nav_fixed' => true,
            'title' => 'Ranks & Positions',
            'fulllength' => false,
            'ranks' => $ranks
        ));
    }
}