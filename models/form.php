<?php

class Form {

    public function getForm ($formID) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM forms WHERE id = :id LIMIT 1");
        $query->execute(array(":id" => $formID));
        
        if ($query->rowCount() == 0) { return false; } 
        return $query->fetch();
    }

    public function getFields ($formID) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM fields WHERE form = :id");
        $query->execute(array(":id" => $formID));
        
        if ($query->rowCount() == 0) { return false; } 

        $return = array();

        foreach ($query->fetchAll() as $result) {
            $return[$result->fieldName] = $result;
        }

        return $return;
    }

    public static function getFormTypes() {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT `action` FROM forms WHERE active = 1");
        $query->execute();
        
        if ($query->rowCount() == 0) { return false; }

        $return = array();

        foreach ($query->fetchAll() as $type) {
            if (!in_array($type->action, $return)) {
                array_push($return, $type->action);
            }
        }

        return $return;
    }

    public function canSubmitForm($form) {
        if ($form->requires_perms == 0) { return true; }
        if (!Account::isLoggedIn()) { return false; } // Must be logged in...

        $member = Member::getMember(Account::$steamid);
        if (!$member) { return false; } // Wtf??
        
        $rank = Application::$ranks[$member->rank];

        $value = "form_submit_" . $form->id;
        if (!property_exists($rank, $value)) { return false; }
        if (($rank->$value) == 0) { return false; }

        return true;
    }

    public function getLowestRankWithAccess($formID) {
        $var = Faction::$var;
        if ($var == null) { return false; }
        
        $ranks = Application::getRanks($var);
        
        usort($ranks, function ($rank1, $rank2) use ($faction) {
            return Factions::orderRanks($faction, $rank1, $rank2);
        });

        foreach ($ranks as $rank) {
            $value = "form_submit_" . $formID;
            
            if (property_exists($rank, $value)) {
                if (($rank->$value) == 1) { return $rank; }
            }
        }

        return false;
    }
}