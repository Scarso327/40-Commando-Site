<?php

class App extends Controller {

    public function __construct() {
        parent::__construct(true);

        if (Account::$member->is_in_unit == 1) {
            new DisplayError("#Fe012");
            die();
        }

        Controller::$currentPage = "Application";
        Controller::addCrumb(array("Application", "app"));
    }
    
    public function index() {
        $page = "Account";
        $stages = array("register account", "application", "interview", "all arms commando course", "assessment");
        $completed = array("Register Account");
        $forms = array();
        $params = array("css" => array("frontend.min.css", "application.min.css"));

        $candidate_id = Member::getCandidateID(Account::$steamid);

        if ($candidate_id != -1) {
            Controller::addCrumb(array("Progress", "app"));

            $record = Records::getRecord($candidate_id);

            if (!$record) {
                new DisplayError("#500");
                exit;
            }
            
            $params["record"] = $record;

            if ($record->app_id != -1) {
                array_push($completed, "Application");
            } else {
                $form = Form::getForm(1);

                if (!$form) {
                    new DisplayError("#500");
                    exit;
                }

                $fields = Form::getFields($form->id);

                if (!$fields) {
                    new DisplayError("#Fe006");
                    exit;
                }

                $forms = array_merge($forms, array(array($form, $fields)));

                // Form Check... (Do it here as we only want them to be able to do this if they've not already subbed one...)
                if (isset($_GET['form'])) {
                    Forms::onFormSubmit($form, $fields);
                }
            }

            if ($record->interview_id != -1) {
                array_push($completed, "Interview");
            }

            if (-1 != -1) {
                array_push($completed, "All Arms Commando Course");
            }

            if ($record->assessment_id != -1) {
                array_push($completed, "Assessment");
            }

            $cCount = count($completed);
            $current = (($cCount <= count($stages)) ? $stages[$cCount] : "");

            $params["current"] = $current;
            $params["completed"] = $completed;

            Controller::addCrumb(array(ucwords(strtolower($page)), "app/?stage=".$page));
        } else {
            if (isset($_GET['start-process'])) {
                $record = Records::createRecord(Account::$steamid);
                if (!$record) { new DisplayError("#500"); exit; }
                if (!Member::setRecordID(Account::$steamid, $record)) { new DisplayError("#500"); exit; }
                header("Location: ".URL."app");
            }

            Controller::addCrumb(array("Beginning", "app"));
        }

        $page = ((isset($_GET['stage'])) ? $_GET['stage'] : "register account");

        if (!in_array(strtolower($page), $stages)) {
            new DisplayError("#404");
            die();
        }

        if (in_array(strtolower($page), array("all arms commando course", "assessment"))) {
            $params["member"] = Member::getMember(Account::$steamid);

            if (!$params["member"]) {
                new DisplayError("#500");
                exit;
            }

            array_push($params["css"], "structure.min.css");
        }

        $params["page"] = ucfirst(strtolower($page));

        if (count($forms) > 0) { $params['page_forms'] = $forms; }
        Controller::buildPage(array(ROOT . 'views/navbar', ROOT . 'views/breadcrumbs', ROOT . 'views/app/progress'), true, $params);
    }
}