<?php

class Actions {
    public function addMember($first_name, $last_name, $steamid, $rank, $assignment) {
        // Date / Error Checks...
        $member = Member::getMember($steamid);
        if (!$member || $member->is_in_unit == 1) { new DisplayError("#Fe005"); die(); }
        if (Member::isNameTaken($first_name, $last_name)) { new DisplayError("#Fe013"); die(); }
        if (!array_key_exists($rank, Application::$ranks) || !Assignments::getAssignment($assignment)) { new DisplayError("#500"); die(); }

        // Update Info...
        if (
            !Member::setName($steamid, $first_name, $last_name) || 
            !Member::setRank($steamid, $rank) ||
            !Member::setAssignment($steamid, $assignment)
        ) { new DisplayError("#500"); die(); }

        // Set membership...
        return Member::setMembership($steamid, 1);
    }

    public function dischargeMember($steamid, $type, $notes) {
        $member = Member::getMember($steamid);
        if (!$member || $member->is_in_unit == 0) { new DisplayError("#Fe005"); die(); }
        if (!Member::setRecordID($steamid, -1) && $member->candidate_id != -1) { new DisplayError("#500"); exit; }

        // It shouldn't matter if these fail so we don't check for it...
        Member::setName($steamid, "", "");
        Member::setRank($steamid, -1);
        Member::setAssignment($steamid, -1);
        Member::setSecondaryAssignments($steamid, array());
        Award::removeAllAwards($steamid);

        return Member::setMembership($steamid, 0);
    }

    public function setRank($steamid, $rank, $isActing, $notes) {
        $member = Member::getMember($steamid);
        if (!$member) { new DisplayError("#Fe005"); die(); }
        if (!array_key_exists($rank, Application::$ranks)) { new DisplayError("#500"); die(); }
        if (!Member::setRank($steamid, $rank, (($isActing == "Yes") ? 1 : 0))) { new DisplayError("#500"); die(); }

        return (array(
            ((object) array(
                "name" => "New Rank",
                "fieldName" => "newRnk",
                "value" => (($isActing == "Yes") ? "Acting " : "").(Application::getRankInfo($rank, $member->assignment))->name,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "Old Rank",
                "fieldName" => "oldRnk",
                "value" => (($member->is_acting == 1) ? "Acting " : "").(Application::getRankInfo($member->rank, $member->assignment))->name,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "Notes",
                "fieldName" => "notes",
                "value" => $notes,
                "if_blank" => "N/A"
            ))
        ));
    }

    public function setAssignments($steamid, $primary, $secondary = array()) {
        $member = Member::getMember($steamid);
        if (!$member) { new DisplayError("#Fe005"); die(); }
        if ($secondary == null) { $secondary = array(); }
        if (in_array($primary, $secondary)) { new DisplayError("#Fe015"); die(); }

        $assignments = array_merge(array($primary), $secondary);
        $friendly_assignments = array();

        foreach ($assignments as $assignment) {
            if ($assignment == -1) {
                $info = ((object) array(
                    "name" => "Unassigned"
                ));
            } else {
                $info = Assignments::getAssignment($assignment);
                if (!$info) { new DisplayError("#500"); die(); }
            }

            array_push($friendly_assignments, $info->name);
        }
        
        if ($member->assignment != $primary) {
            if (!Member::setAssignment($steamid, $primary)) { new DisplayError("#500"); die(); }
        }

        if ($member->sec_assignment != $secondary) {
            if (!Member::setSecondaryAssignments($steamid, $secondary)) { new DisplayError("#500"); die(); }
        }

        $primary = $friendly_assignments[0];
        unset($friendly_assignments[0]);

        return (array(
            ((object) array(
                "name" => "Primary Assignment",
                "fieldName" => "pAssignment",
                "value" => $primary,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "Secondary Assignments",
                "fieldName" => "sAssignment",
                "value" => (implode(", ", $friendly_assignments)),
                "if_blank" => ""
            ))
        ));
    }
    
    public function checkNameAvalability($steamid, $first_name, $last_name, $age, $time, $reason, $agreement) {
        if (Member::isNameTaken($first_name, $last_name)) {
            new DisplayError("#Fe013");
            die();
        }

        return true;
    }

