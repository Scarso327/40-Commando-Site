<?php
/**
 * Created by PhpStorm.
 * User: ScarsoLP
 * Date: 15/03/2018
 * Time: 10:41 PM
 */
class DisplayError extends Controller
{
    public function __construct($myError, $needReDirect = false, $onlyError = false)
    {
        parent::__construct(false);
        
        $errors = Errors;
        $error = require $errors;

        if (!(isset($error[$myError]))) { $myError = "#Fe003"; }

        $error_title = $error[$myError][0];
        $error_message = $error[$myError][1];

        Controller::addCrumb(array("Error", ""));

        if($needReDirect) {
            $myError = str_replace("#", "", $myError);
            header("Location: ".URL."e/".$myError);
        } else {
            View::$isError = true;

            $error_page_info = array (
                'title' => $myError,
                'fulllength' => false,
                'error_title' => $error_title,
                'error_message' => $error_message
            );

            if ($onlyError) {
                foreach ($error_page_info as $key => $value) {
                    $this->{$key} = $value;
                }
                
                include(ROOT . 'views/system/error.php');
            } else {
                Controller::$currentPage = $myError;
                Controller::$subPage = "";
                Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/banner', ROOT . 'views/breadcrumbs', ROOT . 'views/system/error'), false, array_merge(array (
                    'css' => array ('frontend.min.css'),
                    'nav_fixed' => true,
                    'title' => $myError,
                    'fulllength' => false,
                    'error_title' => $error_title,
                    'error_message' => $error_message
                ), $error_page_info));
            }
        }
    }
}