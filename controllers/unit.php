<?php

class Unit extends Controller {

    static $basePage = array(ROOT . 'views/navbar', ROOT . 'views/breadcrumbs');

    public function __construct() {
        parent::__construct(true);

        if (Account::$member->is_in_unit != 1) {
            new DisplayError("#Fe011");
            die();
        }

        Controller::$currentPage = "Unit Access";
        Controller::addCrumb(array("Unit Access", "unit"));
    }

    public function index() {
        self::personnel();
    }

    public function personnel($steamid = null, $subpage = "") {
        $pages = array();
        $params = array(
            'css' => array (
                'unit.min.css'
            )
        );

        Controller::$subPage = "Personnel";
        Controller::addCrumb(array("Personnel", "unit/personnel"));

        Filter::XSSFilter($steamid);
        Filter::clearAll($steamid);

        if ($steamid != null) {
            $params['member'] = (Member::getMember($steamid));

            if (!$params['member']) {
                new DisplayError("#Fe005", false);
                exit;
            }

            Controller::addCrumb(array((($params['member']->first_name == "" || $params['member']->last_name == "") ? $params['member']->steamName : $params['member']->first_name." ".$params['member']->last_name), "unit/personnel/".$steamid));

            Forms::$steamidOverride = $params['member']->steamid;

            $params['subpage'] = strtolower ($subpage);

            switch ($subpage) {
                case '':
                    $params["history"] = self::getHistory($steamid);
                    $params["buttons"] = array();

                    $form_ids = array(9);

                    if ($params['member']->is_in_unit == 1) {
                        $form_ids = array_merge($form_ids, array(10, 11, 12));
                    }

                    foreach ($form_ids as $form_id) {
                        $form = Form::getForm($form_id);

                        if ($form && Form::canSubmitForm($form)) {
                            View::addForm($form);
                            array_push($params["buttons"], array("id" => $form->id, "name" => $form->name, "action_name" => $form->action_name, "colour" => $form->form_colour));
                        }
                    }
                    break;
                case 'awards':
                    if ($params['member']->is_in_unit != 1) {
                        new DisplayError("#404", false);
                        exit;
                    }

                    if (isset($_GET['remove'])) {
                        $award_to_remove = Filter::XSSFilter($_GET['remove']);

                        if (!Award::removeAward($params['member']->steamid, $award_to_remove)) {
                            new DisplayError("#500");
                            die();
                        }

                        Logs::log(array("steamid" => array("fieldName" => "steamid", "value" => $params['member']->steamid)), Account::$steamid, "Award", "Revoked", 0);
                        header("Location: ".URL."unit/personnel/".$params['member']->steamid."/awards");
                    }

                    $params["form"] = Form::getForm(7);
                    $params["fields"] = Form::getFields(7);

                    if (!$params["form"] || !$params["fields"]) {
                        new DisplayError("#500");
                        exit;
                    }

                    $params["awards"] = Award::getMembersAwards($params['member']->steamid);
                    break;
                default:
                    new DisplayError("#404", false);
                    exit;
            }

            array_push($params['css'], 'profile.min.css');
            array_push($pages, ROOT . "views/unit/personnel/profile");
        } else {
            $form = Form::getForm(8);

            if ($form) {
                if (Form::canSubmitForm($form)) {
                    View::addForm($form);
                    parent::addCrumbButton("<a title=\"New Member\" onclick=\"showModal(this)\" data-id=\"8\" data-name=\"Member Addition\"><i class=\"fas fa-plus-circle\"></i></a>");
                }
            } else {
                new DisplayError("#500");
                exit;
            }

            $params["companies"] = Assignments::getCompanies();
            $params['orbat_search'] = true;
            
            array_push($params['css'], 'orbat.min.css');
            array_push($pages, ROOT . 'views/orbat/orbat');
        }

        Controller::buildPage(array_merge(self::$basePage, $pages), true, $params);
    }

    public function form($form = null, $submitted = false) {
        $formInfo = explode("-", $form);
        $form = Form::getForm($formInfo[0]);

        if (!$form || ($form->modal == 1 && !$submitted)) {
            new DisplayError("#404");
            exit;
        }

        if (!Form::canSubmitForm($form)) {
            new DisplayError("#Fe007");
            exit;
        }

        if ($form->predefinedSteamid == 1 && !$submitted) {
            if (!Steam::isSteamID($formInfo[2])) {
                new DisplayError("#Fe010");
                exit;
            }

            Forms::$steamidOverride = $formInfo[2];
        }

        $fields = Form::getFields($form->id);

        if (!$fields) {
            new DisplayError("#Fe006");
            exit;
        }

        if ($submitted) {
            Forms::onFormSubmit($form, $fields);
        } else {
            Controller::addCrumb(array($form->name." Form", "unit/form/".$form->id."-".$form->name));
            Controller::buildPage(array_merge(self::$basePage, array(ROOT . 'views/unit/form')), true, array(
                "form" => $form,
                "fields" => $fields
            ));
        }
    }