    public function insertApplication($steamid, $log_id) {
        $app_id = Applications::submitApplication($steamid, $log_id);

        if (!$app_id) {
            return false;
        }

        $response = Applications::addApplicationResponse($app_id, $log_id);

        if (!$response) {
            return false;
        }

        $app = Applications::getApplication($app_id);
        if (!$app) { return false; }
        $answers = Logs::getResponse($app->log_id);
        if (!$answers) { return false; }
        if (!Member::setName($steamid, $answers[0]->value, $answers[1]->value)) { return false; }

        return (Records::setFieldID($steamid, "app_id", $app_id));
    }

    public function acceptApplication($steamid, $notes) {
        if (!self::setAppLogData($steamid)) { return false; }
        return Applications::acceptApplication(Forms::$customLogData["app_id"]);
    }

    public function rejectApplication($steamid, $notes) {
        if (!self::setAppLogData($steamid)) { return false; }
        if (!Records::setFieldID($steamid, "app_id")) { return false; }

        return Applications::rejectApplication(Forms::$customLogData["app_id"]);
    }

    public function setAppLogData($steamid, $notes = null) {
        if (array_key_exists("app_id", Forms::$customLogData)) { return true; }

        $candidate_id = Member::getCandidateID($steamid);
        if (!$candidate_id) { return false; }
        $record = Records::getRecord($candidate_id);
        if (!$record) { return false; }

        Forms::$customLogData = array("app_id" => $record->app_id);
        return true;
    }

    public function addApplicationNote($steamid, $log_id) {
        return Applications::addApplicationResponse(Forms::$customLogData["app_id"], $log_id);
    }

    public function passInterview($steamid) {
        $candidate_id = Member::getCandidateID($steamid);
        if (!$candidate_id) { return false; }

        $record = Records::getRecord($candidate_id);
        if (!$record) { return false; }

        $interviews = Records::getInterviews($record);
        if (!$interviews) { return false; }

        // Update Member...
        if (!Member::setRank($steamid, 1)) { return false; }

        return Records::setFieldID($steamid, "interview_id", $interviews[0]->id);
    }

    public function giveAward($steamid, $award, $notes) {
        if (!Trainings::trainingExists($award)) { return false; }

        if (Award::hasAward($steamid, $award)) {
            new DisplayError("#Fe014");
            die();
        }

        return Award::giveAward($steamid, $award);
    }

    public function createOperation ($steamid, $log_id) {
        if (!array_key_exists("operation_uid", Forms::$customLogData)) { return false; }

        $uid = Forms::$customLogData["operation_uid"];
        $title = Forms::$customLogData["operation_title"];
        $publish = Forms::$customLogData["operation_publish"];
        $datetime = Forms::$customLogData["operation_datetime"];

        return Operations::insertOperation($uid, $steamid, $title, $log_id, (($publish == "No") ? 0 : 1), $datetime);
    }

    public function formatOperationLog($uid, $title, $steamid, $date, $time, $weather, $pos, $prelims, $sit, $mission, $effort, $combat, $signals, $publish) {
        $member = Member::getMember($steamid);
        if (!$member) { new DisplayError("#Fe005"); die(); }

        Forms::$customLogData = array(
            "operation_uid" => $uid,
            "operation_title" => $title,
            "operation_publish" => $publish,
            "operation_datetime" => date('Y-m-d H:i:s', strtotime($date." ".$time))
        );

        return (array(
            ((object) array(
                "name" => "Commander Steam ID",
                "fieldName" => "commander",
                "value" => "<a target='_blank' href='".URL."unit/personnel/".$steamid."'>".$member->first_name." ".$member->last_name."</a>",
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "PRELIMS",
                "fieldName" => "finalprelims",
                "value" => "TIME: ".date('Hi\z d M Y', strtotime($date." ".$time))."</br>".
                           "WEATHER: ".$weather."</br>".
                           "POSITION: ".$pos."</br>".
                           $prelims,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "SITUATION",
                "fieldName" => "finalsituation",
                "value" => $sit,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "MISSION",
                "fieldName" => "finalmission",
                "value" => $mission,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "OUR OWN EFFORT",
                "fieldName" => "ourowneffort",
                "value" => $effort,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "COMBAT SERVICE SUPPORT",
                "fieldName" => "combatservicesupport",
                "value" => $combat,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "COMMAND & SIGNALS",
                "fieldName" => "commandsignals",
                "value" => $signals,
                "if_blank" => ""
            )),
            ((object) array(
                "name" => "Published",
                "fieldName" => "finalpublish",
                "value" => $publish,
                "if_blank" => ""
            ))
        ));
    }
}