    public function operations($op_uid = null) {
        Controller::$subPage = "Operations";
        Controller::addCrumb(array("Operations", "unit/operations"));
        $params = array(
            'css' => array (
                'application.min.css'
            )
        );
        $views = array(ROOT . "views/unit/operations/op_view");

        if ($op_uid == null) {
            $form = Form::getForm(13);

            if ($form) {
                if (Form::canSubmitForm($form)) {
                    View::addForm($form);
                    parent::addCrumbButton("<a title=\"New Operation\" onclick=\"showModal(this)\" data-id=\"13\" data-name=\"Operation Creation\"><i class=\"fas fa-plus-circle\"></i></a>");
                }
            } else {
                new DisplayError("#500");
                exit;
            }
        } else {
            Filter::XSSFilter($op_uid);
            Filter::clearAll($op_uid);

            $op = Operations::getOperation($op_uid);

            if (!$op) {
                new DisplayError("#404");
                exit;
            }

            $params["op"] = $op;
            $params["op_log"] = Logs::getResponse($op->log_id);
        }

        Controller::buildPage(array_merge(self::$basePage, $views), true, $params);
    }

    public function trainings() {
        Controller::$subPage = "Trainings";
        Controller::addCrumb(array("Trainings", "unit/trainings"));
        Controller::buildPage(array_merge(self::$basePage, array()), true, array());
    }

    public function recruitment($itemID = -1, $itemPage = null) {
        $pages = array(ROOT . "views/unit/recruitment/recruitment");
        $params = array(
            'css' => array (
                'unit.min.css',
                'application.min.css',
                'profile.min.css'
            )
        );
        $subpages = array("Application", "Interview", "All Arms Commando Course", "Assessment");

        Controller::$subPage = "Recruitment";
        Controller::addCrumb(array("Recruitment", "unit/recruitment"));

        Session::start();

        if ($itemID == -1) {
            if (isset($_GET['last_app'])) {
                $last_app = Session::get("last_app");

                if ($last_app) {
                    header("Location: ".URL."unit/recruitment/".$last_app);
                }
            }

            $params["records"] = Records::getAllRecords();
        } else {
            if ($itemPage == null) {
                header("Location: ".URL."unit/recruitment/".$itemID."/".strtolower($subpages[0]));
            }

            $itemPage = str_replace("-", " ", $itemPage);

            $record = Records::getRecord($itemID);

            if (!$record || !in_array(ucwords($itemPage), $subpages)) {
                new DisplayError("#404");
                exit;
            }

            $member = Member::getMember($record->steamid);

            if (!$member) {
                new DisplayError("#500");
                exit;
            }

            $params["subpage"] = ucwords(strtolower($itemPage));
            
            Forms::$steamidOverride = $record->steamid;

            switch ($params["subpage"]) {
                case "Application":
                    $params["app"] = Applications::getApplication($record->app_id);
                    // $params["app_answers"] = Logs::getResponse($params["app"]->log_id);
                    $params["app_responses"] = Applications::getApplicationResponses($params["app"]->id);

                    if ($params["app"]->accepted == 0 && $params["app"]->declined == 0) {
                        foreach (array(2, 3, 4) as $form_id) {
                            $form = Form::getForm($form_id);

                            if ($form) {
                                View::addForm($form);
                            }
                        }
                    }
                    break;
                case "Interview":
                    if ($record->interview_id == -1) {
                        foreach (array(5,6) as $form_id) {
                            $form = Form::getForm($form_id);

                            if ($form) {
                                View::addForm($form);
                            }
                        }
                    };

                    $params["interviews"] = Records::getInterviews($record);
                    break;
            }

            $page_url = str_replace(" ", "-", strtolower($itemPage));
            Session::set("last_app", $itemID."/".$page_url);

            Controller::addCrumb(array($member->steamName."'s Candidate Record", "unit/recruitment/".$itemID));
            Controller::addCrumb(array($params["subpage"], "unit/recruitment/".$itemID."/".$page_url));
            
            $params["record"] = $record;
            $params["member"] = $member;
            $params["subpages"] = $subpages;
        }

        Controller::buildPage(array_merge(self::$basePage, $pages), true, $params);
    }

    public function statistics() {
        Controller::$subPage = "Statistics";
        Controller::addCrumb(array("Statistics", "unit/statistics"));
        Controller::buildPage(array_merge(self::$basePage, array()), true, array());
    }

    private static function getHistory ($steamid) {
        $target = "member";
        $dates = array(date('Y-m-d', strtotime('-1 week')), date('Y-m-d'));

        $types = Form::getFormTypes();
        $logs = null;

        if (isset($_GET['submit-type'])) {
            $target = $_GET['submit-type'];
            $dates = array($_GET['start-time'], $_GET['end-time']);
            $type = $_GET['type'];

            $logs = Logs::getHistory($steamid, $target, $dates);

            if ($type != "all") {
                foreach ($logs as $key=>$log) {
                    if ($type == "other") {
                        if (in_array($log->action, $types)) {
                            unset($logs[$key]);
                        }
                    } else {
                        if ($log->action != $type) {
                            unset($logs[$key]);
                        }
                    }
                }
            }
        }

        if ($logs == null) { $logs = Logs::getHistory($steamid, $target, $dates); }

        $history = array(
            "submit-type" => $target,
            "type" => $type,
            "dates" => $dates,
            "logs" => $logs
        );

        return $history;
    }
}